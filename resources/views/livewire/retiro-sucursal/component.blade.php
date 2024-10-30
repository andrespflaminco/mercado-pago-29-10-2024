<div>	                

                  @include('livewire.retiro-sucursal.configurar-columnas')
                  @include('livewire.reports-nuevo.mail-modal')

                  @include('livewire.reports-nuevo.elegir-cuit')

				 
                  @include('livewire.reports-nuevo.form-imprimir')
                  @include('livewire.reports-nuevo.condicion-iva')
                  @include('livewire.reports-nuevo.variaciones')
                  @include('livewire.reports-nuevo.abrir-caja')
                  @include('livewire.gastos.estado-pedido-pos')
                  @include('livewire.reports-nuevo.agregar-pago')
                  @include('livewire.reports-nuevo.form-hoja-ruta')
                  @include('livewire.reports-nuevo.form-hoja-ruta-nueva')
                  @include('livewire.factura.form-pagos')
                  @include('livewire.reports-nuevo.actualizar-estado-venta')
                  @include('livewire.reports-nuevo.sales-detail3')
                  @include('livewire.pos_nuevo.partials.descuentos')
                  @include('common.form-cliente')
                  
                  
    
    @if($NroVenta == 0)
    <div class="page-header">
					<div class="page-title">
							<h4>Retiro por sucursal</h4>
							<h6>Busca una venta por el codigo de retiro</h6>
						</div>
					
					</div>

	<!-- /product list -->
	<div class="card">
	        
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
										<a style="font-size:14px !important; padding:5px !important; background: #FF9F43 !important; width: auto !important; color: white;" wire:click="Filtros('{{$MostrarOcultar}}')" class="btn btn-filter" >
											<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
											<div style="margin-left: 5px; margin-right: 5px; font-size: 14px !important;">
											<b>Filtros</b> 
											</div>
										</a>
								</div>
								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li></li>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							
							<div class="card mb-3"  style="border: solid 1px #eee; padding: 20px; display:{{$MostrarOcultar}};">
                                              <div class="card-body">
                                                <div class="row">
                                                  
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Numero de venta</label>
                                                    <input type="text" wire:model="numero_venta_filtro">
                                                  </div>
                                                  </div>
                                                  
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Codigo de retiro</label>
                                                    <input type="text" wire:model="codigo_retiro_filtro">
                                                  </div>
                                                  </div>
                                                  
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Cliente</label>
                                                    <input type="text" wire:model="cliente_id_filtro">
                                                  </div>
                                                  </div>
                            
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Fecha desde</label>
                                                    <input type="date" wire:model="dateFrom" class="form-control">
                            
                                                  </div>
                                                  </div>
                            
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Fecha hasta</label>
                                                    <input type="date" wire:model="dateTo" class="form-control">
                            
                                                  </div>
                                                  </div>
                            
                                                  
                                               </div>
                            
                                               </div>
                            
                            
                            
                            
                            
                            
                                                  </div>
                                                  
   
                        <!--TABLAE-->
                         @if(session('status'))
                         <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                         @endif

							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
                                        <th ></th>
                                        <th wire:click="OrdenarColumna('nro_venta')" @if(!$columns['nro_venta']) style="display: none;" @endif>NRO VENTA 
                                            @if ($columnaOrden == 'nro_venta')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th> 
                                        <th wire:click="OrdenarColumna('codigo_retiro')" @if(!$columns['codigo_retiro']) style="display: none;" @endif>CODIGO RETIRO 
                                            @if ($columnaOrden == 'codigo_retiro')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('status')" @if(!$columns['status']) style="display: none;" @endif> ESTADO
                                            @if ($columnaOrden == 'status')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('created_at')" @if(!$columns['created_at']) style="display: none;" @endif>FECHA
                                            @if ($columnaOrden == 'created_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('nombre_cliente')" @if(!$columns['nombre_cliente']) style="display: none;" @endif>CLIENTE
                                            @if ($columnaOrden == 'nombre_cliente')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('subtotal')" @if(!$columns['subtotal']) style="display: none;" @endif>SUBTOTAL
                                            @if ($columnaOrden == 'subtotal')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('descuento_promo')" @if(!$columns['descuento_promo']) style="display: none;" @endif>DESCUENTO PROMOCIONES
                                            @if ($columnaOrden == 'descuento_promo')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('descuento')" @if(!$columns['descuento']) style="display: none;" @endif>DESCUENTO GRAL
                                            @if ($columnaOrden == 'descuento')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('recargo')" @if(!$columns['recargo']) style="display: none;" @endif>RECARGO
                                            @if ($columnaOrden == 'recargo')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('iva')" @if(!$columns['iva']) style="display: none;" @endif>IVA
                                            @if ($columnaOrden == 'iva')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('total')" @if(!$columns['total']) style="display: none;" @endif>TOTAL
                                            @if ($columnaOrden == 'total')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('nombre_banco')" @if(!$columns['nombre_banco']) style="display: none;" @endif>FORMA DE PAGO
                                            @if ($columnaOrden == 'nombre_banco')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('deuda')" @if(!$columns['deuda']) style="display: none;" @endif>A COBRAR
                                            @if ($columnaOrden == 'deuda')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                      
                                        <th wire:click="OrdenarColumna('nro_factura')" @if(!$columns['nro_factura']) style="display: none;" @endif>FACTURA
                                            @if ($columnaOrden == 'nro_factura')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>

                                        <th wire:click="OrdenarColumna('nota_interna')" @if(!$columns['nota_interna']) style="display: none;" @endif>NOTA INTERNA
                                            @if ($columnaOrden == 'nota_interna')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('observaciones')" @if(!$columns['observaciones']) style="display: none;" @endif>OBSERVACIONES
                                            @if ($columnaOrden == 'observaciones')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        
										</tr>
									</thead>
									<tbody>
                                    @if($data_reportes->count() < 1)
                                    <tr><td colspan="7"><h5>Sin Resultados</h5></td></tr>
                                    @endif
                                    @foreach($data_reportes as $d)
                                    <?php $sum += $d->total; ?>
                                    <tr>
                                        <td>
                                            @if($estado_filtro == 0)
                                              <div class="btn-group mb-1 mr-1">
                                                    <button id="dropdown-toggle" class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div style="z-index:99999999999999999 !important;" class="dropdown-menu">
                 
                                                    <a href="javascript:void(0);" wire:click.prevent="RenderFactura({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Ver </a>
                                                    <a href="{{ url('report-remito/pdf' . '/' . $d->id) }}" target="_blank"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Imprimir Comprobante de entrega </a>
                                                    
                                                    @if($d->status != "Entregado")
                                                    <a href="javascript:void(0);" wire:click.prevent="MarcarComoEntregado({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Marcar como entregada </a>
                                                    @endif
                                                                                                        
                                                    @if($d->status == "Entregado")
                                                    <a href="javascript:void(0);" wire:click.prevent="MarcarComoDevuelto({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Marcar como pendiente de entrega en sucursal </a>
                                                    @endif
                                                    
                                                    <a hidden href="javascript:void(0);" wire:click.prevent="SetCompraSucursal({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Setear compra </a>
                                                    @if(2 < Auth::user()->plan)
                                                      <a hidden href="javascript:void(0);" wire:click="MailModal({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>Enviar por mail</a>
                                                    @endif
                                                    
                                                    
                                                    <a hidden href="{{ url('report-factura/pdf' . '/' . $d->id) }}" target="_blank"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Imprimir </a>
                                                    

                                                  </div>
                                              </div>
                                            @else
                                            <!----- RESTAURAR VENTA ----->
                                                <a href="javascript:void(0);" onclick="RestaurarVenta({{$d->id}})" class="btn btn-dark text-white"> Restaurar </a>

                                            @endif
                                        </td>
                                        
                                        <td @if(!$columns['nro_venta']) style="display: none;" @endif>{{$d->nro_venta}}</td>
                                        <td @if(!$columns['codigo_retiro']) style="display: none;" @endif>{{$d->codigo_retiro}}</td>
                                        <td @if(!$columns['nro_venta']) style="display: none;" @endif>
                                            {{$d->status}}
                                        </td>
                                        <td @if(!$columns['created_at']) style="display: none;" @endif>
                                           
                                                {{\Carbon\Carbon::parse($d->created_at)->format('d/m/Y')}}
                                    
                                        </td>
                                        
                                        <td @if(!$columns['nombre_cliente']) style="display: none;" @endif>{{$d->nombre_cliente}}
                                         @foreach($ecommerce_envios as $ee)
                                        
                                        @if($ee->sale_id == $d->id)
                                        - {{$ee->nombre_destinatario}}
                                
                                        @endif
                                        
                                        @endforeach
                                        </td>
                                        <td @if(!$columns['subtotal']) style="display: none;" @endif>
                                            ${{number_format($d->subtotal,2)}}
                                        </td>  
                                        <td @if(!$columns['descuento_promo']) style="display: none;" @endif>
                                             @if($d->descuento_promo != null)
                                             ${{number_format($d->descuento_promo,2)}}
                                             @else
                                             -
                                             @endif
                                        </td>
                                        <td @if(!$columns['descuento']) style="display: none;" @endif>
                                            ${{number_format($d->descuento,2)}}
                                        </td>  
                                        <td @if(!$columns['recargo']) style="display: none;" @endif>
                                            ${{number_format($d->recargo,2)}}
                                        </td>  
                                     
                                        
                                        <td @if(!$columns['iva']) style="display: none;" @endif>
                                             ${{number_format($d->iva,2)}}
                                        </td>
                                        <td @if(!$columns['total']) style="display: none;" @endif>
                                          <b>$
                                          {{number_format($d->subtotal-$d->descuento-$d->descuento_promo+$d->recargo+$d->iva,2)}}</b></td>
                                        <td @if(!$columns['nombre_banco']) style="display: none;" @endif>{{$d->nombre_banco}} - {{$d->nombre_metodo_pago}}</td>
                                        <td @if(!$columns['deuda']) style="display: none;" @endif>
                                        @if($d->deuda != null && $d->deuda != 0)
                                        $ {{$d->deuda}}
                                        @endif
                                        </td>

                                        <td @if(!$columns['nro_factura']) style="display: none;" @endif>
                                        @if($d->nro_nota_credito)
                                         
                                          @if($d->nro_factura)
                                          <p style="text-decoration:line-through;">
                                          <?php
                                          $porciones = explode("-", $d->nro_factura);
                                          $tipo_factura = $porciones[0]; // porción1
                                          $pto_venta = $porciones[1]; // porción2
                                          $nro_factura_ = $porciones[2]; // porción2
                                          echo $tipo_factura."-".str_pad($pto_venta, 3, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 5, "0", STR_PAD_LEFT); ?>
                                          </p>
                                          @else
                                          -
                                          @endif
                                            
                                         <p style="color: red;"> {{$d->nro_nota_credito}} </p>
                                         
                                          @else
                                            @if($d->nro_factura)
                                          <?php
                                          $porciones = explode("-", $d->nro_factura);
                                          $tipo_factura = $porciones[0]; // porción1
                                          $pto_venta = $porciones[1]; // porción2
                                          $nro_factura_ = $porciones[2]; // porción2
                                          echo $tipo_factura."-".str_pad($pto_venta, 3, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 5, "0", STR_PAD_LEFT); ?>
                                          @else
                                          -
                                          @endif
                                        
                                          @endif
                                        
                                        </td>

                                        <td @if(!$columns['nota_interna']) style="display: none;" @endif>
                                           {{$d->nota_interna}}
                                        </td>
                                        <td @if(!$columns['observaciones']) style="display: none;" @endif>
                                           {{$d->observaciones}}
                                        </td>
                                        
                                    </tr>
                                    @endforeach

									</tbody>
								</table>
								
							</div>
							<br><br>
							{{$data_reportes->links()}}
						</div>
					</div>

	
	@else
	@include('livewire.retiro-sucursal.ver-venta')
	@endif


                  
</div>


<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', function () {
         $('.tagging').select2({
                        tags: true
                    });           
        });
        
    });
        
    document.addEventListener('DOMContentLoaded', function(){

        $('.tagging').select2({
                        tags: true
                    });

                $('#select2-dropdown').on('change', function(e) {
                  var id = $('#select2-dropdown').select2('val');
                  var name = $('#select2-dropdown option:selected').text();
                  @this.set('clientesSelectedName', name);
                  @this.set('ClienteSeleccionado', ''+id);
                  @this.emit('locationUsersSelected', $('#select2-dropdown').select2('val'));
                });

                $('#select2-dropdown2').on('change', function(e) {
                  var id = $('#select2-dropdown2').select2('val');
                  var name = $('#select2-dropdown2 option:selected').text();
                  @this.set('UsuarioSelectedName', name);
                  @this.set('usuarioSeleccionado', ''+id);
                  @this.emit('UsuarioSelected', $('#select2-dropdown2').select2('val'));
                });

                $('#select2-dropdown3').on('change', function(e) {
                  var id = $('#select2-dropdown3').select2('val');
                  var name = $('#select2-dropdown3 option:selected').text();
                  @this.set('EstadoSelectedName', name);
                  @this.set('EstadoSeleccionado', ''+id);
                  @this.emit('EstadoSelected', $('#select2-dropdown3').select2('val'));
                });


                $('#select2-dropdown-metodo-pago').on('change', function(e) {
                  var id = $('#select2-dropdown-metodo-pago').select2('val');
                  var name = $('#select2-dropdown-metodo-pago option:selected').text();
                  @this.set('MetodoPagoSelectedName', name);
                  @this.set('MetodoPagoSeleccionado', ''+id);
                  @this.emit('MetodoPagoSelected', $('#select2-dropdown-metodo-pago').select2('val'));
                });




        //eventos

        //2-5-2024
        window.livewire.on('listado-cuit-show', Msg =>{
            $('#ElegirCuit').modal('show')
        })        
        
        window.livewire.on('listado-cuit-hide', Msg =>{
            $('#ElegirCuit').modal('hide')
        })        
        
                
        window.livewire.on('show-modal-descuentos', Msg =>{
            $('#Descuentos').modal('show')
        })
        
        window.livewire.on('hide-modal-descuentos', Msg =>{
            $('#Descuentos').modal('hide')
        })
        
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })

        window.livewire.on('agregar-pago', Msg =>{
            $('#AgregarPago').modal('show')
        })

        window.livewire.on('agregar-pago-hide', Msg =>{
            $('#AgregarPago').modal('hide')
        })
        
       window.livewire.on('abrir-caja', Msg =>{
            $('#AbrirCaja').modal('show')
        })
        
        window.livewire.on('abrir-caja-hide', Msg =>{
            $('#AbrirCaja').modal('hide')
        })

        window.livewire.on('agregar-iva', Msg =>{
            $('#AgregarIva').modal('show')
        })

        window.livewire.on('agregar-iva-hide', Msg =>{
            $('#AgregarIva').modal('hide')
        })

        window.livewire.on('show-modal-actualizar-estado', Msg =>{
            $('#ActualizarEstadoVenta').modal('show')
        })

        window.livewire.on('hide-modal-actualizar-estado', Msg =>{
            $('#ActualizarEstadoVenta').modal('hide')
        })

        window.livewire.on('show-modal3', Msg =>{
            $('#modalDetails3').modal('show')
        })

        window.livewire.on('hide-modal3', Msg =>{
            $('#modalDetails3').modal('hide')
        })

        window.livewire.on('cerrar-factura', Msg =>{
            $('#theModal1').modal('hide')
        })

        window.livewire.on('mail-modal', Msg =>{
             $('#MailModal').modal('show')
        })


        window.livewire.on('cerrar-modal-mail', Msg =>{
             $('#MailModal').modal('hide')
        })

        window.livewire.on('msg', Msg =>{
            Noty(Msg)
        })


        window.livewire.on('abrir-hr-nueva', Msg =>{
            $('#theModal').modal('show')
        })

        window.livewire.on('modal-hr-hide', Msg =>{
            $('#theModal').modal('hide')
        })

        window.livewire.on('hr-added', Msg => {
          noty(Msg)
        })
        
        window.livewire.on('msg', Msg => {
          noty(Msg)
        })

        window.livewire.on('abrir-imprimir', Msg =>{
          $('#FormImprimir').modal('show')
        })
        
       window.livewire.on('editar-cliente', Msg =>{
          $('#EditarCliente').modal('show')
        })
        
        
       window.livewire.on('editar-cliente-hide', Msg =>{
          $('#EditarCliente').modal('hide')
        })


        window.livewire.on('modal-estado', Msg =>{
            $('#modalDetails-estado-pedido').modal('show')
        })

        window.livewire.on('modal-estado-hide', Msg =>{
            $('#modalDetails-estado-pedido').modal('hide')
        })
        
        
		window.livewire.on('variacion-elegir', Msg => {
			$('#Variaciones').modal('show')
		})

		window.livewire.on('variacion-elegir-hide', Msg => {
			$('#Variaciones').modal('hide')
		})
		
		window.livewire.on('modal-agregar-cliente-hide', Msg => {
			$('#ModalAgregarCliente').modal('hide')
		})
		
		


        window.livewire.on('hr-asignada', Msg => {
          noty(Msg)
        })

        window.livewire.on('pago-agregado', Msg => {
          noty(Msg)
        })

        window.livewire.on('pago-actualizado', Msg => {
          noty(Msg)
        })

        window.livewire.on('pago-eliminado', Msg => {
          noty(Msg)
        })

        window.livewire.on('no-stock', Msg => {
    			noty(Msg, 2)
    		})
    		
    	window.livewire.on('elegir-condicion-iva', Msg => {
    		$('#CondicionIva').modal('show')
		})
        
        window.livewire.on('elegir-condicion-iva-hide', Msg => {
    		$('#CondicionIva').modal('hide')
		})
        
        
        var total = $('#suma_totales').val();
        $('#ver_totales').html('Ventas: '+total);

    window.livewire.on('modal-show', msg => {
      $('#theModal1').modal('show')
    });
    
    
	window.livewire.on('volver-stock', variable => {
	 var porciones = variable.split('-');
	 var id = porciones[0];
	 var stock = porciones[1];
	$("#qty"+id).val(stock);
	})
	
		window.livewire.on('msg-factura', id => {
				swal({
				title: 'IMPORTATE',
				text: 'EL CLIENTE NO TIENE UN CUIT ASOCIADO. CONFIGURELO POR FAVOR',
				showCancelButton: true,
				cancelButtonText: 'CERRAR',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'IR A CONFIGURAR'
				}).then(function(result) {
				if (result.value) {
			    window.location.href = '/clientes';
				swal.close()
				}

				})

			})
			
			

    	window.livewire.on('confirmar-cambiar-iva', data => {
				const iva = data.iva;
				swal({
				title: 'ATENCION',
				text: 'DEBE ASIGNAR UN NUEVO VALOR AL IVA',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
				})
			})
			

    	window.livewire.on('cancelar-pagos-mensaje', id => {
				swal({
				title: 'CAMBIAR ESTADO',
				text: '¿DESEA ELIMINAR LOS PAGOS ASOCIADOS AL PEDIDO # '+id+' ?',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
				}).then(function(result) {
				if (result.value) {
				window.livewire.emit('cancelar-pagos', id)
				swal.close()
				}

				})

			})


			    window.livewire.on('no-factura', id => {
				swal({
				title: 'IMPORTATE',
				text: 'DEBE CONFIGURAR SUS DATOS FISCALES ANTES DE FACTURAR',
				showCancelButton: true,
				cancelButtonText: 'CERRAR',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'IR A CONFIGURAR'
				}).then(function(result) {
				if (result.value) {
			    window.location.href = '/mi-comercio';
				swal.close()
				}

				})

			})



    function rePrint(saleId)
    {
        window.open("print://" + saleId,  '_self').close()
    }
  });

function cerrarModalDescuentos() {
  // Oculta el modal con ID "Descuentos"
  $("#Descuentos").modal('hide');
}
	function QuitarPromo(id,id_sale_detail) {

		swal({
			title: '¿CONFIRMAS ELIMINAR LA PROMOCION? ',
			text: 'Esta accion no puede retraerse',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('QuitarPromo', id,id_sale_detail)
				swal.close()
			}

		})
	}  
	
  function ConfirmFactura(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE FACTURAR LA VENTA #'+id+' ?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('FacturarVenta', id)
        swal.close()
      }

    })
  }
    function ConfirmFacturaEnLote(id) {
    var accion = $('#accion').val();   
    if(accion == 1){ var msg = '¿QUIERE FACTURAR EN LOTE LAS VENTAS SELECCIONADAS?';}
    if(accion == 2){ var msg = '¿QUIERE ELIMINAR EN LOTE LAS VENTAS SELECCIONADAS? ESTO ELIMINARA TAMBIEN LOS PAGOS ASOCIADOS.';}
    
    swal({
      title: 'CONFIRMAR',
      text: msg,
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        Accion()
        swal.close()
      }

    })
  }
  
  
  
    function RestaurarVenta(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE RESTAURAR LA VENTA? SE RESTAURARAN TAMBIEN LOS PAGOS ASOCIADOS',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarVenta', id)
        swal.close()
      } 

    })
  }
  
    function ConfirmCancelado(estado_id,origen) {

    $('#modalDetails2').modal('hide')
    
    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE CANCELAR LA VENTA? SE ELIMINARAN LOS PAGOS ASOCIADOS',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('CancelarVenta', estado_id , origen)
        swal.close()
      } else {
       $('#modalDetails2').modal('show')   
      }

    })
  }
  
      function EliminarVenta(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE ELIMINAR LA VENTA? SE ELIMINARAN LOS PAGOS ASOCIADOS',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('EliminarVenta', id)
        swal.close()
      } 

    })
  }
  

</script>

<script>
function showHtmlDiv() {
  var htmlShow = document.getElementById("html-show");
  if (htmlShow.style.display === "none") {
    htmlShow.style.display = "block";
  } else {
    htmlShow.style.display = "none";
  }
}
</script>
<script>



function ConfirmAnularFactura(id) {

  swal({
    title: 'CONFIRMAR',
    text: '¿CONFIRMAS REALIZAR NOTA DE CREDITO DE LA FACTURA?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('AnularFactura', id)
      swal.close()
    }

  })
}

function ConfirmPago(id) {

  swal({
    title: 'CONFIRMAR',
    text: '¿CONFIRMAS ELIMINAR EL PAGO?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('deletePago', id)
      swal.close()
    }

  })
}

  function ConfirmFactura(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE FACTURAR LA VENTA ?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('FacturarVenta', id)
        swal.close()
      }

    })
  }
  
  
  function ConfirmEliminarProductoPedido(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE ELIMINAR EL PRODUCTO?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('deleteRow', id)
        swal.close()
      }

    })
  }
  
  
  
  
    function RestaurarVenta(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE RESTAURAR LA VENTA? SE RESTAURARAN TAMBIEN LOS PAGOS ASOCIADOS',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarVenta', id)
        swal.close()
      } 

    })
  }
  
    function ConfirmCancelado(estado_id,origen) {

    $('#modalDetails2').modal('hide')
    
    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE CANCELAR LA VENTA? SE ELIMINARAN LOS PAGOS ASOCIADOS',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('CancelarVenta', estado_id , origen)
        swal.close()
      } else {
       $('#modalDetails2').modal('show')   
      }

    })
  }
  
      function EliminarVenta(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE ELIMINAR LA VENTA? SE ELIMINARAN LOS PAGOS ASOCIADOS',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('EliminarVenta', id)
        swal.close()
      } 

    })
  }
  

</script>

<script type="text/javascript">

function simpli() {


var settings = {
    async: true,
    crossDomain: true,
    url: "https://api.simpliroute.com/v1/routes/visits/",
    method: "POST",
    headers: {
        "content-type": "application/json",
        authorization: "Token e68449ce3030a1a087e65ff8f95e4e6f8da87416",
    },
    processData: false,
    data: '{\n  "title": "Kwik e mart",\n  "address": "742 Evergreen Terrace, Springfield, USA",\n  "latitude": 44.052698,\n  "longitude": -123.020718,\n  "contact_name": "Apu Nahasapeemapetilon",\n  "contact_phone": "+123413123212",\n  "contact_email": "apu@example.com",\n  "reference": "invoice_id",\n  "notes": "Leave at front door",\n  "planned_date": "2022-08-12"\n}',
};

$.ajax(settings).done(function (response) {
    console.log(response);
});

}

</script>

	
 <script>

$(document).on('click keyup','.mis-checkboxes',function() {
   calcular();
 });
 
 
 

$(".check_todos").click(function(event){
	     if($(this).is(":checked")) {
		 	document.querySelectorAll('.mis-checkboxes').forEach(function(checkElement) {
                checkElement.checked = true;
            });
            calcular();
          }else{
			document.querySelectorAll('.mis-checkboxes').forEach(function(checkElement) {
                checkElement.checked = false;
            });
            calcular();
         }
 });

function Accion() {

  var id_accion = $('#accion').val();
  var tot = $('#total');
  tot.val(0);
  const ids = [];

  $('.mis-checkboxes').each(function() {
    if($(this).hasClass('mis-checkboxes')) {
      tot.val(($(this).is(':checked') ? parseFloat($(this).attr('tu-attr-precio')) : 0) + parseFloat(tot.val()));  
      
      if($(this).is(':checked')) {
      ids.push($(this).attr('tu-attr-id'));    
      }
       
    }
    else {
      tot.val(parseFloat(tot.val()) + (isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())));
      
    }
  });
  
  window.livewire.emit('accion-lote', ids , id_accion);

}

function calcular() {
  var tot = $('#total');
  tot.val(0);
  const ids = [];

  $('.mis-checkboxes').each(function() {
    if($(this).hasClass('mis-checkboxes')) {
      tot.val(($(this).is(':checked') ? parseFloat($(this).attr('tu-attr-precio')) : 0) + parseFloat(tot.val()));  
      
      if($(this).is(':checked')) {
      ids.push($(this).attr('tu-attr-id'));    
      }
      
    }
    else {
      tot.val(parseFloat(tot.val()) + (isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val())));
      
    }
  });
  
  console.log( ids );
  
  var total_mostrar = tot.val();
  
  if(total_mostrar > 0) {
   $("#total_mostrar").html("Total seleccionado: $ "+total_mostrar);
  } else {
     $("#total_mostrar").html("");
  }
  
  
}

    // Función para ocultar el modal
    function ocultarModal() {
        $('#ModalAgregarCliente').modal('hide');
        // Obtén el elemento del modal por su ID
        var modal = document.getElementById("ModalAgregarCliente");

        // Modifica la propiedad 'display' para ocultar el modal (puedes usar 'none')
        modal.style.display = "none";
        
    }
</script>

    <script>
        function scrollToElement(elementId) {
            var element = document.getElementById(elementId);
            
            // Verifica si el elemento existe
            if (element) {
                // Utiliza la propiedad scrollTop para desplazarte al elemento
                $('html, body').animate({
                    scrollTop: $(element).offset().top
                }, 100);
            }
        }
    </script>