<?php
namespace App\Traits;


// Modelos


use App\Models\compras_proveedores;
use App\Models\User;
use App\Models\nota_credito;
use App\Models\facturacion;
use App\Models\detalle_facturacion;
use App\Models\datos_facturacion;
use App\Models\Sale;
use App\Models\SaleDetail;

// Otros

use Afip;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;


trait FacturacionAfip {
    
public function EmitirFacturaTrait($ventaIdFactura) {

        try {
        
        $response_validation = $this->ValidateFactura($ventaIdFactura);
        if($response_validation == false){
            return;
        }
        
        
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$this->comercio_id = $comercio_id;
		$u = User::find($comercio_id);
        $this->casa_central_id = $u->casa_central_user_id;
        
		$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();

        // dd($this->datos_facturacion->cuit);
        if($this->datos_facturacion != null) {
        if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {

        //dd($this->datos_facturacion->cuit);
        
		$afip = new Afip(array('CUIT' => $this->datos_facturacion->cuit, 'production' => true));

		/**
		* Numero del punto de venta
		**/
		$punto_de_venta = $this->datos_facturacion->pto_venta;

		$this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaIdFactura);

        if($this->factura->dni != null) {
                        
                      
        ///////////// SI ES FACTURA C ///////////////
                
                
		if($this->factura->tipo_comprobante == 'C') {
		/**
		* Tipo de factura
		**/
		$tipo_de_comprobante = 11; // 11 = Factura C

		if($tipo_de_comprobante == 11) {
		$this->tipo_factura = 'C';
		}

		/**
		* Número de la ultima Factura C
		**/
		$last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_comprobante);

		/**
		* Concepto de la factura
		*
		* Opciones:
		*
		* 1 = Productos
		* 2 = Servicios
		* 3 = Productos y Servicios
		**/
		$concepto = 1;


		/**
		* Tipo de documento del comprador
		*
		* Opciones:
		*
		* 80 = CUIT
		* 86 = CUIL
		* 96 = DNI
		* 99 = Consumidor Final
		**/

        /**
        * Numero de documento del comprador (0 para consumidor final)
        **/
              if ($this->factura->cliente_id == 1) {

              $tipo_de_documento = 99;

              $numero_de_documento = 0;

              } else {

              $tipo_de_documento = 80;

              $numero_de_documento = $this->factura->dni;

              }

              /**
               * Numero de comprobante
               **/
              $numero_de_factura = $last_voucher+1;

              $this->numero_factura = $numero_de_factura;
              /**
               * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
               **/
              $fecha = date('Y-m-d');

              /**
               * Importe de la Factura
               **/


              $importe_total = $this->factura->total;
                
                  
              /**
               * Los siguientes campos solo son obligatorios para los conceptos 2 y 3
               **/
              	$fecha_servicio_desde = null;
              	$fecha_servicio_hasta = null;
              	$fecha_vencimiento_pago = null;


              $data = array(
              	'CantReg' 	=> 1, // Cantidad de facturas a registrar
              	'PtoVta' 	=> $punto_de_venta,
              	'CbteTipo' 	=> $tipo_de_comprobante,
              	'Concepto' 	=> $concepto,
              	'DocTipo' 	=> $tipo_de_documento,
              	'DocNro' 	=> $numero_de_documento,
              	'CbteDesde' => $numero_de_factura,
              	'CbteHasta' => $numero_de_factura,
              	'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
              	'FchServDesde'  => $fecha_servicio_desde,
              	'FchServHasta'  => $fecha_servicio_hasta,
              	'FchVtoPago'    => $fecha_vencimiento_pago,
              	'ImpTotal' 	=> $importe_total,
              	'ImpTotConc'=> 0, // Importe neto no gravado
              	'ImpNeto' 	=> $importe_total, // Importe neto
              	'ImpOpEx' 	=> 0, // Importe exento al IVA
              	'ImpIVA' 	=> 0, // Importe de IVA
              	'ImpTrib' 	=> 0, //Importe total de tributos
              	'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
              	'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
              );

              /**
               * Creamos la Factura
               **/
              $res = $afip->ElectronicBilling->CreateVoucher($data);

              /**
               * Mostramos por pantalla los datos de la nueva Factura
               **/
                
                // Setear la compra
                
                $this->UpdateCompra($this->factura); 
        
                $this->factura->update([
                  'cae' => $res['CAE'], //CAE asignado a la Factura
                  'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                  'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura
                  ]);
                
                $this->GuardarFacturacion($res,$data,$punto_de_venta);
                
                $this->emit('pago-actualizado', 'FACTURA GENERADA CORRECTAMENTE');


                }

        ////////////// SI ES FACTURA B ///////////////
                
                
                
        if($this->factura->tipo_comprobante == 'B' || $this->factura->tipo_comprobante == 'FB') {

        $this->tipo_factura = 'FB';

        /**
        * Numero del punto de venta
        **/

        /**
        * Tipo de factura
        **/
        $tipo_de_factura = 6; // 6 = Factura B


        $this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaIdFactura);
        /**
        * Número de la ultima Factura B
        **/
        $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_factura);

        /**
        * Concepto de la factura
        *
        * Opciones:
        *
        * 1 = Productos
        * 2 = Servicios
        * 3 = Productos y Servicios
        **/
        $concepto = 1;

        /**
        * Tipo de documento del comprador
        *
        * Opciones:
        *
        * 80 = CUIT
        * 86 = CUIL
        * 96 = DNI
        * 99 = Consumidor Final
        **/

        if ($this->factura->cliente_id == 1) {

        $tipo_de_documento = 99;

        $numero_de_documento = 0;

        } else {

        $tipo_de_documento = 80;

        $numero_de_documento = $this->factura->dni;

       }

        /**
        * Numero de documento del comprador (0 para consumidor final)
        **/

        /**
        * Numero de factura
        **/
        $numero_de_factura = $last_voucher+1;

        $this->numero_factura = $numero_de_factura;

        /**
        * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
        **/
        $fecha = date('Y-m-d');

        /**
        * Importe sujeto al IVA (sin icluir IVA)
        **/
                   
        $importe_gravado = floatval($this->factura->subtotal);
        $importe_gravado = number_format((float)$importe_gravado, 2, '.', '');
                  

        /**
        * Importe exento al IVA
        **/
        $importe_exento_iva = 0;

        /**
        * Importe de IVA
        **/
        $tipo_iva = $this->SetTipoIva($this->factura->alicuota_iva);
        
        $importe_iva = floatval(($importe_gravado * $this->factura->alicuota_iva));
        $importe_iva = number_format((float)$importe_iva, 2, '.', '');
                  

        /**
        * Los siguientes campos solo son obligatorios para los conceptos 2 y 3
        **/


        $fecha_servicio_desde = null;
        $fecha_servicio_hasta = null;
        $fecha_vencimiento_pago = null;
        
        
   
        $data = array(
        'CantReg' 	=> 1, // Cantidad de facturas a registrar
        'PtoVta' 	=> $punto_de_venta,
        'CbteTipo' 	=> $tipo_de_factura,
        'Concepto' 	=> $concepto,
        'DocTipo' 	=> $tipo_de_documento,
        'DocNro' 	=> $numero_de_documento,
        'CbteDesde' => $numero_de_factura,
        'CbteHasta' => $numero_de_factura,
        'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
        'FchServDesde'  => $fecha_servicio_desde,
        'FchServHasta'  => $fecha_servicio_hasta,
        'FchVtoPago'    => $fecha_vencimiento_pago,
        'ImpTotal' 	=> $importe_gravado + $importe_iva + $importe_exento_iva,
        'ImpTotConc'=> 0, // Importe neto no gravado
        'ImpNeto' 	=> $importe_gravado,
        'ImpOpEx' 	=> $importe_exento_iva,
        'ImpIVA' 	=> $importe_iva,
        'ImpTrib' 	=> 0, //Importe total de tributos
        'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
        'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
        'Iva' 		=> array(// Alícuotas asociadas al factura
        array(
        	'Id' 		=> $tipo_iva, // Id del tipo de IVA (5 = 21%)
        	'BaseImp' 	=> $importe_gravado,
        	'Importe' 	=> $importe_iva
        	)
         	),
        );

                  /**
                   * Creamos la Factura
                   **/
                  $res = $afip->ElectronicBilling->CreateVoucher($data);

                  /**
                   * Mostramos por pantalla los datos de la nueva Factura
                   **/
                   
                   
                // Setear la compra
                
                $this->UpdateCompra($this->factura); 
                
                $this->factura->update([
                     'cae' => $res['CAE'], //CAE asignado a la Factura
                     'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                     'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura
                     ]);
                  
                  $this->GuardarFacturacion($res,$data,$punto_de_venta);
                  
                  $this->emit('pago-actualizado', 'FACTURA GENERADA CORRECTAMENTE');


                }

                ////////////// SI ES FACTURA A ///////////////
                

                if($this->factura->tipo_comprobante == 'A') {


                      $this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaIdFactura);
                        /**
                        * Tipo de factura
                        **/
                        $tipo_de_factura = 1; // 1 = Factura A

                        $this->tipo_factura = 'FA';

                        /**
                        * Número de la ultima Factura A
                        **/
                        $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_factura);

                        /**
                        * Concepto de la factura
                        *
                        * Opciones:
                        *
                        * 1 = Productos
                        * 2 = Servicios
                        * 3 = Productos y Servicios
                        **/
                        $concepto = 1;

                        /**
                        * Tipo de documento del comprador
                        *
                        * Opciones:
                        *
                        * 80 = CUIT
                        * 86 = CUIL
                        * 96 = DNI
                        * 99 = Consumidor Final
                        **/
                        $tipo_de_documento = 80;

                        /**
                        * Numero de documento del comprador (0 para consumidor final)
                        **/
                        $numero_de_documento = $this->factura->dni;

                        /**
                        * Numero de factura
                        **/
                        $numero_de_factura = $last_voucher+1;

                        $this->numero_factura = $numero_de_factura;

                        /**
                        * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
                        **/
                        $fecha = date('Y-m-d');

                        /**
                        * Importe sujeto al IVA (sin icluir IVA)
                        **/
                      $importe_gravado = floatval($this->factura->subtotal);
                      $importe_gravado = number_format((float)$importe_gravado, 2, '.', '');
                      
    
                      /**
                       * Importe exento al IVA
                       **/
                      $importe_exento_iva = 0;
                    
                        /**
                        * Importe de IVA
                        **/
                        $tipo_iva = $this->SetTipoIva($this->factura->alicuota_iva);
                        
                        
                        $importe_iva = floatval(($importe_gravado * $this->factura->alicuota_iva));
                        $importe_iva = number_format((float)$importe_iva, 2, '.', '');
                        
                        /**
                        * Los siguientes campos solo son obligatorios para los conceptos 2 y 3
                        **/

                        $fecha_servicio_desde = null;
                        $fecha_servicio_hasta = null;
                        $fecha_vencimiento_pago = null;

                        $data = array(
                        'CantReg' 	=> 1, // Cantidad de facturas a registrar
                        'PtoVta' 	=> $punto_de_venta,
                        'CbteTipo' 	=> $tipo_de_factura,
                        'Concepto' 	=> $concepto,
                        'DocTipo' 	=> $tipo_de_documento,
                        'DocNro' 	=> $numero_de_documento,
                        'CbteDesde' => $numero_de_factura,
                        'CbteHasta' => $numero_de_factura,
                        'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
                        'FchServDesde'  => $fecha_servicio_desde,
                        'FchServHasta'  => $fecha_servicio_hasta,
                        'FchVtoPago'    => $fecha_vencimiento_pago,
                        'ImpTotal' 	=> $importe_gravado + $importe_iva + $importe_exento_iva,
                        'ImpTotConc'=> 0, // Importe neto no gravado
                        'ImpNeto' 	=> $importe_gravado,
                        'ImpOpEx' 	=> $importe_exento_iva,
                        'ImpIVA' 	=> $importe_iva,
                        'ImpTrib' 	=> 0, //Importe total de tributos
                        'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
                        'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
                        'Iva' 		=> array(// Alícuotas asociadas al factura
                          array(
                            'Id' 		=> $tipo_iva, // Id del tipo de IVA (5 = 21%)
                            'BaseImp' 	=> $importe_gravado,
                            'Importe' 	=> $importe_iva
                          )
                        ),
                        );

                        /**
                        * Creamos la Factura
                        **/
                        $res = $afip->ElectronicBilling->CreateVoucher($data);

                        /**
                        * Mostramos por pantalla los datos de la nueva Factura
                        **/
                        
                                
                        // Setear la compra
                        
                        $this->UpdateCompra($this->factura); 
                        
                        $this->factura->update([
                          'cae' => $res['CAE'], //CAE asignado a la Factura
                          'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                          'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura
                          ]);

                        $this->GuardarFacturacion($res,$data,$punto_de_venta);
                        
                        $this->emit('pago-actualizado', 'FACTURA A GENERADA CORRECTAMENTE');

            }
            
            
                //////// SI NO TIENEN TIPO DE COMPROBANTE ASOCIADO O ES CONSUMIDOR FINAL
                if($this->factura->tipo_comprobante == 'CF' || $this->factura->tipo_comprobante == '' || $this->factura->tipo_comprobante == null) {
                
                if($this->datos_facturacion->condicion_iva == 'IVA Responsable inscripto') {
                
                  $this->tipo_factura = 'FB';

                                    /**
                   * Numero del punto de venta
                   **/

                  /**
                   * Tipo de factura
                   **/
                  $tipo_de_factura = 6; // 6 = Factura B


                  $this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaIdFactura);
                  /**
                   * Número de la ultima Factura B
                   **/
                  $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_factura);

                  /**
                   * Concepto de la factura
                   *
                   * Opciones:
                   *
                   * 1 = Productos
                   * 2 = Servicios
                   * 3 = Productos y Servicios
                   **/
                  $concepto = 1;

                  /**
                   * Tipo de documento del comprador
                   *
                   * Opciones:
                   *
                   * 80 = CUIT
                   * 86 = CUIL
                   * 96 = DNI
                   * 99 = Consumidor Final
                   **/

                  if ($this->factura->cliente_id == 1) {

                  $tipo_de_documento = 99;

                  $numero_de_documento = 0;

                  } else {

                  $tipo_de_documento = 80;

                  $numero_de_documento = $this->factura->dni;

                  }

                  /**
                   * Numero de documento del comprador (0 para consumidor final)
                   **/

                  /**
                   * Numero de factura
                   **/
                  $numero_de_factura = $last_voucher+1;

                  $this->numero_factura = $numero_de_factura;

                  /**
                   * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
                   **/
                  $fecha = date('Y-m-d');

                  /**
                   * Importe sujeto al IVA (sin icluir IVA)
                   **/
                  
                  $total = floatval($this->factura->total);    
                  $total = number_format((float)$total, 2, '.', '');
                  
                  // Validar que $this->factura->alicuota_iva no sea 0
                  
                  $tipo_iva = $this->SetTipoIva($this->factura->alicuota_iva);
                        
                  $importe_gravado = floatval($this->factura->total/(1 + $this->factura->alicuota_iva));
                  $importe_gravado = number_format((float)$importe_gravado, 2, '.', '');
                  
                  
                  
                  
                  /**
                   * Importe exento al IVA
                   **/
                  $importe_exento_iva = 0;

                  /**
                   * Importe de IVA
                   **/
                   
                  $importe_iva = floatval($total - $importe_gravado);
                  $importe_iva = number_format((float)$importe_iva, 2, '.', '');
                  
                
                  /**
                   * Los siguientes campos solo son obligatorios para los conceptos 2 y 3
                   **/


                  	$fecha_servicio_desde = null;
                  	$fecha_servicio_hasta = null;
                  	$fecha_vencimiento_pago = null;


                  $data = array(
                  	'CantReg' 	=> 1, // Cantidad de facturas a registrar
                  	'PtoVta' 	=> $punto_de_venta,
                  	'CbteTipo' 	=> $tipo_de_factura,
                  	'Concepto' 	=> $concepto,
                  	'DocTipo' 	=> $tipo_de_documento,
                  	'DocNro' 	=> $numero_de_documento,
                  	'CbteDesde' => $numero_de_factura,
                  	'CbteHasta' => $numero_de_factura,
                  	'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
                  	'FchServDesde'  => $fecha_servicio_desde,
                  	'FchServHasta'  => $fecha_servicio_hasta,
                  	'FchVtoPago'    => $fecha_vencimiento_pago,
                  	'ImpTotal' 	=> $importe_gravado + $importe_iva + $importe_exento_iva,
                  	'ImpTotConc'=> 0, // Importe neto no gravado
                  	'ImpNeto' 	=> $importe_gravado,
                  	'ImpOpEx' 	=> $importe_exento_iva,
                  	'ImpIVA' 	=> $importe_iva,
                  	'ImpTrib' 	=> 0, //Importe total de tributos
                  	'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
                  	'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
                  	'Iva' 		=> array(// Alícuotas asociadas al factura
                  		array(
                  			'Id' 		=> $tipo_iva, // Id del tipo de IVA (5 = 21%)
                  			'BaseImp' 	=> $importe_gravado,
                  			'Importe' 	=> $importe_iva
                  		)
                  	),
                  );
                  
                  //dd($data);
                  
                  /**
                   * Creamos la Factura
                   **/
                  $res = $afip->ElectronicBilling->CreateVoucher($data);

                  /**
                   * Mostramos por pantalla los datos de la nueva Factura
                   **/
                   $this->factura->update([
                     'cae' => $res['CAE'], //CAE asignado a la Factura
                     'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                     'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura , 
                     'subtotal' => $importe_gravado,
                     'iva' => $importe_iva,
                     'tipo_comprobante' => 'B'
                     ]);
                    
                    $this->GuardarFacturacion($res,$data,$punto_de_venta);
                                      
                    $this->emit('pago-actualizado', 'FACTURA GENERADA CORRECTAMENTE');


                
                    

                    
                }
                
                
                
                if($this->datos_facturacion->condicion_iva == 'Monotributo') { 
							/**
							 * Tipo de factura
							 **/
							  $tipo_de_comprobante = 11; // 11 = Factura C

							  if($tipo_de_comprobante == 11) {
							  $this->tipo_factura = 'C';
							  }

							  /**
							 * Número de la ultima Factura C
							 **/
							$last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_comprobante);

							/**
							 * Concepto de la factura
							 *
							* Opciones:
							*
							* 1 = Productos
							* 2 = Servicios
							* 3 = Productos y Servicios
							**/
							$concepto = 1;


							/**
							* Tipo de documento del comprador
							*
							* Opciones:
							*
							* 80 = CUIT
							* 86 = CUIL
							* 96 = DNI
							* 99 = Consumidor Final
							**/

              /**
               * Numero de documento del comprador (0 para consumidor final)
               **/
              if ($this->factura->cliente_id == 1) {

              $tipo_de_documento = 99;

              $numero_de_documento = 0;

              } else {

              $tipo_de_documento = 80;

              $numero_de_documento = $this->factura->dni;

              }

              /**
               * Numero de comprobante
               **/
              $numero_de_factura = $last_voucher+1;

              $this->numero_factura = $numero_de_factura;
              /**
               * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
               **/
              $fecha = date('Y-m-d');

              /**
               * Importe de la Factura
               **/


              $importe_total = $this->factura->total;

              /**
               * Los siguientes campos solo son obligatorios para los conceptos 2 y 3
               **/
              	$fecha_servicio_desde = null;
              	$fecha_servicio_hasta = null;
              	$fecha_vencimiento_pago = null;


              $data = array(
              	'CantReg' 	=> 1, // Cantidad de facturas a registrar
              	'PtoVta' 	=> $punto_de_venta,
              	'CbteTipo' 	=> $tipo_de_comprobante,
              	'Concepto' 	=> $concepto,
              	'DocTipo' 	=> $tipo_de_documento,
              	'DocNro' 	=> $numero_de_documento,
              	'CbteDesde' => $numero_de_factura,
              	'CbteHasta' => $numero_de_factura,
              	'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
              	'FchServDesde'  => $fecha_servicio_desde,
              	'FchServHasta'  => $fecha_servicio_hasta,
              	'FchVtoPago'    => $fecha_vencimiento_pago,
              	'ImpTotal' 	=> $importe_total,
              	'ImpTotConc'=> 0, // Importe neto no gravado
              	'ImpNeto' 	=> $importe_total, // Importe neto
              	'ImpOpEx' 	=> 0, // Importe exento al IVA
              	'ImpIVA' 	=> 0, // Importe de IVA
              	'ImpTrib' 	=> 0, //Importe total de tributos
              	'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
              	'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
              );

              /**
               * Creamos la Factura
               **/
              $res = $afip->ElectronicBilling->CreateVoucher($data);

              /**
               * Mostramos por pantalla los datos de la nueva Factura
               **/


                $this->factura->update([
                  'cae' => $res['CAE'], //CAE asignado a la Factura
                  'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                  'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura
                  ]);
                  
                  $this->GuardarFacturacion($res,$data,$punto_de_venta);

                  $this->emit('pago-actualizado', 'FACTURA GENERADA CORRECTAMENTE');


                 }
                    
                }
                
                 
                } else {
                    
                //dd('El cliente no tiene asociado un CUIT. Adhieralo en el modulo clientes.');
                
                $this->emit('msg-factura', 'El cliente no tiene asociado un CUIT. Adhieralo en el modulo clientes.');    
                }


                            } else {

                            $this->emit('no-factura', '');

                            return;

                            }
							} else {
							 $this->emit('no-factura', '');

                            return;   
							}
                            
                            
                           

    // Código que genera la excepción
} catch (\Exception $e) {
   $this->handleAfipException($e);
   // error_log($e->getTraceAsString()); 
   // Registrar el stack trace completo en el log
   return;
}

}

public function ValidateFactura($ventaId){
       $ultima_factura = facturacion::where('sale_id',$ventaId)->orderBy('id','desc')->first();
       // si la nota de credito es nula y la factura ya esta hecha significa que no tiene que volver a facturar.
       if($ultima_factura != null){
       if($ultima_factura->nota_credito == null){
       return false;       
       } else {
       return true;       
       }
       } else {
       return true;    
       }
}

public function SetTipoIva($alicuota_iva) {

if($alicuota_iva == 0.105) {
return 4;
}

if($alicuota_iva == 0.21) {
return 5;    
}

if($alicuota_iva == 0.27) {
return 6;   
}   

}

public function UpdateCompra($venta) {

if($venta->canal_venta == "Venta a sucursales") {
                    
$compra = compras_proveedores::where('sale_casa_central',$venta->id)->first();
                
$compra->update([
'tipo_factura' => $this->tipo_factura,   
'nro_factura' => $punto_de_venta.'-'.$this->numero_factura    
]);
                
}    
}


public function GuardarFacturacion($res,$data,$punto_de_venta) {

                $factura =  facturacion::create([
                    'cae' => $res['CAE'], //CAE asignado a la Factura
                    'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                    'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura,
                    'sale_id' => $this->factura->id,
                    'subtotal' => $data['ImpNeto'],
                    'iva' =>  $data['ImpIVA'],
                    'total' =>   $data['ImpTotal'],
                    'casa_central_id' => $this->casa_central_id,
                    'comercio_id' => $this->comercio_id,
                    'cuit_vendedor' => $this->datos_facturacion->cuit,
                    'cuit_comprador' => $data['DocNro'],
                    'cliente_id' => $this->factura->cliente_id,
                    'alicuota_iva' => $this->factura->alicuota_iva,
                    'tipo_comprobante' => $this->tipo_factura,
                    'condicion_iva' => $this->datos_facturacion->condicion_iva
                    ]);
                    
                $this->GuardarDetalleDeProductosFacturados($factura);    
                
}

// Función para manejar excepciones de AFIP
private function handleAfipException(\Exception $e) {
    
    $mensajeOriginal = $e->getMessage();
    $mensajeAmigable = "Se produjo un error: " . $e->getMessage();
    
    $mensaje_error = "Se ha producido un error, intente más tarde."; // Mensaje predeterminado
    
    if (strpos($mensajeOriginal, "(600) ValidacionDeToken:") !== false) {
        $mensaje_error = "Verifica que la configuración del cuit sea el correcto en la app y en página de la AFIP";
    } elseif (strpos($mensajeOriginal, "(11002) El punto de venta no se encuentra habilitado a usar el presente WS. Ver metodo FEParamGetPtosVenta") !== false) {
        $mensaje_error = "Verifica que el punto de venta este bien configurado en la página de AFIP";
    } elseif (strpos($mensajeOriginal, "(600) ValidacionDeToken: Error al verificar hash: VerificacionDeHash: No validó la firma digital.") !== false) {
        $mensaje_error = "Error con el token de AFIP ponganse en contacto con soporte al cliente";
    } elseif (strpos($mensajeOriginal, "SOAP Fault: ns1:coe.notAuthorized Computador no autorizado a acceder al servicio") !== false) {
        $mensaje_error = "Error con el token de AFIP ponganse en contacto con soporte al cliente";
    } elseif (strpos($mensajeOriginal, "SOAP Fault: HTTP Service") !== false) {
        $mensaje_error = "No hay respuesta del servidor de AFIP, pruebe nuevamente mas tarde.";
    } elseif (strpos($mensajeOriginal, "SOAP Fault: HTTP") !== false) {
        $mensaje_error = "No hay respuesta del servidor de AFIP, pruebe nuevamente mas tarde.";
    } elseif (strpos($mensajeOriginal, "SOAP Fault: HTTP Service Unavailable") !== false) {
        $mensaje_error = "No hay respuesta del servidor de AFIP, pruebe nuevamente mas tarde.";
    } elseif (strpos($mensajeOriginal, "(501) Error interno de base de datos") !== false) {
        $mensaje_error = "Error en los servidores de AFIP. Intente mas tarde";
    }
    
    // Emitir mensaje de error
    $this->emit("msg-error-afip", $mensaje_error);
    return;
}


// Función para manejar excepciones de AFIP
private function handleAfipExceptionOld(\Exception $e) {
    
    $mensajeOriginal = $e->getMessage();
    $mensajeAmigable = "Se produjo un error: " . $e->getMessage();
    
    $mensaje_error = false;
    
    if (strpos($mensajeOriginal, "(600) ValidacionDeToken:") !== false) {
        $mensaje_error = "Verifica que la configuración del cuit sea el correcto en la app y en página de la AFIP";
    } 
    
    if (strpos($mensajeOriginal, "(11002) El punto de venta no se encuentra habilitado a usar el presente WS. Ver metodo FEParamGetPtosVenta") !== false) {
        $mensaje_error = "Verifica que el punto de venta este bien configurado en la página de AFIP";
    } 
    
    if (strpos($mensajeOriginal, "(600) ValidacionDeToken: Error al verificar hash: VerificacionDeHash: No validó la firma digital.") !== false) {
        $mensaje_error = "Error con el token de AFIP ponganse en contacto con soporte al cliente";
    } 
    
    if (strpos($mensajeOriginal, "SOAP Fault: ns1:coe.notAuthorized Computador no autorizado a acceder al servicio") !== false) {
        $mensaje_error = "Error con el token de AFIP ponganse en contacto con soporte al cliente";
    } 
        
    if (strpos($mensajeOriginal, "SOAP Fault: HTTP Service") !== false) {
        $mensaje_error = "No hay respuesta del servidor de AFIP, pruebe nuevamente mas tarde.";
    } 
    
    if (strpos($mensajeOriginal, "SOAP Fault: HTTP") !== false) {
        $mensaje_error = "No hay respuesta del servidor de AFIP, pruebe nuevamente mas tarde.";
    } 
    if (strpos($mensajeOriginal, "SOAP Fault: HTTP Service Unavailable") !== false) {
        $mensaje_error = "No hay respuesta del servidor de AFIP, pruebe nuevamente mas tarde.";
    } 
    
    if (strpos($mensajeOriginal, "(501) Error interno de base de datos") !== false) {
        $mensaje_error = "Error en los servidores de AFIP. Intente mas tarde";
    } 
    
    if (strpos($mensajeOriginal, "(501) Error interno de base de datos") !== false) {
        $mensaje_error = "Error en los servidores de AFIP. Intente mas tarde";
    } 
        
    

  if($mensaje_error != false){
  $this->emit("msg-error-afip", $mensaje_error);
  return;
  } else {
  $this->emit("msg-error-afip", $mensajeAmigable);
  return;
  }
  
}

// anular factura 

public function AnularFacturaTraitViejo($ventaId){
    
        
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();
    
    
    $this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaId);
    
    $venta = Sale::find($ventaId);
    
    $array = explode("-",$venta->nro_factura);
    
    $tipo_factura = $array[0];
    
    /* Vemos que tipo de factura es */
    if($tipo_factura == "FB") {
     $tipo_comprobante_factura = 6;
     $tipo_comprobante_nota_credito = 8;   
    }
    if($tipo_factura == "B") {
    $tipo_comprobante_factura = 6;
    $tipo_comprobante_nota_credito = 8;   
    }
    if($tipo_factura == "FA") {
    $tipo_comprobante_factura = 1;
    $tipo_comprobante_nota_credito = 3;   
    }
    if($tipo_factura == "A") {
    $tipo_comprobante_factura = 1;
     $tipo_comprobante_nota_credito = 3;   
    }
    if($tipo_factura == "FC") {
    $tipo_comprobante_factura = 11;
    $tipo_comprobante_nota_credito = 13;   
    }
    if($tipo_factura == "C") {
    $tipo_comprobante_factura = 11;
    $tipo_comprobante_nota_credito = 13;   
    }
    
    
    /* Obtenemos el punto de venta usado en esa factura */
    $pto_venta = $array[1];
    /* Obtenemos el numero de factura */
    $nro_factura = $array[2];
    
    
    if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {
    
    
    $afip = new Afip(array('CUIT' => $this->datos_facturacion->cuit, 'production' => true));
    
    /**
     * Numero del punto de venta
     **/
    $punto_de_venta = $pto_venta;
    
    /**
     * Tipo de Nota de Crédito
     **/
    $tipo_de_nota = $tipo_comprobante_nota_credito; // 8 = Nota de Crédito B
    
    /**
     * Número de la ultima Nota de Crédito B
     **/
    $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_nota);
    
    /**
     * Numero del punto de venta de la Factura 
     * asociada a la Nota de Crédito
     **/
    $punto_factura_asociada = $pto_venta;
    
    /**
     * Tipo de Factura asociada a la Nota de Crédito
     **/
    $tipo_factura_asociada = $tipo_comprobante_factura; // 6 = Factura B
    
    /**
     * Numero de Factura asociada a la Nota de Crédito
     **/
    $numero_factura_asociada = $nro_factura;
    
    /**
     * Concepto de la Nota de Crédito
     *
     * Opciones:
     *
     * 1 = Productos 
     * 2 = Servicios 
     * 3 = Productos y Servicios
     **/
    $concepto = 1;
    
    /**
     * Tipo de documento del comprador
     *
     * Opciones:
     *
     * 80 = CUIT 
     * 86 = CUIL 
     * 96 = DNI
     * 99 = Consumidor Final 
     **/
     
     
    /**
     * Numero de documento del comprador (0 para consumidor final)
     **/
     
    if ($this->factura->cliente_id == 1) {
    
     $tipo_de_documento = 99;
    
    $numero_de_documento = 0;
    
    } else {
    
    $tipo_de_documento = 80;
    
    $numero_de_documento = $this->factura->dni;
    
    }
    
    /**
     * Numero de Nota de Crédito
     **/
    $numero_de_nota = $last_voucher+1;
    
    /**
     * Fecha de la Nota de Crédito en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
     **/
    $fecha = date('Y-m-d');
    
    /**
     * Informacion de la factura
     **/
    $informacion = $afip->ElectronicBilling->GetVoucherInfo($nro_factura, $pto_venta, $tipo_comprobante_factura); 
    
    if($informacion === NULL){
        dd('La factura no existe');
    }
    else{
        
    $importe_gravado = $informacion->ImpNeto;
    $importe_iva = $informacion->ImpIVA;
    
    /**
     * Importe exento al IVA
     **/
    $importe_exento_iva = 0;
    
    /**
     * Importe de IVA
     **/
    
    $fecha_servicio_desde = null;
    $fecha_servicio_hasta = null;
    $fecha_vencimiento_pago = null;
    
    
    
    $data = array(
    'CantReg'       => 1,
    'PtoVta'        => $punto_de_venta,
    'CbteTipo'      => $tipo_de_nota, 
    'Concepto'      => $concepto,
    'DocTipo'       => $tipo_de_documento,
    'DocNro'        => $numero_de_documento,
    'CbteDesde'     => $numero_de_nota,
    'CbteHasta'     => $numero_de_nota,
    'CbteFch'       => intval(str_replace('-', '', $fecha)),
    'FchServDesde'  => $fecha_servicio_desde,
    'FchServHasta'  => $fecha_servicio_hasta,
    'FchVtoPago'    => $fecha_vencimiento_pago,
    'ImpTotal'      => $importe_gravado + $importe_iva + $importe_exento_iva,
    'ImpTotConc'    => 0,
    'ImpNeto'       => $importe_gravado,
    'ImpOpEx'       => $importe_exento_iva,
    'ImpIVA'        => $importe_iva,
    'ImpTrib'       => 0,
    'MonId'         => 'PES',
    'MonCotiz'      => 1,
    'CbtesAsoc'     => array(
        array(
            'Tipo'      => $tipo_factura_asociada,
            'PtoVta'    => $punto_factura_asociada,
            'Nro'       => $numero_factura_asociada,
        )
    ),
);

// Verificar si $tipo_factura es igual a "FC" antes de incluir la sección 'Iva'
if ($tipo_factura != "FC" && $tipo_factura != "C") {
    
    $tipo_iva = $this->SetTipoIva($this->factura->alicuota_iva);
    
    $data['Iva'] = array(
        array(
            'Id'        => $tipo_iva,
            'BaseImp'   => $importe_gravado,
            'Importe'   => $importe_iva 
        )
    );
}
    
    /** 
     * Creamos la Nota de Crédito 
     **/
    $res = $afip->ElectronicBilling->CreateVoucher($data);
    
    /**
     * Mostramos por pantalla los datos de la nueva Nota de Crédito 
     **/
    
    
       nota_credito::create([
          'cae' => $res['CAE'], //CAE asignado a la Factura
          'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
          'nro_nota_credito' => "NC-".$tipo_factura."-".$numero_de_nota,
          'nro_factura' => $venta->nro_factura,
          'venta_id' => $ventaId,
          'comercio_id' => $venta->comercio_id 
          ]);
    
     $this->emit('pago-actualizado', 'NOTA DE CREDITO GENERADA CORRECTAMENTE');
    
    }
    }
    
}


public function AnularFacturaTrait($factura_id){
    
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();

    $this->factura = facturacion::join('clientes_mostradors','clientes_mostradors.id','facturacions.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','facturacions.*')->find($factura_id);
//    $this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaId);
    
    $venta = Sale::find($this->factura->sale_id);
    $ventaId = $this->factura->sale_id;
    
    $array = explode("-",$venta->nro_factura);
    
    $tipo_factura = $array[0];
    
    /* Vemos que tipo de factura es */
    if($tipo_factura == "FB") {
     $tipo_comprobante_factura = 6;
     $tipo_comprobante_nota_credito = 8;   
    }
    if($tipo_factura == "B") {
    $tipo_comprobante_factura = 6;
    $tipo_comprobante_nota_credito = 8;   
    }
    if($tipo_factura == "FA") {
    $tipo_comprobante_factura = 1;
    $tipo_comprobante_nota_credito = 3;   
    }
    if($tipo_factura == "A") {
    $tipo_comprobante_factura = 1;
     $tipo_comprobante_nota_credito = 3;   
    }
    if($tipo_factura == "FC") {
    $tipo_comprobante_factura = 11;
    $tipo_comprobante_nota_credito = 13;   
    }
    if($tipo_factura == "C") {
    $tipo_comprobante_factura = 11;
    $tipo_comprobante_nota_credito = 13;   
    }
    
    
    /* Obtenemos el punto de venta usado en esa factura */
    $pto_venta = $array[1];
    /* Obtenemos el numero de factura */
    $nro_factura = $array[2];
    
    
    if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {
    
    
    $afip = new Afip(array('CUIT' => $this->datos_facturacion->cuit, 'production' => true));
    
    /**
     * Numero del punto de venta
     **/
    $punto_de_venta = $pto_venta;
    
    /**
     * Tipo de Nota de Crédito
     **/
    $tipo_de_nota = $tipo_comprobante_nota_credito; // 8 = Nota de Crédito B
    
    /**
     * Número de la ultima Nota de Crédito B
     **/
    $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_nota);
    
    /**
     * Numero del punto de venta de la Factura 
     * asociada a la Nota de Crédito
     **/
    $punto_factura_asociada = $pto_venta;
    
    /**
     * Tipo de Factura asociada a la Nota de Crédito
     **/
    $tipo_factura_asociada = $tipo_comprobante_factura; // 6 = Factura B
    
    /**
     * Numero de Factura asociada a la Nota de Crédito
     **/
    $numero_factura_asociada = $nro_factura;
    
    /**
     * Concepto de la Nota de Crédito
     *
     * Opciones:
     *
     * 1 = Productos 
     * 2 = Servicios 
     * 3 = Productos y Servicios
     **/
    $concepto = 1;
    
    /**
     * Tipo de documento del comprador
     *
     * Opciones:
     *
     * 80 = CUIT 
     * 86 = CUIL 
     * 96 = DNI
     * 99 = Consumidor Final 
     **/
     
     
    /**
     * Numero de documento del comprador (0 para consumidor final)
     **/
     
    if ($this->factura->cliente_id == 1) {
    
     $tipo_de_documento = 99;
    
    $numero_de_documento = 0;
    
    } else {
    
    $tipo_de_documento = 80;
    
    $numero_de_documento = $this->factura->dni;
    
    }
    
    /**
     * Numero de Nota de Crédito
     **/
    $numero_de_nota = $last_voucher+1;
    
    /**
     * Fecha de la Nota de Crédito en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
     **/
    $fecha = date('Y-m-d');
    
    /**
     * Informacion de la factura
     **/
    $informacion = $afip->ElectronicBilling->GetVoucherInfo($nro_factura, $pto_venta, $tipo_comprobante_factura); 
    
    if($informacion === NULL){
        dd('La factura no existe');
    }
    else{
        
    $importe_gravado = $informacion->ImpNeto;
    $importe_iva = $informacion->ImpIVA;
    
    /**
     * Importe exento al IVA
     **/
    $importe_exento_iva = 0;
    
    /**
     * Importe de IVA
     **/
    
    $fecha_servicio_desde = null;
    $fecha_servicio_hasta = null;
    $fecha_vencimiento_pago = null;
    
    
    
    $data = array(
    'CantReg'       => 1,
    'PtoVta'        => $punto_de_venta,
    'CbteTipo'      => $tipo_de_nota, 
    'Concepto'      => $concepto,
    'DocTipo'       => $tipo_de_documento,
    'DocNro'        => $numero_de_documento,
    'CbteDesde'     => $numero_de_nota,
    'CbteHasta'     => $numero_de_nota,
    'CbteFch'       => intval(str_replace('-', '', $fecha)),
    'FchServDesde'  => $fecha_servicio_desde,
    'FchServHasta'  => $fecha_servicio_hasta,
    'FchVtoPago'    => $fecha_vencimiento_pago,
    'ImpTotal'      => $importe_gravado + $importe_iva + $importe_exento_iva,
    'ImpTotConc'    => 0,
    'ImpNeto'       => $importe_gravado,
    'ImpOpEx'       => $importe_exento_iva,
    'ImpIVA'        => $importe_iva,
    'ImpTrib'       => 0,
    'MonId'         => 'PES',
    'MonCotiz'      => 1,
    'CbtesAsoc'     => array(
        array(
            'Tipo'      => $tipo_factura_asociada,
            'PtoVta'    => $punto_factura_asociada,
            'Nro'       => $numero_factura_asociada,
        )
    ),
);

    // Verificar si $tipo_factura es igual a "FC" antes de incluir la sección 'Iva'
    if ($tipo_factura != "FC" && $tipo_factura != "C") {
        
        $tipo_iva = $this->SetTipoIva($this->factura->alicuota_iva);
        
        $data['Iva'] = array(
            array(
                'Id'        => $tipo_iva,
                'BaseImp'   => $importe_gravado,
                'Importe'   => $importe_iva 
            )
        );
    }
        
    /** 
     * Creamos la Nota de Crédito 
     **/
    $res = $afip->ElectronicBilling->CreateVoucher($data);
    
    /**
     * Mostramos por pantalla los datos de la nueva Nota de Crédito 
     **/
    
    
       nota_credito::create([
          'cae' => $res['CAE'], //CAE asignado a la Factura
          'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
          'nro_nota_credito' => "NC-".$tipo_factura."-".$numero_de_nota,
          'nro_factura' => $venta->nro_factura,
          'venta_id' => $ventaId,
          'comercio_id' => $venta->comercio_id 
          ]);
          
      $facturacion = facturacion::find($this->factura->id);
      $facturacion->nota_credito = "NC-".$tipo_factura."-".$numero_de_nota;
      $facturacion->save();
    
     $this->emit('pago-actualizado', 'NOTA DE CREDITO GENERADA CORRECTAMENTE');
    
    }
    }
    
}

public function GuardarDetalleDeProductosFacturados($factura){
    
    $sale_detail = SaleDetail::where('sale_id',$factura->sale_id)->get();    

    foreach($sale_detail as $sd){
        
    detalle_facturacion::create([
        'comercio_id'=> $sd->comercio_id,
        'cliente_id'=> $sd->cliente_id,
        'price'=> $sd->price,
        'quantity'=> $sd->quantity,
        'recargo'=> $sd->recargo,
        'descuento'=> $sd-> descuento,
        'iva'=> $sd-> iva,
        'iva_total'=> $sd-> iva_total,
        'product_id'=> $sd-> product_id,
        'product_barcode'=> $sd-> product_barcode,
        'referencia_variacion'=> $sd->referencia_variacion,
        'product_name'=> $sd->product_name,
        'relacion_precio_iva'=> $sd->relacion_precio_iva,
        'factura_id'=> $factura->id,
        'nro_factura'=> $factura->nro_factura,
        'sale_id'  => $factura->sale_id  
    ]);    
    
        
    }    
}

}
