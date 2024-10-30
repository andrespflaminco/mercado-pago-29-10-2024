<div>	                

                  @include('livewire.reports.configurar-columnas')
                  @include('livewire.reports.mail-modal')



				 
                  @include('livewire.reports.form-imprimir')
                  @include('livewire.reports.condicion-iva')
                  @include('livewire.reports.variaciones')
                  @include('livewire.reports.abrir-caja')
                  @include('livewire.gastos.estado-pedido-pos')
                  @include('livewire.reports.agregar-pago')
                  @include('livewire.reports.form-hoja-ruta')
                  @include('livewire.reports.form-hoja-ruta-nueva')
                  @include('livewire.factura.form-pagos')
                  @include('livewire.reports.actualizar-estado-venta')
                  @include('livewire.reports.sales-detail3')
                  @include('common.form-cliente')
                  
                  
    
    @if($NroVenta == 0)
    <div class="page-header">
					<div class="page-title">
							<h4>Ventas </h4>
							<h6>Ver los detalles de ventas</h6>
						</div>
						<div class="page-btn">
							<a hidden href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar producto</a>
						</div>
					</div>

	<!-- /product list -->
	<div class="card">
	        <ul class="nav nav-tabs  mb-3">
            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                <a style="{{ $sucursal_id == $comercio_id ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{$comercio_id}})"  > {{auth()->user()->name}} </a>
            </li>
            @foreach($sucursales as $item)
            <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                <a style="{{ $sucursal_id == $item->sucursal_id ? 'color: #e95f2b;' : '' }}" class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
            </li>
            @endforeach
        	</ul>
			
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
										<a wire:click="Filtros('{{$MostrarOcultar}}')" class="btn btn-cancel" style="width: auto; height: 34px; color: white;">
											<img style="padding:3px !important;"  src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img">
											</span>
											<text style="padding:5px;">Filtros</text>
											
										</a>
								</div>
								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
										    									
											<a 
											style="font-size:12px !important; padding:5px !important; background: #198754 !important;" 
											class="btn btn-cancel" 
											wire:click="ExportarReporte(' {{ ( ($usuarioSeleccionado == '' ? '0' : $usuarioSeleccionado) . '/' . ($ClienteSeleccionado == '' ? '0' : $ClienteSeleccionado)  .  '/' . ($estado_pago == '' ? '0' : ($estado_pago == 'Pago' ? '1' : '2')) . '/'. ($EstadoSeleccionado == '' ? '0' : $EstadoSeleccionado) . '/' . ($MetodoPagoSeleccionado == '' ? '0' : $MetodoPagoSeleccionado) . '/' .  ($estado_facturacion == '' ? 'all' : $estado_facturacion) . '/'  . $dateFrom . '/' . $dateTo) }} ')"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
											Exportar </a>
											
										</li>
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
                                                    <label>Cliente</label>
                                                    <div wire:ignore>
                            
                                                        <select class="form-control tagging" multiple="multiple" id="select2-dropdown">
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
                                                    <label>Vendedor</label>
                                                    <div wire:ignore>
                            
                                                        <select class="form-control tagging" multiple="multiple" id="select2-dropdown2">
                                                          @foreach($users as $u)
                                                          <option value="{{$u->id}}">{{$u->name}}</option>
                                                          @endforeach
                                                      </select>
                                                  </div>
                                                  </div>
                                                  </div>
                            
                                                  <div class="col-sm-12 col-md-4">
                                                   <div class="form-group">
                                                    <label>Estado de pedido</label>
                                                    <div wire:ignore>
                            
                                                        <select class="form-control tagging" multiple="multiple" id="select2-dropdown3">
                                                          <option value="Pendiente">Pendiente</option>
                                                          <option value="En proceso">En proceso</option>
                                                          <option value="Entregado">Entregado</option>
                                                          <option value="Cancelado">Cancelado</option>
                                                      </select>
                                                  </div>
                                                  </div>
                                                  </div>
                            
                                                  <div class="col-sm-12 col-md-4">
                                                     <div class="form-group">
                                                      <label>Metodo de pago</label>
                                                      <div wire:ignore>
                            
                                                          <select class="form-control tagging" multiple="multiple" id="select2-dropdown-metodo-pago">
                                                            <option value="1">Efectivo</option>
                                                            @foreach($metodo_pago_filtro as $mp)
                                                            <option value="{{$mp->id}}"> {{$mp->nombre_banco}} - {{$mp->nombre}}</option>
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
                                                    <label>Estado de facturacion</label>
                            
                                                        <select wire:model.lazy="estado_facturacion" class="form-control" >
                                                          <option value="all">Todos</option>
                                                          <option value="0">No facturado</option>
                                                          <option value="1">Facturado</option>
                            
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
                                                  
                                                  
                                        <div class="row">

						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									DEUDA
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">$ {{ number_format($suma_deuda,2,",",".") ?: 0}}</span></h5>
									<h6>A cobrar</h6>
								</div>
							</div>
						</div>
						
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									PROM
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">$ {{ number_format($ticket_promedio,2,",",".") ?: 0}}</span></h5>
									<h6>Ticket Promedio</h6>
								</div>
							</div>
						</div>

						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash1">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
						            CANT
						            </span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters"></span> {{ number_format($cantidad_tickets,0,",",".") ?: 0}}</h5>
									<h6>Cantidad de tickets</h6>
								</div>
							</div>
						</div>
						
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									    Total
					   				</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">$ {{ number_format($suma_totales,2,",",".") ?: 0}}</span></h5>
									<h6>Ventas totales</h6>
								</div>
							</div>
						</div>

					
					</div>          
                        <!--TABLAE-->
                         @if(session('status'))
                         <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                         @endif
                    	<div class="row justify-content-between">						                
                    				    
				<!---------- ACCIONES EN LOTE -------->
		
                     @if($estado_filtro == 0)
                        <div style="padding-left: 0;" class="col-12 ml-0">
                          <div  class="input-group mt-2 mb-1">
                            <select style="    margin-left: 1.3%;   /* padding: 6px; */   border-color: #bfc9d4; border-radius: 3px;" type="text" id="accion" placeholder="Acciones en lote">
                              <option value="Elegir">Acciones en lote</option>
                             
                              <option value="1">Facturar</option>
                              <option value="2">Eliminar</option>
                              
                              </select>
                            <div class="input-group-append">
                              <button style="background:white; border: solid 1px #bfc9d4;" onclick="ConfirmFacturaEnLote()"  type="button">Aplicar</button>
                            </div>
                          </div>
                          
                          <input hidden id="total" type="text" placeholder="0.00"/>
                          
                          <div style="margin-left: 1.3%;" id="total_mostrar"></div>

                        </div>

                        <!----....................------>
                        
                        @endif

                    
                    	<!------------------------------------>
                    
                        <!---------- FILTRO DE ESTADO -------->
                    	
                    	<div class="col-2 mt-2 ">
                    	<div>
                    	<div style="padding-left: 0;" class="col-12 ml-0">
                    	<div  class="input-group">
                    	<a class="{{ $estado_filtro == 0 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(0)">Activos</a> | <a class="{{ $estado_filtro == 1 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(1)">Papelera</a>    
                    	</div>	
                    	</div>	    
                    	</div>    		    
                    
                    	</div>
                    			    
                    	<!----------------------------------->
                    	
                                
                    			    
                    
                    					
                    	</div>

							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
                                        <th>
                                        <input name="Todos" type="checkbox" value="1" class="check_todos"/>    
                                        </th>
                                        <th wire:click="OrdenarColumna('nro_venta')" @if(!$columns['nro_venta']) style="display: none;" @endif>NRO VENTA 
                                            @if ($columnaOrden == 'nro_venta')
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
                                        <th wire:click="OrdenarColumna('nro_factura')" @if(!$columns['nro_factura']) style="display: none;" @endif>FACTURA
                                            @if ($columnaOrden == 'nro_factura')
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

                                        <th wire:click="OrdenarColumna('status')" @if(!$columns['status']) style="display: none;" @endif>ESTADO
                                            @if ($columnaOrden == 'status')
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
                                         <td><input type="checkbox" wire:model.defer="id_check" tu-attr-precio="{{($d->subtotal-$d->descuento+$d->recargo+$d->iva)}}" tu-attr-id="{{($d->id)}}"  class="mis-checkboxes" value="{{$d->id}}"></td>
                                        <td @if(!$columns['nro_venta']) style="display: none;" @endif>{{$d->nro_venta}}</td>
                                        <td @if(!$columns['created_at']) style="display: none;" @endif>
                                           
                                                {{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y H:i')}}
                                    
                                        </td>
                                        <td @if(!$columns['nombre_cliente']) style="display: none;" @endif>{{$d->nombre_cliente}}
                                         @foreach($ecommerce_envios as $ee)
                                        
                                        @if($ee->sale_id == $d->id)
                                        - {{$ee->nombre_destinatario}}
                                
                                        @endif
                                        
                                        @endforeach
                                        </td>
                                        <td @if(!$columns['subtotal']) style="display: none;" @endif>
                                            @if($d->nro_factura)
                                            ${{number_format($d->subtotal-$d->descuento+$d->recargo,2)}}
                                            @else
                                            ${{number_format($d->subtotal-$d->descuento+$d->recargo+$d->iva,2)}}
                                            @endif
                                        </td>
                                        <td @if(!$columns['iva']) style="display: none;" @endif>
                                             @if($d->nro_factura)
                                             ${{number_format($d->iva,2)}}
                                             @else
                                             -
                                             @endif
                                        </td>
                                        <td @if(!$columns['total']) style="display: none;" @endif>
                                          <b>$
                                          {{number_format($d->subtotal-$d->descuento+$d->recargo+$d->iva,2)}}</b></td>
                                        <td @if(!$columns['nombre_banco']) style="display: none;" @endif>{{$d->nombre_banco}} - {{$d->nombre_metodo_pago}}</td>
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
                                        <td @if(!$columns['deuda']) style="display: none;" @endif>
                                        $ {{$d->deuda}}
                                        </td>
                                        <td @if(!$columns['status']) style="display: none;" @endif>
                                        @if($d->status == 'Pendiente')
                                        <span style="cursor:pointer;" wire:click.prevent="ActualizarEstadoModal({{$d->id}})" class="badges bg-lightyellow">
                                        Pendiente
                                        </span>
                                        @endif
                                        @if($d->status == 'En proceso')
                                        <span style="cursor:pointer; background: #6c757d !important;"  wire:click.prevent="ActualizarEstadoModal({{$d->id}})" class="badges bg-lightyellow">En proceso</span>
                                        @endif
                                        @if($d->status == 'Cancelado')
                                        <span style="cursor:pointer;" wire:click.prevent="ActualizarEstadoModal({{$d->id}})" class="badges bg-lightred">Cancelado</span>
                                        @endif
                                        @if($d->status == 'Entregado')
                                        <span style="cursor:pointer;"  wire:click.prevent="ActualizarEstadoModal({{$d->id}})" class="badges bg-lightgreen">Entregado</span>
                                        @endif
                                        @if($d->status == 'Entrega Parcial')
                                        <span style="cursor:pointer; background: #719ad7 !important;""  wire:click.prevent="ActualizarEstadoModal({{$d->id}})"  class="badges bg-lightblue">Entrega Parcial</span>
                                        @endif
                                       </td>
                                        <td @if(!$columns['nota_interna']) style="display: none;" @endif>
                                           {{$d->nota_interna}}
                                        </td>
                                        <td @if(!$columns['observaciones']) style="display: none;" @endif>
                                           {{$d->observaciones}}
                                        </td>
                                        <td>
                                            @if($estado_filtro == 0)
                                              <div class="btn-group mb-1 mr-1">
                                                    <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <div style="z-index:99999999999999999 !important;" class="dropdown-menu">
                 
                                                    <a href="javascript:void(0);" wire:click.prevent="RenderFactura({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Ver </a>
                                                    <a hidden href="javascript:void(0);" wire:click.prevent="SetCompraSucursal({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Setear compra </a>
                                                    @if(2 < Auth::user()->plan)
                                                      <a href="javascript:void(0);" wire:click="MailModal({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>Enviar por mail</a>
                                                    @endif
                                                    
                                                    
                                                    <a href="{{ url('report-factura/pdf' . '/' . $d->id) }}" target="_blank"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Imprimir </a>
                                                    
                                                    <a href="javascript:void(0);" onclick="EliminarVenta({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Eliminar </a>


                                                    @if(Auth::user()->plan != 1)

                                                     @if($d->nro_factura > 0)

                                                      @else
                                                      
                                                      @if(1 < Auth::user()->plan)
                                                      
                                                      @if($datos_facturacion_elegidos != null)
                                                      @if ( ($datos_facturacion_elegidos->iva_defecto != $d->alicuota_iva) || ($datos_facturacion_elegidos->relacion_precio_iva != $d->relacion_precio_iva) )
                                                      <a href="javascript:void(0);" class="dropdown-item" wire:click="ElegirCondicionesIVA('{{$d->id}}')"><i class="flaticon-home-fill-1 mr-1"></i>Facturar</a>
                                                      
                                                      @else
                                                      
                                                      @if($d->nro_factura == null)
                                                      <a href="javascript:void(0);" class="dropdown-item" onclick="ConfirmFactura('{{$d->id}}')"><i class="flaticon-home-fill-1 mr-1"></i>Facturar</a>
                                                      @endif
                                                      @endif
                                                      @endif
                                                      @endif
                                                      
                                                      @endif

                                                      @endif
                                                  </div>
                                              </div>
                                            @else
                                            <!----- RESTAURAR VENTA ----->
                                                <a href="javascript:void(0);" onclick="RestaurarVenta({{$d->id}})" class="btn btn-dark text-white"> Restaurar </a>

                                            @endif
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
	@include('livewire.reports.ver-venta')
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