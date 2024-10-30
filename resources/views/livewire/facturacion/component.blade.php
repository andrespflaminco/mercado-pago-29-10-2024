<div>	                

@if($usuario->confirmed_at == null)

<div style="padding:30px !important;" class="card mt-3 mb-3">
<div class="row">
<div class="col-12 text-center">
    <h2>Este es un modulo pago, no disponible en la prueba gratuita. </h2>
    <br>
    <br>
    <br>
    <a class="btn btn-submit" href="https://www.flaminco.com.ar/planes/">SUSCRIBIRSE</a>
</div> 
</div> 
</div> 


@else

@if($datos_facturacion_existe == 0)

@include('livewire.facturacion.pasos')

@endif

@if($datos_facturacion_existe == 2)
<div style="padding:30px !important;" class="card mt-3 mb-3">
<div class="row">
<div class="col-12 text-center">
    <h2>Aguarde 24 hs habiles por favor que AFIP habilite el servicio. </h2>
</div> 
</div> 
</div> 

@endif

@if($datos_facturacion_existe == 1)
    <!-- Modal -->
<div class="modal fade" id="MailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ingrese un mail</h5>
                <button type="button" class="close" wire:click.prevent="CerrarModalMail()" aria-label="Close">
                  x
                </button>
            </div>
            <div style="width: 100% !important;" class="modal-body">
            <div class="col-12">
            <label>Mail</label>
            <input type="text" wire:model.defer="mail_ingresado" class="form-control" >    
            </div>
             </div>
            <div class="modal-footer">
                 <a href="javascript:void(0);" wire:click.prevent="CerrarModalMail()" class="btn btn-cancel">Cerrar</a>
                 <a wire:click.prevent="EnviarMail()" href="javascript:void(0);" class="btn btn-submit me-2" >Enviar</a>
            </div>
        </div>
    </div>
</div>


				 
                  @include('livewire.reports.form-imprimir')
                  @include('livewire.reports.condicion-iva')
                  @include('livewire.reports.variaciones')
                  @include('livewire.reports.abrir-caja')
                  @include('livewire.gastos.estado-pedido-pos')
                  @include('livewire.reports.actualizar-estado-venta')
                  @include('livewire.reports.sales-detail3')
                  @include('common.form-cliente')
                  
                  
                  
    @if($factura_id == 0)
    <div class="page-header">
					<div class="page-title">
							<h4>Facturacion</h4>
							<h6>Ver las facturas emitidas</h6>
						</div>
						<div class="page-btn">
							<a hidden href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar producto</a>
						</div>
					</div>

	<!-- /product list -->
	<div class="card">
	        <ul class="nav nav-tabs  mb-3">
            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                <a style="{{ $sucursal_id == $comercio_id ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{auth()->user()->id}})"  > {{auth()->user()->name}} </a>
            </li>
            @foreach($sucursales as $item)
            <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                <a style="{{ $sucursal_id == $item->sucursal_id ? 'color: #e95f2b;' : '' }}" class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
            </li>
            @endforeach
        	</ul>
			
						<div class="card-body">
					    
							<div class="table-top">
								@can("ver filtros facturacion")
								<div class="search-set">
									<div class="search-path">
									   
										<a wire:click="Filtros('{{$MostrarOcultar}}')" class="btn btn-filter" >
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img">
											
											</span>
										</a>
									</div>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								@endcan
								
								@can("exportar excel facturacion")
								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
										   <a data-bs-toggle="tooltip" wire:click="ExportarReporte(' {{ ( ($tipo_comprobante_buscar == '' ? '0' : $tipo_comprobante_buscar) . '/' . ($facturas_repetidas == '' ? '0' : $facturas_repetidas) . '/' . ($ClienteSeleccionado == '' ? '0' : $ClienteSeleccionado)  .  '/' . ($estado_pago == '' ? '0' : ($estado_pago == 'Pago' ? '1' : '2')) . '/'  . $dateFrom . '/' . $dateTo) }} ')"
											data-bs-placement="top" title="Exportar excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
								@endcan
							</div>
						@can("ver filtros facturacion")
							<!------ FILTROS ------->
							<div class="card mb-3"  style="border: solid 1px #eee; padding: 20px; display:{{$MostrarOcultar}};">
                                              <div class="card-body">
                                                <div class="row">

                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>CUIT Vendedor</label>
                                                        <select class="form-control" wire:model="cuit_vendedor_buscar">
                                                          <option value="0">Todos</option>
                                                          @foreach($cuit_vendedor as $cuit)
                                                          <option value="{{$cuit}}">{{$cuit}}</option>
                                                          @endforeach
                                                      </select>
                                                  </div>
                                                  </div>
                                                  
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Cliente</label>
                                                    <div wire:ignore>
                            
                                                        <select class="form-control tagging" multiple="multiple" id="clientes-dropdown">
                                                          <option value="1">Consumidor final</option>
                                                          @foreach($clientes as $client)
                                                          <option value="{{$client->id}}">{{$client->nombre}}</option>
                                                          @endforeach
                                                      </select>
                                                  </div>
                                                  </div>
                                                  </div>

                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Estado de pago</label>
                            
                                                        <select wire:model.lazy="estado_pago" class="form-control" >
                                                          <option value="">Todos</option>
                                                          <option value="Pendiente">Pendiente</option>
                                                          <option value="Pago">Pago</option>
                            
                                                      </select>
                                                  </div>
                                                  </div>
                                                  
                                                  
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Tipo de comprobante</label>
                            
                                                        <select wire:model="tipo_comprobante_buscar" class="form-control" >
                                                          <option value="0">Todos</option>
                                                          @foreach($tipo_comprobante as $tc)
                                                          <option value="{{$tc}}">Factura @if($tc == "FB") B @elseif ($tc == "FA") A @else {{$tc}} @endif</option>
                                                          @endforeach
                                                          <option value="NC">Nota de Credito</option>
                            
                                                      </select>
                                                  </div>
                                                  </div>
                                                  
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Cantidad de facturas por venta</label>
                            
                                                        <select wire:model="facturas_repetidas" class="form-control" >
                                                          <option value="0">Todos</option>
                                                          <option value="1">Ventas con mas de 1 factura</option>
                                                          <option value="2">Ventas con solo 1 factura</option>
                            
                                                      </select>
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
                        @endcan
                        
                    @can("ver totales facturacion")        
                   
                    @if($condicion_iva_unico == "Monotributo")        
                    <p class="mb-0">Condicion: Monotributo</p>
					
                    <div class="row ">
						
						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									    Total
					   				</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">$ {{ number_format($suma_totales,2,",",".") ?: 0}}</span></h5>
									<h6>Total facturado</h6>
								</div>
							</div>
						</div>
						
						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: rgba(40, 199, 111, 0.12) !important;">
									    {{$categoria_monotributo_actual}}
					   				</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters" > Hasta $ {{ number_format($limite_categoria_actual,2,",",".") ?: 0}}</span></h5>
									<h6>Categoria actual</h6>
								</div>
							</div>
						</div>
						
						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: rgba(234, 84, 85, 0.12) !important;">
									    Limite
					   				</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">$ {{ number_format($limite_categorias_monotributo,2,",",".") ?: 0}}</span></h5>
									<h6>Limite  monotributo</h6>
								</div>
							</div>
						</div>

					</div>
					@else 
					
					@if($condicion_iva_unico != null)
					    <p class="mb-0">Condicion: {{$condicion_iva_unico}}</p>
					@endif
					<div class="row">

						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									SUB
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">$ {{ number_format($suma_subtotal,2,",",".") ?: 0}}</span></h5>
									<h6>Subtotal</h6>
								</div>
							</div>
						</div>

						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget dash1">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
						            IVA
						            </span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters"></span> $ {{ number_format($iva_total,2,",",".") ?: 0}}</h5>
									<h6>IVA ventas</h6>
								</div>
							</div>
						</div>
						
						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									    Total
					   				</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">$ {{ number_format($suma_totales,2,",",".") ?: 0}}</span></h5>
									<h6>Total facturado</h6>
								</div>
							</div>
						</div>
                        

					</div>
					@endif
					
					
					@endcan
                        
                        @if(session('status'))
                         <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                         @endif
       					
       						<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
                                        <th wire:click="OrdenarColumna('nro_venta')">NRO VENTA
                                            @if ($columnaOrden == 'nro_venta')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('nro_factura')" >FACTURA
                                            @if ($columnaOrden == 'nro_factura')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('nota_credito')" > NOTA CREDITO
                                            @if ($columnaOrden == 'nota_credito')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        

                                        <th wire:click="OrdenarColumna('created_at')">FECHA VENTA
                                            @if ($columnaOrden == 'created_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('fecha_factura')">FECHA FACTURA
                                            @if ($columnaOrden == 'fecha_factura')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('cuit_vendedor')">CUIT VENDEDOR
                                            @if ($columnaOrden == 'cuit_vendedor')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>                                        <th wire:click="OrdenarColumna('nombre_cliente')">CLIENTE
                                            @if ($columnaOrden == 'nombre_cliente')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('subtotal')" >SUBTOTAL
                                            @if ($columnaOrden == 'subtotal')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('iva')" >IVA
                                            @if ($columnaOrden == 'iva')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('total')" >TOTAL
                                            @if ($columnaOrden == 'total')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th hidden wire:click="OrdenarColumna('total_facturado')" >TOTAL FACTURADO
                                            @if ($columnaOrden == 'total')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('cae')">CAE
                                            @if ($columnaOrden == 'cae')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        
                                        <th >ACCIONES</th>
										</tr>
									</thead>
									<tbody>
                                    @if($data_reportes->count() < 1)
                                    <tr><td colspan="7"><h5>Sin Resultados</h5></td></tr>
                                    @endif
                                    @foreach($data_reportes as $d)
                                    <?php $sum += $d->total; ?>
                                    <tr>
                                        
                                        <td>{{$d->nro_venta}}</td>
                                        <td>
                                        
                                        <span style="cursor:pointer;" wire:click="RenderFactura('{{$d->factura_id}}')" class="badge bg-success">
                                             {{$d->nro_factura}} 
                                        </span>
                                        
                                        </td>

                                        <td>
                                        
                                        @if($d->nota_credito != null)
                                        <span style="cursor:pointer;" wire:click="RenderFactura('{{$d->factura_id}}')" class="badge bg-danger">
                                             {{$d->nota_credito}}
                                        </span>
                                        @endif
                                        
                                        </td>
                                        <td>
                                           
                                                {{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y H:i')}}
                                    
                                        </td>
                                        <td>
                                           
                                                {{\Carbon\Carbon::parse($d->fecha_factura)->format('d-m-Y H:i')}}
                                    
                                        </td>
                                       <td>
                                            @if(strlen($d->cuit_vendedor) > 2)
                                                {{ substr($d->cuit_vendedor, 0, 2) }}-{{ substr($d->cuit_vendedor, 2, -1) }}-{{ substr($d->cuit_vendedor, -1) }}
                                            @else
                                                {{ $d->cuit_vendedor }} <!-- En caso de que la longitud sea menor o igual a 2, muestra el valor sin formato -->
                                            @endif
                                        </td>

                                        <td >{{$d->nombre_cliente}}
                                       
                                        </td>
                                        <td >
                                            @if($d->nro_factura)
                                            ${{number_format($d->subtotal-$d->descuento+$d->recargo,2)}}
                                            @else
                                            ${{number_format($d->subtotal-$d->descuento+$d->recargo+$d->iva,2)}}
                                            @endif
                                        </td>
                                        <td >
                                             @if($d->nro_factura)
                                             ${{number_format($d->iva,2)}}
                                             @else
                                             -
                                             @endif
                                        </td>
                                        <td>
                                          <b>$
                                          {{number_format($d->subtotal-$d->descuento+$d->recargo+$d->iva,2)}} </b></td>
                                        </td>
                                        <td hidden>$ {{number_format($d->total_facturado,2) }}</td>
                                        <td>{{$d->cae}}</td>


                                        <td>
                                              <div class="btn-group mb-1 mr-1">
                                                    <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div style="z-index:99999999999999999 !important;" class="dropdown-menu">
                 
                                                    <a href="javascript:void(0);" wire:click.prevent="RenderFactura({{$d->factura_id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Ver </a>
                                                    @if($d->nota_credito == null)
                                                    @can("anular factura")
                                                    <a href="javascript:void(0);" onclick="ConfirmAnularFactura({{$d->factura_id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Anular factura (Nota de credito) </a>
                                                    @endcan
                                                    @endif                                                
                                                   
                                                  </div>
                                              </div>
                                        </td>
                                    </tr>
                                    @endforeach

									</tbody>
								</table>
								
							</div>
							<br><br>
							@can("ver paginacion facturas")
							{{$data_reportes->links()}}
							@endcan
						</div>
					</div>

	
	@else
	@include('livewire.facturacion.ver-venta')
	@endif

    @include('livewire.facturacion.ver-factura')
                  

@endif


@endif

</div>


<script>

    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', function () {
         $('.tagging').select2({
                        tags: true
                    });           
        });

        $('#clientes-dropdown').on('change', function(e) {
          @this.emit('ClienteSelected', $('#clientes-dropdown').select2('val'));
        });

        $('#select2-dropdown2').on('change', function(e) {
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
                  @this.emit('ClienteSelected', $('#select2-dropdown').select2('val'));
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

        
        window.livewire.on('ver-factura', Msg =>{
            $('#VerFactura').modal('show')
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
        
        noty("Cliente agregado");
    }
</script>
<!-- Agrega esta sección de script a tu archivo HTML -->
<script>

    document.addEventListener("DOMContentLoaded", function () {

        // Inicializa el paso
        mostrarPaso(1);


        // Agrega escuchadores de eventos de clic a los botones de navegación
        document.getElementById("paso1BtnAnterior").addEventListener("click", function () {
            mostrarPaso(1);
        });
        
        // Agrega escuchadores de eventos de clic a los botones de navegación
        document.getElementById("paso2BtnAnterior").addEventListener("click", function () {
            mostrarPaso(2);
        });

        document.getElementById("paso2Btn").addEventListener("click", function () {
            mostrarPaso(2);
        });

        document.getElementById("paso3Btn").addEventListener("click", function () {
            mostrarPaso(3);
        });
    });

    function mostrarPaso(numeroPaso) {
        // Oculta todos los pasos
        document.querySelectorAll(".contenido-paso").forEach(function (paso) {
            paso.style.display = "none";
        });

        // Muestra el paso seleccionado
        document.getElementById("paso" + numeroPaso).style.display = "block";

        // Actualiza la visibilidad de los botones de navegación
        actualizarVisibilidadBotones(numeroPaso);
        
        // Hacer que la página vuelva arriba
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
    }

    function actualizarVisibilidadBotones(numeroPaso) {
        // Muestra u oculta los botones según el paso actual
        document.getElementById("paso1BtnAnterior").style.display = (numeroPaso == 2) ? "inline-block" : "none";
        document.getElementById("paso2BtnAnterior").style.display = (numeroPaso == 3) ? "inline-block" : "none";
        document.getElementById("paso2Btn").style.display = (numeroPaso == 1) ? "inline-block" : "none";
        document.getElementById("paso3Btn").style.display = (numeroPaso == 2) ? "inline-block" : "none";
        document.getElementById("paso4Btn").style.display = (numeroPaso == 3) ? "inline-block" : "none";
        
    }
</script>
<script>
    function validarFormulario() {
       var respuesta = validacionFormulario();
       if(respuesta == true){
           window.livewire.emit('StoreDatosFacturacion', '');
       }
    }
</script>
<script>
    function validacionFormulario() {
        // Obtiene los valores de los campos
        var relacionPrecioIva = document.getElementById("relacion_precio_iva_form").value;
        var ivaDefecto = document.getElementById("iva_defecto_form").value;
        var condicionIva = document.getElementById("condicion_iva_form").value;

        var razonSocial = document.getElementById("razon_social_form").value;
        var Cuit = document.getElementById("cuit_form").value;
        var PuntoVenta = document.getElementById("pto_venta_form").value;
        
        


        // Realiza las verificaciones
        if(razonSocial == ""){
        alert("El nombre de fantasia o razon social no puede estar vacio.");
        return false;            
        }
        
        if(Cuit == ""){
        alert("El CUIT no puede estar vacio.");
        return false;            
        }
        // Utiliza una expresión regular para comprobar que la cadena solo contiene números
        var CuitsoloNumeros = /^[0-9]+$/.test(Cuit);
        
        if(!CuitsoloNumeros){
        alert("El CUIT debe contener solo numeros.");
        return false;       
        }

        if (condicionIva == "Elegir") {
            alert("Debe elegir la condición de IVA.");
            return false;
        }
        
        if(PuntoVenta == ""){
        alert("El punto de venta no puede estar vacio.");
        return false;            
        }
        // Utiliza una expresión regular para comprobar que la cadena solo contiene números
        var PuntoVentasoloNumeros = /^[0-9]+$/.test(PuntoVenta);
        
        if(!PuntoVentasoloNumeros){
        alert("El punto de venta debe contener solo numeros.");
        return false;       
        }
        if (ivaDefecto == "Elegir") {
            alert("Debe elegir el IVA defecto.");
            return false;
        }        
        if (relacionPrecioIva == "Elegir") {
            alert("Debe elegir la relacion Precio IVA.");
            return false;
        }

        
        if (relacionPrecioIva == 0 && ivaDefecto != 0) {
            alert("Chequee el IVA por defecto y la relación precio IVA.");
            return false;
        }

        if (relacionPrecioIva != 0 && ivaDefecto == 0) {
            alert("Chequee el IVA por defecto y la relación precio IVA.");
            return false;
        }


        if (condicionIva == "Monotributo" && ivaDefecto != 0) {
            alert("No puede incluir IVA si es monotributo.");
            return false;
        }

        if (condicionIva != "Monotributo" && ivaDefecto == 0) {
            alert("Debe incluir el IVA.");
            return false;
        }

        // Si todas las verificaciones pasan, puedes permitir el envío del formulario
        return true;
        
        
    }
</script>