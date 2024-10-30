<?php

namespace App\Http\Livewire;


// Trait
use App\Models\bancos;
use App\Models\cajas;
use App\Models\Category;
use App\Models\ClientesMostrador;
use App\Models\ColumnConfiguration;
use App\Models\datos_facturacion;
use App\Models\lista_precios;
use App\Models\metodo_pago;
use App\Models\pagos_facturas;
use App\Models\paises;
use App\Models\presupuestos;
use App\Models\presupuestos_detalle;
use App\Models\Product;
use App\Models\productos_lista_precios;
use App\Models\productos_stock_sucursales;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\proveedores;
use App\Models\provincias;
use App\Models\Sale;
use App\Models\seccionalmacen;
use App\Models\sucursales;
use App\Models\User;
use App\Models\variaciones;
use App\Traits\ClientesTrait;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Redirect;


class PresupuestoResumenController extends Component
{

    use ClientesTrait;

    use WithPagination;
    use WithFileUploads;

    public $name, $barcode, $cost, $price, $pago, $total_total, $accion, $casa_central_id, $estado_pago, $caja,
        $detalle_productos, $proveedor_elegido, $stock_productos_cerrar_venta, $stock, $alerts,
        $categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle, $componentName,
        $comercio_id, $data_Cart, $dateFrom, $dateTo, $Cart, $deuda, $metodo_pago_elegido, $product, $total,
        $itemsQuantity, $cantidad, $carrito, $qty, $detalle_cliente, $detalle_facturacion, $ventaId, $style2,
        $pagos2, $estado2, $estado, $listado_hojas_ruta, $suma_monto, $suma_cash, $suma_deuda, $rec, $tot,
        $usuario, $tipo_pago, $monto_ap, $recargo_total, $total_pago, $NroVenta, $id_pago, $tipos_pago,
        $detalle_venta, $detalle_compra, $dci, $details, $style, $saleId, $countDetails, $sumDetails,
        $detalle_comercio, $formato_modal, $metodo_pago_agregar_pago, $fecha_ap, $fecha_editar, $detalle_proveedor,
        $nota_interna, $observaciones, $presupuesto;

    public $query_product;
    public $sucursal_id;
    public $cliente_id;
    public $tipo_factura;
    public $lista_precios;
    public $iva_agregar;
    public $productos_variaciones_datos = [];
    public $mostrarFiltros = false;

    protected $listeners = [
        'scan-code' => 'BuscarCode',
        'deleteRow' => 'EliminarProducto'
    ];
    private $pagination = 25;

    public function MostrarFiltro()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }

    public function mount()
    {
        $this->accion = 0;
        $this->caja = cajas::select('*')->where('estado', 0)->where('user_id', Auth::user()->id)->max('id');
        $fecha_editar = Carbon::now()->format('d-m-Y');
        $this->fecha_ap = Carbon::now()->format('d-m-Y');
        $this->tipos_pago = [];
        $this->iva_agregar = 0;
        $this->style = 'none';
        $this->style2 = 'block';
        $this->detalle_compra = [];
        $this->pagos2 = [];
        $this->detalle_cliente = [];
        $this->dci = [];
        $this->detalle_productos = [];
        $this->stock_productos_cerrar_venta = [];
        $this->detalle_comercio = [];
        $this->total = [];
        $this->details = [];
        $this->pageTitle = 'Listado';
        $this->cantidad = 1;
        $this->monto_ap = 0;
        $this->tipo_pago = 1;
        $this->metodo_pago_agregar_pago = 1;
        $this->componentName = 'Productos';
        $this->metodo_pago_elegido = 'Elegir';
        $this->categoryid = 'Elegir';
        $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
        $this->dateTo = Carbon::now()->format('d-m-Y');

        $this->loadColumns();
    }

    public function loadColumns()
    {
        $columns = ColumnConfiguration::where(['user_id' => Auth::id(), 'table_name' => 'presupuestos_resumen'])
            ->pluck('is_visible', 'column_name')
            ->toArray();

        // Todas las columnas disponibles
        $allColumns = [
            'id' => true,
            'nombre_cliente' => true,
            'created_at' => true,
            'items' => true,
            'subtotal' => true,
            'descuento' => true,
            'iva' => true,
            'total' => true,
            'etiquetas' => true,
            'pago' => true,
            'deuda' => true,
            'estado_pago' => true,
            'entrega_parcial' => true,
            'actualizacion' => true
        ];

        // Fusionar columnas personalizadas con todas las columnas disponibles
        $this->columns = array_merge($allColumns, $columns);
    }

    public function showCart()
    {
        return view('livewire.ventas-fabrica.cart')
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function BuscarCode($barcode)
    {

        if (Auth::user()->comercio_id != 1) {
            $comercio_id = Auth::user()->comercio_id;
        } else {
            $comercio_id = Auth::user()->id;
        }

        // Obtener el producto

        $record = Product::where('barcode', $barcode)
            ->where('eliminado', 0)
            ->where('comercio_id', $comercio_id)
            ->first();

        // Obtener el precio


        if ($record == null || empty($record)) {

            $this->emit('scan-notfound', 'El producto no está registrado');
            $this->codigo = '';

        } else {

            $this->selected_id = $record->id;
            $this->name = $record->name;
            $this->barcode = $record->barcode;
            $this->cost = $record->cost;
            $this->price = $record->price;
            $this->stock = $record->stock;
            $this->categoryid = $record->categorias_fabrica_id;
            $this->image = null;

            $this->emit('show-modal', 'Show modal!');

            $this->codigo = '';
        }

    }


    // escuchar eventos

    public function BuscarCodeVariacion($barcode)
    {

        $this->product = explode('|-|', $barcode);

        $barcode = $this->product[0];
        $variacion = strval($this->product[1]);

        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        $this->tipo_usuario = User::find(Auth::user()->id);

        if ($this->tipo_usuario->sucursal != 1) {
            $this->casa_central_id = $comercio_id;
        } else {

            $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
            $this->casa_central_id = $this->casa_central->casa_central_id;
        }

        $product = Product::where('barcode', $barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado', 0)->first();
        $item = $product->id;

        // cambiar eso $venta = Sale::find($this->NroVenta);

        $producto_venta = presupuestos_detalle::where('presupuestos_detalles.producto_id', $product->id)
            ->where('presupuestos_detalles.referencia_variacion', $variacion)
            ->where('presupuestos_detalles.presupuesto_id', $this->NroVenta)
            ->first();

        $pdv = productos_variaciones_datos::join('products', 'products.id', 'productos_variaciones_datos.product_id')
            ->select('productos_variaciones_datos.*')
            ->where('productos_variaciones_datos.product_id', $product->id)
            ->where('productos_variaciones_datos.referencia_variacion', $variacion)
            ->where('products.eliminado', 0)
            ->first();

        // precio del producto

        $product_precio = productos_lista_precios::join('products', 'products.id', 'productos_lista_precios.product_id')
            ->where('products.id', $product->id)
            ->where('products.eliminado', 0)
            ->where('productos_lista_precios.referencia_variacion', $variacion)
            ->where('productos_lista_precios.lista_id', 0)
            ->select('productos_lista_precios.precio_lista as price')
            ->first();

        $this->iva = $this->iva_agregar * $product_precio->price;

        if ($producto_venta == [] || $producto_venta == null || empty($producto_venta)) {

            $this->items_viejo = presupuestos_detalle::create([
                'cantidad' => 1,
                'precio' => $product_precio->price,
                'recargo' => 0,
                'descuento' => 0,
                'nombre' => $product->name . " - " . $pdv->variaciones,
                'barcode' => $product->barcode,
                'comercio_id' => $product->comercio_id,
                'presupuesto_id' => $this->NroVenta,
                'producto_id' => $product->id,
                'alicuota_iva' => $this->iva_agregar,
                'iva' => $this->iva
            ]);


        } else {

            $producto_venta->update([
                'cantidad' => $producto_venta->cantidad + 1
            ]);

            // Calcula totales

            $this->details = presupuestos_detalle::where('presupuesto_id', $this->items_viejo->presupuesto_id)->get();
            //

            $suma = $this->details->sum(function ($item) {
                $suma = (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) + ((($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva);

            });

            $iva = $this->details->sum(function ($item) {
                return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * $item->recargo) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva;
            });

            $recargo = $this->details->sum(function ($item) {
                return ($item->precio * $item->cantidad * $item->recargo);
            });


            $descuento = $this->details->sum(function ($item) {
                return ($item->precio * $item->cantidad * ($item->descuento / $item->precio));
            });

            $subtotal = $this->details->sum(function ($item) {
                return ($item->precio * $item->cantidad);
            });


            $this->subtotal_venta_nuevo = $subtotal;
            $this->total_venta_nuevo = $suma;
            $this->recargo_nuevo = $recargo;
            $this->descuento_nuevo = $descuento;
            $this->iva_venta_nuevo = $iva;
            $this->items_venta_nuevo = $this->details->sum('cantidad');

            //   dd($this->total_venta_nuevo);

            $this->presupuestos = presupuestos::find($this->NroVenta);

            $this->presupuestos->update([
                'subtotal' => $this->subtotal_venta_nuevo,
                'total' => $this->total_venta_nuevo,
                'items' => $this->items_venta_nuevo,
                'descuento' => $this->descuento_nuevo,
                'recargo' => $this->recargo_nuevo,
                'iva' => $this->iva_venta_nuevo
            ]);

            $this->resetProduct();

            $this->RenderFactura($this->NroVenta);


        }

        $this->emit('variacion-elegir-hide', '');
        $this->RenderFactura($this->NroVenta);

    }

    public function resetProduct()
    {
        $this->products_s = [];
        $this->query_product = '';
        $this->RenderFactura($this->NroVenta);
    }

    public function RenderFactura($ventaId)
    {

        $this->accion = 1;
        $this->NroVenta = $ventaId;

        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;


        $this->detalle_cliente = ClientesMostrador::join('presupuestos', 'presupuestos.cliente_id', 'clientes_mostradors.id')
            ->select('clientes_mostradors.*')
            ->where('presupuestos.id', $ventaId)
            ->get();

        $this->detalle_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();


        $this->ventaId = $ventaId;

        $this->estado = "display: none;";
        $this->estado2 = "display: none;";


        /////////////// DETALLE DE VENTA /////////////////////7
        $this->dci = presupuestos_detalle::where('presupuestos_detalles.presupuesto_id', $ventaId)->get();

        $this->total = presupuestos::where('presupuestos.id', $ventaId)->get();

        $this->total_total = presupuestos::where('presupuestos.id', $ventaId)->first();

        $this->detalle_comercio = User::join('datos_facturacions', 'datos_facturacions.comercio_id', 'users.id')
            ->join('provincias', 'provincias.id', 'datos_facturacions.provincia')
            ->select('users.*', 'datos_facturacions.localidad', 'datos_facturacions.domicilio_fiscal as direccion')->where('users.id', $comercio_id)->get();


        $this->emit('modal-show', 'Show modal');


        //
    }

    public function AbrirModal($id)
    {
        $record = Product::find($id);

        $this->selected_id = $record->id;
        $this->name = $record->name;
        $this->barcode = $record->barcode;
        $this->cost = $record->cost;
        $this->price = $record->price;
        $this->stock = $record->stock;
        $this->categoryid = $record->categorias_fabrica_id;
        $this->image = null;

        $this->emit('show-modal', 'Show modal!');
    }

    public function Edit2($monto_total)
    {


        $this->monto_total = $monto_total;

        $this->emit('show-modal2', 'Show modal!');
    }

    public function MontoPago()
    {


        $this->deuda = $this->monto_total - $this->pago;

        $this->emit('show-modal2', 'Show modal!');
    }

    public function orders()
    {
        $orders = auth()->user()->processedOrders();
        $suma = 0;
        return view('products.orders', compact('orders', 'suma'));
    }

    // reset values inputs
    public function resetUI()
    {
        $this->accion = 0;
        $this->name = '';
        $this->cantidad = 1;
        $this->barcode = '';
        $this->cost = '';
        $this->price = '';
        $this->stock = '';
        $this->alerts = '';
        $this->search = '';
        $this->categoryid = 'Elegir';
        $this->image = null;
        $this->selected_id = 0;

    }


    public function MontoPagoEditarPago($value)
    {
        $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

        $this->MontoPagoEditarPago = $value;

        $this->recargo = $metodo_pago->recargo / 100;

        $this->recargo_total = $this->MontoPagoEditarPago * $this->recargo;

        $this->total_pago = $this->recargo_total + $this->MontoPagoEditarPago;

    }


    public function CreatePago($ventaId)
    {

        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        $pago_factura = pagos_facturas::create([
            'monto_compra' => $this->monto_ap,
            'caja' => $this->caja,
            'metodo_pago' => $this->metodo_pago_agregar_pago,
            'created_at' => $this->fecha_ap,
            'comercio_id' => $comercio_id,
            'id_compra' => $ventaId,
            'eliminado' => 0
        ]);

        $this->monto_ap = '';
        $this->metodo_pago_ap = 'Elegir';
        $this->caja = cajas::where('estado', 0)->where('user_id', Auth::user()->id)->max('id');

        $this->emit('pago-agregado', 'El pago fue guardado.');

        $this->emit('agregar-pago-hide', 'hide');

        $this->ActualizarEstadoDeuda($ventaId);

        $this->ResetPago();

        $this->RenderFactura($ventaId);


    }


    ////////////// FACTURA ///////////////


    public function MostrarPagos()
    {
        $this->estado = "display: block;";
        $this->estado2 = "display: none;";
    }


    function AgregarPago($id_pago)
    {

        $this->emit('cerrar-factura', 'details loaded');

        $this->emit('agregar-pago', 'details loaded');

        $this->caja_pago = cajas::select('*')->where('id', $this->caja)->get();

        $this->id_pago = $id_pago;

        $this->formato_modal = 0;

    }

    function EditPago($id_pago)
    {


        $this->CerrarFactura();

        $this->emit('agregar-pago', 'details loaded');
        $this->formato_modal = 1;
        $this->id_pago = $id_pago;


        $pagos = pagos_facturas::find($id_pago);

        $this->caja = $pagos->caja;

        $this->metodo_pago_agregar_pago = $pagos->metodo_pago;

        $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

        $this->tipo_pago = $metodo_pago->cuenta;

        $this->monto_ap = $pagos->monto_compra;

        $this->fecha_ap = Carbon::parse($pagos->created_at)->format('d-m-Y');


        $this->total_pago = $this->monto_ap;


    }

    public function CerrarFactura()
    {
        $this->emit('cerrar-factura', 'details loaded');
    }


// Mostrar el presupuesto

    public function CerrarVenta()
    {

        Cart::clear();

        if (Auth::user()->comercio_id != 1) {
            $comercio_id = Auth::user()->comercio_id;
        } else {
            $comercio_id = Auth::user()->id;
        }


        $this->detalle_productos = presupuestos_detalle::join('products', 'products.id', 'presupuestos_detalles.producto_id')
            ->where('presupuestos_detalles.presupuesto_id', $this->NroVenta)
            ->select('presupuestos_detalles.nombre', 'presupuestos_detalles.cantidad', 'products.stock')
            ->get();
        $this->presupuesto = presupuestos::where('presupuestos.id', $this->NroVenta)->first();
        $this->detalle_presupuesto_cv = presupuestos_detalle::where('presupuesto_id', $this->NroVenta)->get();

        foreach ($this->detalle_presupuesto_cv as $product) {

            $this->productos_base = productos_stock_sucursales::where('product_id', $product->producto_id)
                ->where('referencia_variacion', $product->referencia_variacion)
                ->where('eliminado', 0)
                ->first();

            if (($this->productos_base->stock < $product->cantidad) && ($product->stock_descubierto == "no")) {

                $this->stock_productos_cerrar_venta = array(
                    'nombre' => $this->productos_base->name,
                    'barcode' => $this->productos_base->barcode,
                    'stock' => $this->productos_base->stock,
                    'cantidad' => $product->cantidad
                );
            }

        }


        if ($this->stock_productos_cerrar_venta != []) {

            $this->emit('cerrar-venta', 'NO PODRA CERRAR LA VENTA YA QUE EL PRODUCTO SE ENCUENTRA SIN STOCK');

        } else {

            $i = 0;
            $cart_products = [];
            foreach ($this->detalle_presupuesto_cv as $product) {

                $this->productos_base = Product::find($product->producto_id);

                if ($product->descuento != 0) {
                    $this->descuento = ($product->descuento / $product->precio) * 100;
                } else {
                    $this->descuento = 0;
                }

                $cost = $this->GetCosto($product->producto_id, $product->referencia_variacion);
                $this->id_cart = $product->producto_id . "-" . $product->referencia_variacion . $i;
                $price_producto = $this->getProductPrecioProduct($product->producto_id, $product->referencia_variacion);

                $cart_products[] = array(
                    'id' => $this->id_cart,
                    'name' => $product->nombre,
                    'price' => $product->precio,
                    'quantity' => $product->cantidad,
                    'attributes' => array(
                        'precio_original' => $price_producto->precio_lista,
                        'pesable' => ($product->unidad_medida == 1) ? 1 : 0,
                        'product_id' => $product->producto_id,
                        'image' => '',
                        'alto' => '1',
                        'ancho' => '1',
                        'relacion_precio_iva' => $this->productos_base->relacion_precio_iva,
                        'seccionalmacen_id' => $this->productos_base->seccionalmacen_id,
                        'tipo_unidad_medida' => $product->unidad_medida,
                        'cantidad_unidad_medida' => $product->cantidad_unidad_medida,
                        'iva' => $product->alicuota_iva,
                        'cost' => $cost,
                        'descuento_promo' => null,
                        'cantidad_promo' => null,
                        'nombre_promo' => null,
                        'id_promo' => null,
                        'descuento' => $this->descuento,
                        'referencia_variacion' => $product->referencia_variacion,
                        'comercio_id' => $comercio_id,
                        'sucursal_id' => $this->productos_base->comercio_id,
                        'barcode' => $this->productos_base->barcode,
                        'stock' => $this->productos_base->stock,
                        'stock_descubierto' => $this->productos_base->stock_descubierto,
                        'added_at' => Carbon::now(),
                        'comentario' => ''
                    ));
                $i++;
            }

            Cart::add($cart_products);

            $cliente = ClientesMostrador::join('presupuestos', 'presupuestos.cliente_id', 'clientes_mostradors.id')
                ->select('clientes_mostradors.*')
                ->where('presupuestos.id', $this->NroVenta)
                ->first();


            session(['MetodoPago' => $this->presupuesto->metodo_pago]);
            session(['IdCliente' => $this->presupuesto->cliente_id]);
            session(['NombreCliente' => $cliente->nombre]);
            session(['NroVenta' => $this->NroVenta]);
            $idVenta = 'cuv' . '-' . $this->comercio_id . '-' . Carbon::now()->format('d_m_Y_H_i_s');
            session(['idVenta' => $idVenta]);

            return redirect('pos');

        }

    }

    public function GetCosto($product_id, $variacion)
    {

        $product = Product::find($product_id);

        if ($product->producto_tipo == 's') {
            $cost = $product->cost;
        } else {
            $cost = $this->setProductosVariacionesCost($variacion, $product_id);
        }

        return $cost;

    }

    public function getProductPrecioProduct($product_id, $variacion, $lista_id = 0)
    {
        return productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
            ->where('products.id', $product_id)
            ->where('productos_lista_precios.lista_id', $lista_id)
            ->where('productos_lista_precios.referencia_variacion',  $variacion)
            ->where('products.eliminado', 0)
            ->select('productos_lista_precios.precio_lista')
            ->first();
    }


    public function setProductosVariacionesCost($variacion, $product_id)
    {
        $pdv_cost = productos_variaciones_datos::where('referencia_variacion', $variacion)->where('product_id', $product_id)->orderBy('id', 'desc')->first();
        return $pdv_cost->cost;
    }

    public function EditarPedido($style)
    {

        if ($style === "none") {

            $this->style = "block";

            $this->style2 = "none";

            $this->RenderFactura($this->NroVenta);

        } else {
            $this->style = "none";

            $this->style2 = "block";

            $this->RenderFactura($this->NroVenta);

        }

    }


    public function updateDiscountPedido($id_pedido_prod, $dicount)
    {

        $this->items_viejo = presupuestos_detalle::find($id_pedido_prod);

        $this->items_viejo->update([
            'descuento' => (float)$dicount
        ]);

        $this->details = presupuestos_detalle::where('presupuesto_id', $this->items_viejo->presupuesto_id)->get();
        //

        $suma = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) + ((($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva);
        });

        $iva = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva;
        });

        $recargo = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->recargo / $item->precio));
        });


        $descuento = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->descuento / $item->precio));
        });

        $subtotal = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad);
        });


        $this->subtotal_venta_nuevo = $subtotal;
        $this->total_venta_nuevo = $suma;
        $this->recargo_nuevo = $recargo;
        $this->descuento_nuevo = $descuento;
        $this->iva_venta_nuevo = $iva;
        $this->items_venta_nuevo = $this->details->sum('cantidad');

        $this->presupuestos = presupuestos::find($this->items_viejo->presupuesto_id);

        $this->presupuestos->update([
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'items' => $this->items_venta_nuevo,
            'descuento' => $this->descuento_nuevo,
            'recargo' => $this->recargo_nuevo,
            'iva' => $this->iva_venta_nuevo
        ]);
        $this->emit('variacion-elegir', 'DESCUENTO ACTUALIZADO');
        $this->RenderFactura($this->items_viejo->presupuesto_id);


    }

    public function updatePricePedido($id_pedido_prod, $precio)
    {

        $this->items_viejo = presupuestos_detalle::find($id_pedido_prod);

        $this->items_viejo->update([
            'precio' => (float)$precio
        ]);


        $this->details = presupuestos_detalle::where('presupuesto_id', $this->items_viejo->presupuesto_id)->get();
        //

        $suma = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) + ((($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva);
        });

        $iva = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva;
        });

        $recargo = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->recargo / $item->precio));
        });


        $descuento = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->descuento / $item->precio));
        });

        $subtotal = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad);
        });


        $this->subtotal_venta_nuevo = $subtotal;
        $this->total_venta_nuevo = $suma;
        $this->recargo_nuevo = $recargo;
        $this->descuento_nuevo = $descuento;
        $this->iva_venta_nuevo = $iva;
        $this->items_venta_nuevo = $this->details->sum('cantidad');

        $this->presupuestos = presupuestos::find($this->items_viejo->presupuesto_id);

        $this->presupuestos->update([
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'items' => $this->items_venta_nuevo,
            'descuento' => $this->descuento_nuevo,
            'recargo' => $this->recargo_nuevo,
            'iva' => $this->iva_venta_nuevo
        ]);
        $this->emit('variacion-elegir', 'PRECIO ACTUALIZADO');
        $this->RenderFactura($this->items_viejo->presupuesto_id);


    }

    public function updateQtyPedido($id_pedido_prod, $cant = 1)
    {

        $this->items_viejo = presupuestos_detalle::find($id_pedido_prod);

        $this->items_viejo->update([
            'cantidad' => $cant
        ]);


        $this->details = presupuestos_detalle::where('presupuesto_id', $this->items_viejo->presupuesto_id)->get();
        //

        $suma = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) + ((($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva);
        });

        $iva = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva;
        });

        $recargo = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->recargo / $item->precio));
        });


        $descuento = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->descuento / $item->precio));
        });

        $subtotal = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad);
        });


        $this->subtotal_venta_nuevo = $subtotal;
        $this->total_venta_nuevo = $suma;
        $this->recargo_nuevo = $recargo;
        $this->descuento_nuevo = $descuento;
        $this->iva_venta_nuevo = $iva;
        $this->items_venta_nuevo = $this->details->sum('cantidad');

        $this->presupuestos = presupuestos::find($this->items_viejo->presupuesto_id);

        $this->presupuestos->update([
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'items' => $this->items_venta_nuevo,
            'descuento' => $this->descuento_nuevo,
            'recargo' => $this->recargo_nuevo,
            'iva' => $this->iva_venta_nuevo
        ]);
        $this->emit('variacion-elegir', 'CANTIDAD ACTUALIZADA.');
        $this->RenderFactura($this->items_viejo->presupuesto_id);


    }


// FUNCIONES PARA EDITAR EL PRESUPUESTO

// Actualizar cantidades

    public function EliminarProducto($id_pedido_prod, $cant = 1)
    {

        $this->items_viejo = presupuestos_detalle::find($id_pedido_prod);

        $this->items_viejo->delete();


        $this->details = presupuestos_detalle::where('presupuesto_id', $this->items_viejo->presupuesto_id)->get();
        //

        $suma = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * $item->recargo) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) + ((($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * $item->recargo) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva);
        });

        $iva = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * $item->recargo) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva;
        });

        $recargo = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * $item->recargo);
        });


        $descuento = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->descuento / $item->precio));
        });

        $subtotal = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad);
        });


        $this->subtotal_venta_nuevo = $subtotal;
        $this->total_venta_nuevo = $suma;
        $this->recargo_nuevo = $recargo;
        $this->descuento_nuevo = $descuento;
        $this->iva_venta_nuevo = $iva;
        $this->items_venta_nuevo = $this->details->sum('cantidad');

        $this->presupuestos = presupuestos::find($this->items_viejo->presupuesto_id);

        $this->presupuestos->update([
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'items' => $this->items_venta_nuevo,
            'descuento' => $this->descuento_nuevo,
            'recargo' => $this->recargo_nuevo,
            'iva' => $this->iva_venta_nuevo
        ]);

        $this->RenderFactura($this->items_viejo->presupuesto_id);


    }


// Eliminar un producto

    public function UpdateIva($id_pedido_prod, float $iva )
    {

        $this->items_viejo = presupuestos_detalle::find($id_pedido_prod);

        $this->iva_nvo = $this->items_viejo->precio * $this->items_viejo->cantidad * $iva;

        $this->items_viejo->update([
            'alicuota_iva' => $iva,
            'iva' => $this->iva_nvo
        ]);


        $this->details = presupuestos_detalle::where('presupuesto_id', $this->items_viejo->presupuesto_id)->get();
        //

        $suma = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) + ((($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva);
        });

        $iva = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva;
        });

        $recargo = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->recargo / $item->precio));
        });


        $descuento = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->descuento / $item->precio));
        });

        $subtotal = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad);
        });


        $this->subtotal_venta_nuevo = $subtotal;
        $this->total_venta_nuevo = $suma;
        $this->recargo_nuevo = $recargo;
        $this->descuento_nuevo = $descuento;
        $this->iva_venta_nuevo = $iva;
        $this->items_venta_nuevo = $this->details->sum('cantidad');

        $this->presupuestos = presupuestos::find($this->items_viejo->presupuesto_id);

        $this->presupuestos->update([
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'items' => $this->items_venta_nuevo,
            'descuento' => $this->descuento_nuevo,
            'recargo' => $this->recargo_nuevo,
            'iva' => $this->iva_venta_nuevo
        ]);

        $this->RenderFactura($this->items_viejo->presupuesto_id);


    }


// Actualizar el IVA

    public function updatedQueryProduct()
    {


        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        if ($this->sucursal_id == null)  {
            $this->sucursal_id = $comercio_id;
        }

        $this->products_s = Product::where('comercio_id', 'like', $this->sucursal_id)
            ->where('eliminado', 0)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
            })
            ->limit(5)
            ->get()
            ->toArray();

        $this->RenderFactura($this->NroVenta);


    }


// Buscar el producto en el buscador

    public function selectProduct($item)
    {

        if (Auth::user()->comercio_id != 1) {
            $comercio_id = Auth::user()->comercio_id;
        } else {
            $comercio_id = Auth::user()->id;
        }

        $this->tipo_usuario = User::find(Auth::user()->id);

        if ($this->tipo_usuario->sucursal != 1) {
            $this->casa_central_id = $comercio_id;
        } else {

            $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
            $this->casa_central_id = $this->casa_central->casa_central_id;
        }

        $presupuesto = presupuestos::find($this->NroVenta);
        $product = Product::find($item);
        $metodo_pagos = metodo_pago::where('comercio_id', $comercio_id)->where('id', $presupuesto->metodo_pago_id)->first();

        // ve si tiene variaciones o no

        if ($product->producto_tipo == "v") {

            $this->productos_variaciones_datos = productos_variaciones_datos::where('product_id', $product->id)->where('comercio_id', $this->casa_central_id)->get();

            $this->atributos = productos_variaciones::join('variaciones', 'variaciones.id', 'productos_variaciones.variacion_id')
                ->select('variaciones.nombre', 'variaciones.id as atributo_id', 'productos_variaciones.referencia_id')
                ->where('productos_variaciones.producto_id', $product->id)
                ->get();

            $this->product_id = $product->id;
            $this->barcode = $product->barcode;

            $this->variaciones = variaciones::where('variaciones.comercio_id', $this->casa_central_id)->get();

            $this->RenderFactura($this->NroVenta);
            $this->emit('variacion-elegir', $product->id);

            $this->resetProduct();

            return $this->barcode;
        }


        // Aca termina variaciones

        // precio del producto

        $product_precio = productos_lista_precios::join('products', 'products.id', 'productos_lista_precios.product_id')
            ->where('products.id', $product->id)
            ->where('products.eliminado', 0)
            ->where('productos_lista_precios.referencia_variacion', 0)
            ->where('productos_lista_precios.lista_id', 0)
            ->select('productos_lista_precios.precio_lista as price')
            ->first();


        $descuento = $presupuesto->alicuota_descuento;
        $recargo = $presupuesto->recargo;

        $this->iva = $this->iva_agregar * $product_precio->price;

        // VER SI EL PRODUCTO ESTA O NO EN EL PRESUPUESTO.

        $presupuestos_detalle = presupuestos_detalle::where('presupuesto_id', $this->NroVenta)->get();

        // SI EL PRESUPUESTO CONTIENE EL PRODUCTO

        if ($presupuestos_detalle->contains('product_id', $product->id)) {

            foreach ($presupuestos_detalle as $pd) {

                if ($pd->product_id == $product->id) {

                    $cambio = presupuestos_detalle::find($pd->id);
                    $cantidad_nueva = $cambio->cantidad + 1;

                    $cambio->update([
                        'cantidad' => $cantidad_nueva
                    ]);
                }

            }
        } else {

            // SI EL PRESUPUESTO NO CONTIENE EL PRODUCTO

            // Crea el presupuesto detalle

            $this->items_viejo = presupuestos_detalle::create([
                'cantidad' => 1,
                'precio' => $product_precio->price,
                'recargo' => $product_precio->price * 1 * $recargo,
                'descuento' => $product_precio->price * ($descuento / $product_precio->price),
                'nombre' => $product->name,
                'barcode' => $product->barcode,
                'comercio_id' => $product->comercio_id,
                'presupuesto_id' => $this->NroVenta,
                'producto_id' => $product->id,
                'alicuota_iva' => $this->iva_agregar,
                'iva' => $this->iva
            ]);

        }

        // Calcula totales
        $this->ActualizarTotales($this->items_viejo->presupuesto_id);
        $this->resetProduct();
        $this->RenderFactura($this->NroVenta);


    }

    public function ActualizarTotales($presupuesto_id)
    {

        $this->details = presupuestos_detalle::where('presupuesto_id', $presupuesto_id)->get();
        //

        $suma = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) + ((($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * ($item->recargo / $item->precio)) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva);

        });

        $iva = $this->details->sum(function ($item) {
            return (($item->precio * $item->cantidad) + ($item->precio * $item->cantidad * $item->recargo) - ($item->precio * $item->cantidad * ($item->descuento / $item->precio))) * $item->alicuota_iva;
        });

        $recargo = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * $item->recargo);
        });


        $descuento = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad * ($item->descuento / $item->precio));
        });

        $subtotal = $this->details->sum(function ($item) {
            return ($item->precio * $item->cantidad);
        });


        $this->subtotal_venta_nuevo = $subtotal;
        $this->total_venta_nuevo = $suma;
        $this->recargo_nuevo = $recargo;
        $this->descuento_nuevo = $descuento;
        $this->iva_venta_nuevo = $iva;
        $this->items_venta_nuevo = $this->details->sum('cantidad');

        //   dd($this->total_venta_nuevo);

        $this->presupuestos = presupuestos::find($this->NroVenta);

        $array = [
            'subtotal' => $this->subtotal_venta_nuevo,
            'total' => $this->total_venta_nuevo,
            'items' => $this->items_venta_nuevo,
            'descuento' => $this->descuento_nuevo,
            'recargo' => $this->recargo_nuevo,
            'iva' => $this->iva_venta_nuevo
        ];

        $this->presupuestos->update($array);
    }

    public function MailModalVerVenta($origen, $Id)
    {

        $this->origen_mail_modal = $origen;

        if ($origen == "venta") {
            $this->ventaId = $Id;
            $venta = Sale::find($Id);

            $cliente = ClientesMostrador::find($venta->cliente_id);

            $this->mail_ingresado = $cliente->email;
            $this->emit('mail-modal', '');

            $this->RenderFactura($Id);
        }

        if ($origen == "factura") {

            $this->factura_id = $Id;
            $factura = facturacion::find($Id);

            $cliente = ClientesMostrador::find($factura->cliente_id);

            $this->mail_ingresado = $cliente->email;
            $this->emit('mail-modal', '');

            $this->RenderFactura($factura->sale_id);
        }

    }

    public function EnviarMail()
    {

        if ($this->origen_mail_modal == "venta") {
            return redirect('report-email/pdf' . '/' . $this->ventaId . '/' . $this->mail_ingresado);
        }

        if ($this->origen_mail_modal == "factura") {
            return redirect('enviar-factura/pdf' . '/' . $this->factura_id . '/' . $this->mail_ingresado);
        }

    }

    public function ModalAgregarCliente()
    {

        $this->paises = paises::all();
        $this->provincias = provincias::all();
        $this->lista_precios = lista_precios::where('comercio_id', $this->casa_central_id)->get();
        $this->emit('modal-agregar-cliente', '');
        $this->RenderFactura($this->NroVenta);
    }

    public function StoreCliente()
    {
        $this->sucursal_agregar_cliente = $this->comercio_id;
        $cliente = $this->StoreClienteTrait();
        $this->selectCliente($cliente->id);
        //dd($cliente);
        $this->emit("modal-agregar-cliente-hide", "Cliente agregado");
        $this->render();
        $this->RenderFactura($this->NroVenta);
    }

    public function selectCliente($item)
    {

        //dd($item);

        $this->id_cliente_elegido = $item;

        $p = presupuestos::find($this->NroVenta);
        $p->cliente_id = $item;
        $p->save();

        $this->RenderFactura($this->NroVenta);

        $this->emit("pago-agregado", "Cliente modificado");


    }

    public function render()
    {

        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }

        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        $this->sucursales = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')->select('users.name', 'sucursales.sucursal_id')->where('sucursales.eliminado', 0)->where('casa_central_id', $comercio_id)->get();

        $this->clientes = ClientesMostrador::where('creador_id', $comercio_id)->where('eliminado', 0)->get();
        //dd($this->clientes);

        $this->tipos_pago = bancos::where('bancos.comercio_id', 'like', $comercio_id)
            ->orderBy('bancos.nombre', 'asc')->get();

        if ($this->dateFrom !== '' || $this->dateTo !== '') {
            $from = Carbon::parse($this->dateFrom)->format('Y-m-d') . ' 00:00:00';
            $to = Carbon::parse($this->dateTo)->format('Y-m-d') . ' 23:59:59';

        }

        if ($this->estado_pago !== '') {
            if ($this->estado_pago == 'Pendiente') {

                $this->estado_pago_buscar = ' compras_proveedores.deuda > 0 ';
            }
            if ($this->estado_pago == 'Pago') {
                $this->estado_pago_buscar = ' compras_proveedores.deuda = 0';
            }
        }

        $this->updateStatusBudgets();

        $compras_proveedores = presupuestos::select('presupuestos.*', 'clientes_mostradors.nombre as nombre_cliente')
            ->join('clientes_mostradors', 'clientes_mostradors.id', 'presupuestos.cliente_id')
            ->where('presupuestos.comercio_id', 'like', $comercio_id)
            ->whereBetween('presupuestos.created_at', [$from, $to]);

        if ($this->proveedor_elegido) {
            $compras_proveedores = $compras_proveedores->where('clientes_mostradors.id', $this->proveedor_elegido);
        }

        if ($this->search) {
            $compras_proveedores = $compras_proveedores->where('presupuestos.id', 'like', '%' . $this->search . '%');
        }

        if ($this->estado_pago) {
            $compras_proveedores = $compras_proveedores->whereRaw($this->estado_pago_buscar);
        }

        $compras_proveedores = $compras_proveedores->orderBy('presupuestos.id', 'desc')->paginate($this->pagination);
        $compras_proveedores_totales = presupuestos::select(presupuestos::raw('SUM(presupuestos.total) as total'), presupuestos::raw('SUM(presupuestos.items) as items'))
            ->join('clientes_mostradors', 'clientes_mostradors.id', 'presupuestos.cliente_id')
            ->where('presupuestos.comercio_id', 'like', $comercio_id)
            ->whereBetween('presupuestos.created_at', [$from, $to]);

        if ($this->proveedor_elegido) {
            $compras_proveedores_totales = $compras_proveedores_totales->where('clientes_mostradors.id', $this->proveedor_elegido);
        }

        if ($this->search) {
            $compras_proveedores_totales = $compras_proveedores_totales->where('presupuestos.id', 'like', '%' . $this->search . '%');
        }


        $compras_proveedores_totales = $compras_proveedores_totales->first();
        $this->suma_totales = $compras_proveedores_totales->total;
        $this->suma_cantidades = $compras_proveedores_totales->items;
        $this->suma_deuda = $compras_proveedores_totales->deuda;

        $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();

        return view('livewire.presupuesto-resumen.component', [
            'data' => $compras_proveedores,
            'detalle_compra' => $this->detalle_compra,
            'metodo_pago' => $metodo_pagos,
            'categories' => Category::orderBy('name', 'asc')->where('comercio_id', 'like', $comercio_id)->get(),
            'almacenes' => seccionalmacen::orderBy('nombre', 'asc')->where('comercio_id', 'like', $comercio_id)->get(),
            'prov' => proveedores::orderBy('nombre', 'asc')->where('comercio_id', 'like', $comercio_id)->get()
        ])
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    protected function redirectLogin()
    {
        return Redirect::to("login");
    }

    public function UpdateTipoComprobante($value, $nro_venta, $origen)
    {

        $s = presupuestos::find($nro_venta);
        $s->update([
            'tipo_comprobante' => $value
        ]);

        $this->emit('pago-agregado', 'El tipo de comprobante fue guardado.');

        $this->RenderFactura($nro_venta);
    }

    public function UpdateNotaInterna($value)
    {
        $sale = presupuestos::find($this->NroVenta);
        $sale->observaciones .= PHP_EOL . $value;
        $sale->save();

        $this->emit("msg", "Nota interna guardada");

    }

    public function UpdateObservaciones($value)
    {
        $sale = presupuestos::find($this->NroVenta);
        $sale->observaciones .= PHP_EOL . $value;
        $sale->save();

        $this->emit("msg", "Nota interna guardada");

    }

    private function updateStatusBudgets()
    {

        if (Auth::user()->comercio_id != 1){
            $comercio_id = Auth::user()->comercio_id;
        }
        else{
            $comercio_id = Auth::user()->id;
        }

        presupuestos::whereNotIn('estado', [1,2])
            ->where('comercio_id', '=', $comercio_id)
            ->whereRaw('DATE_ADD(created_at, INTERVAL vigencia DAY) < ?', [Carbon::now()->endOfDay()])
            ->update(['estado' => 1]);


    }

}
