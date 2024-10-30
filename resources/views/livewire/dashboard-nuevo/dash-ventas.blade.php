    
<div class="row">
                
                <!----------- VENTAS ----------------------->

				@include('livewire.dashboard-nuevo.contador-ventas-costos')
				
						
			   <!-------- CONTADORES --------------------------> 
			   
			   
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('clientes')}}" target="_blank" class="dash-count">
								<div class="dash-counts">
									<h4>{{$cantidad_clientes}}</h4>
									<h5>Clientes finales</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="user"></i> 
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('proveedores')}}" target="_blank" class="dash-count das1">
								<div class="dash-counts">
									<h4>{{$cantidad_proveedores}}</h4>
									<h5>Proveedores</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="user-check"></i> 
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('reports')}}" target="_blank" class="dash-count das2">
								<div class="dash-counts">
									<h4>{{$cantidad_facturas_ventas}}</h4>
									<h5>Cantidad de Ventas</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="file-text"></i>
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('compras-resumen')}}" target="_blank" class="dash-count das3">
								<div class="dash-counts">
									<h4>{{$cantidad_facturas_compras}}</h4>
									<h5>Cantidad de Compras</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="file"></i>  
								</div>
							</a>
						</div>
					</div>
<!-- Button trigger modal -->

                    
                    
        <div class="row">
    					<div class="col-lg-7 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Ventas</h5>
									<div class="graph-sets">
										<ul>
											<li>
												<span>Ventas</span>
											</li>
											<li hidden>
												<span>Purchase</span>
											</li>
										</ul>
										<div hidden class="dropdown">
											<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
												Ventas
											</button>
											<ul style="max-height:350px !important;" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Ventas</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Compras</a>
												</li>	
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Ingresos vs Egresos</a>
												</li>	
											</ul>
										</div>
										<div  class="dropdown">
											<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
												{{$filtro_ventas}}
											</button>
											<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
												<li>
													<a wire:click="FiltroVentas('Meses')" href="javascript:void(0);" class="dropdown-item">Mensual</a>
												</li>
												<li>
													<a  wire:click="FiltroVentas('Dias')" href="javascript:void(0);" class="dropdown-item">Diario</a>
												</li>			
											</ul>
										</div>
									</div>
									
								</div>
							   <div class="widget-content">
                                <div class="tabs tab-content">
                                    <div id="content_1" class="tabcontent">
                                        <div id="sales_charts"></div>
                                    </div>
                                </div>
                            </div>
							</div>
						</div>
						
						<div class="col-lg-5 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h4 class="card-title mb-0">Ventas por dia</h4>

								</div>
								

    
								<div class="card-body" style="padding-top: 10px;">
									<div style="overflow-y: auto !important;  height: 300px !important;" class="table-responsive dataview">
										<table id="MetodosPago" class="table" id="miTabla">
											<tbody>
											@php
                                                $totalAcumuladoDias = 0;
                                            @endphp
                                            @foreach($ventas_por_dia as $dia => $ventas)
                                            @php
                                                $totalAcumuladoDias += $ventas;
                                            @endphp
                                            <tr>
                                                <td style="width: 30%">
                                                    @if($dia == "Monday")
                                                    <span class="point-apex-chart-flaminco" style="background-color: #00e396 !important;"></span> Lunes
                                                    @endif
                                                    @if($dia == "Tuesday")
                                                    <span class="point-apex-chart-flaminco" style="background-color: #008ffb !important;"></span> Martes
                                                    @endif
                                                    @if($dia == "Wednesday")
                                                    <span class="point-apex-chart-flaminco" style="background-color: #775DD0 !important;"></span> Miercoles
                                                    @endif
                                                    @if($dia == "Thursday")
                                                    <span class="point-apex-chart-flaminco" style="background-color: #FEB019 !important;"></span> Jueves
                                                    @endif
                                                    @if($dia == "Friday")
                                                    <span class="point-apex-chart-flaminco" style="background-color: #33b2df !important;"></span> Viernes
                                                    @endif
                                                    @if($dia == "Saturday")
                                                    <span class="point-apex-chart-flaminco" style="background-color: #546E7A !important;"></span> Sabado
                                                    @endif
                                                    @if($dia == "Sunday")
                                                    <span class="point-apex-chart-flaminco" style="background-color: #FF4560 !important;"></span> Domingo
                                                    @endif
                                                    
                                                </td>
                                                <td>$ {{ number_format($ventas, 2, ',', '.') }}</td> <!-- Formatea el número con separador de miles y coma decimal -->
                                            </tr>
                                            @endforeach	
                                            <tfooter>
                                                <tr>
                                                    <td><strong>Total</strong></td>
                                                    <td><strong>$ {{ number_format($totalAcumuladoDias, 2, ',', '.') }}</strong></td>
                                                </tr>
                                            </tfooter>
                                            </tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
        
                    
                    
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
						 <div class="row">
						     
						<div class="col-lg-8 col-sm-12 col-12 mb-2"><h6>Rentabilidad por producto</h6></div>   
						<div class="col-lg-4 col-sm-12 col-12 mb-0"></div>   
						
						
						<div class="col-lg-8 col-sm-12 col-12 d-flex">
						
							<div class="flex-fill">
								<div class="pb-0 d-flex justify-content-between align-items-center">
									<div class="graph-sets mr-3">
										<div class="dropdown">
        									<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        										<span class="point-apex-chart-flaminco"></span>
        										@if($switch_ventas_unidades == 1)
        										Ventas ($)
        										@endif
        										@if($switch_ventas_unidades == 2)
        										Unidades Vendidas
        										@endif
        									</button>
        										<ul style="max-height:350px !important;" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchVentasUnidades(1)" class="dropdown-item">Ventas ($)</a>
        											</li>
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchVentasUnidades(2)" class="dropdown-item">Unidades vendidas</a>
        											</li>
        										</ul>
        								</div>
										<div class="dropdown">
        									<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        									<span class="point-apex-chart-flaminco" style="background-color: #00e396 !important;"></span>
        									@if($switch_margen_rentabilidad == 1)
        									Margen Rentabilidad (%)
        									@endif
        									@if($switch_margen_rentabilidad == 2)
        									Rentabilidad ($)
        									@endif
        										
        									</button>
        								    	<ul style="max-height:350px !important;" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchMargenRentabilidad(1)"  class="dropdown-item">Margen Rentabilidad (%)</a>
        											</li>
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchMargenRentabilidad(2)" class="dropdown-item">Rentabilidad ($)</a>
        											</li>
        										</ul>
        								</div>
									</div>
									
								</div>
							   <div class="widget-content">
                                <div class="tabs tab-content">
                                    <div id="content_1" class="tabcontent">
                                        <div id="rentabilidad-producto" class=""></div>
                                    </div>
                                </div>
                            </div>
							</div>
						</div> 
						 <div class="col-lg-4 col-sm-12 col-12 d-flex"><div style="height: 350px;" class="table-responsive dataview w-100">
								<div class="row">
								    <div class="col-3" style="padding-right:0px !important;">Ordenar:</div>
								    <div class="col-5" style="padding-left:0px !important;">
								        <select  class="btn-white" wire:model="criterio_rentabilidad_producto">
										    <option value="3">Ventas en $</option>
										    <option value="4">Unidades vendidas</option>
										    <option value="1">Rentabilidad ($)</option>
										    <option value="2">Margen (%)</option>
										</select>
								    </div>
								    <div class="col-4" >
										<select class="btn-white" wire:model="orden_rentabilidad_producto">
										    <option value="asc">ASC</option>
										    <option value="desc">DESC</option>
										</select>  								        
								    </div>
								</div>
								<table class="table mt-3">
									<thead>
										<tr>
										    <th>Producto</th>
										    <th>
										    @if($criterio_rentabilidad_producto == 1)
										    Rentabilidad ($)
                                            @endif
                                            @if($criterio_rentabilidad_producto == 2)
                                            Margen (%)
                                            @endif
                                            @if($criterio_rentabilidad_producto == 3)
                                            Ventas en $
                                            @endif
                                            @if($criterio_rentabilidad_producto == 4)
                                            Unidades vendidas
                                            @endif
                                            </th>
										</tr>
									</thead>
									<tbody>
									@php
                                    $totalSegundaColumnaProducto = 0;
                                    $totalPorcentajeRentabilidadProducto = 0;
                                    $totalCantidadProducto = 0;
                                    @endphp
                                    @foreach($productos_mas_rentables as $item)
                                        <tr>
                                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">{{ $item->nombre_producto }}</td>
                                            <td>
                                                @if($criterio_rentabilidad_producto == 1)
                                                    $ {{ number_format($item->rentabilidad, 2, ",", ".") }}
                                                    @php
                                                        $totalSegundaColumnaProducto += $item->rentabilidad;
                                                    @endphp
                                                @elseif($criterio_rentabilidad_producto == 2)
                                                    {{ number_format($item->porcentaje_rentabilidad, 2, ",", ".") }} %
                                                    @php
                                                        $totalPorcentajeRentabilidadProducto += $item->porcentaje_rentabilidad;
                                                    @endphp
                                                @elseif($criterio_rentabilidad_producto == 3)
                                                    $ {{ number_format($item->venta, 2, ",", ".") }}
                                                    @php
                                                        $totalSegundaColumnaProducto += $item->venta;
                                                    @endphp
                                                @elseif($criterio_rentabilidad_producto == 4)
                                                    {{ number_format($item->cantidad, 0, ",", ".") }} unid
                                                    @php
                                                        $totalCantidadProducto += $item->cantidad;
                                                    @endphp
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    <tr>
                                        <td>Total</td>
                                        <td>
                                            @if($criterio_rentabilidad_producto == 2 && $productos_mas_rentables->isNotEmpty())
                                                {{ number_format($totalPorcentajeRentabilidadProducto / $productos_mas_rentables->count(), 2, ",", ".") }} %
                                            @else
                                                $ {{ number_format($totalSegundaColumnaProducto, 2, ",", ".") }}
                                            @endif
                                        </td>
                                    </tr>

									</tbody>
									<tfoot></tfoot>
								</table>
							</div></div>
						 </div>
						 
						</div>
					</div>
            </div>
        </div>       
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
						 <div class="row">
						     
						<div class="col-lg-8 col-sm-12 col-12 mb-2"><h6>Rentabilidad por categoria</h6></div>   
						<div class="col-lg-4 col-sm-12 col-12 mb-0"></div>   
						
						
						<div class="col-lg-8 col-sm-12 col-12 d-flex">
						
							<div class="flex-fill">
								<div class="pb-0 d-flex justify-content-between align-items-center">
									<div class="graph-sets mr-3">
										<div class="dropdown">
        									<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        										<span class="point-apex-chart-flaminco"></span>
        										@if($switch_ventas_unidades_categoria == 1)
        										Ventas ($)
        										@endif
        										@if($switch_ventas_unidades_categoria == 2)
        										Unidades Vendidas
        										@endif
        									</button>
        										<ul style="max-height:350px !important;" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchVentasUnidadesCategoria(1)" class="dropdown-item">Ventas ($)</a>
        											</li>
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchVentasUnidadesCategoria(2)" class="dropdown-item">Unidades vendidas</a>
        											</li>
        										</ul>
        								</div>
										<div class="dropdown">
        									<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        									<span class="point-apex-chart-flaminco" style="background-color: #00e396 !important;"></span>
        									@if($switch_margen_rentabilidad_categoria == 1)
        									Margen Rentabilidad (%)
        									@endif
        									@if($switch_margen_rentabilidad_categoria == 2)
        									Rentabilidad ($)
        									@endif
        										
        									</button>
        								    	<ul style="max-height:350px !important;" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchMargenRentabilidadCategoria(1)"  class="dropdown-item">Margen Rentabilidad (%)</a>
        											</li>
        											<li>
        												<a href="javascript:void(0);" wire:click="ElegirSwitchMargenRentabilidadCategoria(2)" class="dropdown-item">Rentabilidad ($)</a>
        											</li>
        										</ul>
        								</div>
									</div>
									
								</div>
							   <div class="widget-content">
                                <div class="tabs tab-content">
                                    <div id="content_1" class="tabcontent">
                                        <div id="rentabilidad-categoria" class=""></div>
                                    </div>
                                </div>
                            </div>
							</div>
						</div> 
						 <div class="col-lg-4 col-sm-12 col-12 d-flex"><div style="height: 350px;" class="table-responsive dataview w-100">
								<div class="row">
								    <div class="col-3" style="padding-right:0px !important;">Ordenar:</div>
								    <div class="col-5" style="padding-left:0px !important;">
								        <select  class="btn-white" wire:model="criterio_rentabilidad_categoria">
										    <option value="3">Ventas en $</option>
										    <option value="4">Unidades vendidas</option>
										    <option value="1">Rentabilidad ($)</option>
										    <option value="2">Margen (%)</option>
										</select>
								    </div>
								    <div class="col-4" >
										<select class="btn-white" wire:model="orden_rentabilidad_categoria">
										    <option value="asc">ASC</option>
										    <option value="desc">DESC</option>
										</select>  								        
								    </div>
								</div>
								<table class="table mt-3">
									<thead>
										<tr>
										    <th>Producto</th>
										    <th>
										    @if($criterio_rentabilidad_categoria == 1)
										    Rentabilidad ($)
                                            @endif
                                            @if($criterio_rentabilidad_categoria == 2)
                                            Margen (%)
                                            @endif
                                            @if($criterio_rentabilidad_categoria == 3)
                                            Ventas en $
                                            @endif
                                            @if($criterio_rentabilidad_categoria == 4)
                                            Unidades vendidas
                                            @endif
                                            </th>
										</tr>
									</thead>
									<tbody>
                                        @php
                                        $totalSegundaColumna = 0;
                                        $totalPorcentajeRentabilidad = 0;
                                        $totalCantidad = 0;
                                        @endphp
                                        
                                        @foreach($categorias_mas_rentables as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    @if($criterio_rentabilidad_categoria == 1)
                                                        $ {{ number_format($item->rentabilidad, 2, ",", ".") }}
                                                        @php
                                                            $totalSegundaColumna += $item->rentabilidad;
                                                        @endphp
                                                    @elseif($criterio_rentabilidad_categoria == 2)
                                                        {{ number_format($item->porcentaje_rentabilidad, 2, ",", ".") }} %
                                                        @php
                                                            $totalPorcentajeRentabilidad += $item->porcentaje_rentabilidad;
                                                        @endphp
                                                    @elseif($criterio_rentabilidad_categoria == 3)
                                                        $ {{ number_format($item->venta, 2, ",", ".") }}
                                                        @php
                                                            $totalSegundaColumna += $item->venta;
                                                        @endphp
                                                    @elseif($criterio_rentabilidad_categoria == 4)
                                                        {{ number_format($item->cantidad, 0) }} unid
                                                        @php
                                                            $totalCantidad += $item->cantidad;
                                                        @endphp
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        
                                        <tr>
                                            <td>
                                                @if($criterio_rentabilidad_categoria == 2 && $categorias_mas_rentables->isNotEmpty())
                                                Promedio total
                                                @else
                                                Total
                                                @endif
                                            </td>
                                            <td>
                                                @if($criterio_rentabilidad_categoria == 2 && $categorias_mas_rentables->isNotEmpty())
                                                    {{ number_format($totalPorcentajeRentabilidad / $categorias_mas_rentables->count(), 2, ",", ".") }} %
                                                @else
                                                    $ {{ number_format($totalSegundaColumna, 2, ",", ".") }}
                                                @endif
                                            </td>
                                        </tr>
                                        

									</tbody>
									<tfoot></tfoot>
								</table>
							</div></div>
						 </div>
						 
						</div>
					</div>
            </div>
        </div>     
        
        
        
        <div class="row mt-3">
    					<div class="col-lg-7 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Ventas por vendedor</h5>
									<div class="graph-sets mr-3">
                                    <input hidden class="form-control" type="text" wire:click="EstablecerObjetivo" wire:model="objetivo_vendedores">
									</div>
								</div>
							   <div class="widget-content">
                                <div class="tabs tab-content">
                                    <div id="content_1" class="tabcontent">
                                        <div id="vendedores"></div>
                                        <div id="colorContainer" class="color-container"></div>
                                    </div>
                                </div>
                                </div>
							</div>
						</div>
						
						<div class="col-lg-5 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h4 class="card-title mb-0">Ventas por canal</h4>
								</div>
								<div class="card-body">
                                <div id="canal"></div>
								</div>
							</div>
						</div>
		</div>
        <div hidden class="row mt-3">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
						 <div class="row">
						<div class="col-lg-8 col-sm-12 col-12 d-flex">
							<div class="flex-fill">
								<div class="pb-0 d-flex justify-content-between align-items-center">
									<div class="graph-sets mr-3">
									    <h6>Descuentos y promociones por mes</h6>
						
									</div>
								</div>
							   <div class="widget-content">
                                <div class="tabs tab-content">
                                    <div id="content_1" class="tabcontent">
                                        <div id="descuentos" class=""></div>
                                    </div>
                                </div>
                            </div>
							</div>
						</div> 
						 <div class="col-lg-4 col-sm-12 col-12 d-flex"><div style="height: 350px;" class="table-responsive dataview w-100">
								 <div class="row">
                                    <div class="col-12">
                                        <div class="card text-center mb-0">
                                            <div class="card-body">
                                                <h6 class="card-title">Título 1</h6>
                                                <h4>100</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card text-center mb-0">
                                            <div class="card-body">
                                                <h6 class="card-title">Título 2</h6>
                                                <h4>200</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card text-center mb-0">
                                            <div class="card-body">
                                                <h6 class="card-title">Título 3</h6>
                                                <h4>300</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card text-center mb-0">
                                            <div class="card-body">
                                                <h6 class="card-title">Título 4</h6>
                                                <h4>400</h4>
                                            </div>
                                        </div>
                                    </div>
								
							</div></div>
						 </div>
						 
						</div>
					</div>
            </div>
        </div>     
        </div>     
        
        
        
        <div hidden class="row mt-3">
    					<div class="col-lg-7 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Ventas por cliente</h5>
								</div>
							   <div class="widget-content">
                                <div class="tabs tab-content">
                                    <div id="content_1" class="tabcontent">
                                        <div id=""></div>
                                    </div>
                                </div>
                            </div>
							</div>
						</div>
						
						<div class="col-lg-5 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h4 class="card-title mb-0">Ventas por canal</h4>
								</div>
								<div class="card-body">
                                <div id="canal"></div>
								</div>
							</div>
						</div>
		</div>
					
					
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
						    <div class="d-flex justify-content-between align-items-center">
						    <h4 class="card-title">Ventas por producto</h4>
						    
						    <div class="col-3 mb-2">
						    <select class="form-control" wire:model="proveedor_elegido">
						        <option value="0">Todos los proveedores</option>
						        @foreach($proveedores as $pr)
						        <option value="{{$pr->id}}">{{$pr->nombre}}</option>
						        @endforeach
						    </select>						        
						    </div>

						    </div>
							
							
							<div style="height: 350px;" class="table-responsive dataview">
								<table class="table">
									<thead>
										<tr>
												<th wire:click="OrdenarColumnaProductos('barcode')">Codigo Producto 
                                                @if ($columnaOrdenProductos == 'barcode')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenProductos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
                                        
                                        		<th wire:click="OrdenarColumnaProductos('product')">Nombre 
                                                @if ($columnaOrdenProductos == 'product')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenProductos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
                                                
                                                <th wire:click="OrdenarColumnaProductos('nombre_proveedor')">Proveedor 
                                                @if ($columnaOrdenProductos == 'product')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenProductos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
                                        
                                        		<th wire:click="OrdenarColumnaProductos('quantity')">Cantidad 
                                                @if ($columnaOrdenProductos == 'quantity')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenProductos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
                                                
                                                
                                        
                                        		<th wire:click="OrdenarColumnaProductos('total')">Total Vendido
                                                @if ($columnaOrdenProductos == 'total')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenProductos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
                                        
										</tr>
									</thead>
									<tbody>
									    @foreach( $productos as $p)
												<tr>
													<td>{{$p->barcode}}</td>
													<td>
														<a href="javascript:void(0)" wire:click="PromedioVentaProducto('{{$p->id_producto}}')">{{$p->product}}</a>
													</td>
													<td>{{$p->nombre_proveedor}}</td>
													<td>{{number_format($p->quantity,0)}} </td>
													<td>$ {{ number_format($p->total,2)}}</td>
												</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
            </div>
        </div>            

        