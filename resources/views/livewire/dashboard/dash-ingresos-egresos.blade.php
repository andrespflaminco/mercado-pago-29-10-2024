
        <!--------- INGRESOS VS EGRESOS ------------->
		
        <div class="row">
        		
				@include('livewire.dashboard-nuevo.contador-ingresos-egresos')
        </div>       
        
        <!--------- / INGRESOS VS EGRESOS ------------->
		
		
                    
        <div class="row mb-3">
            <div class="col-7">
                <div class="card mb-0">
						<div class="card-body">
						    <div class="dropdown">
								<a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" class="dropset">
									<i class="fa fa-ellipsis-v"></i>
								</a>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
									<li>
									<a href="javascript:void(0)" wire:click="FiltroIngresosEgresos('Meses')" class="dropdown-item">Mes</a>
								</li>
								<li>
									<a href="javascript:void(0)" wire:click="FiltroIngresosEgresos('Dias')" class="dropdown-item">Dia</a>
								</li>
								</ul>
							</div>
						 <div id="chart-finanzas" class=""></div>   
						</div>
					</div>
            </div>
<div class="col-lg-5 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h4 class="card-title mb-0">Ingresos por metodos de pago</h4>
									<div class="dropdown">
										<a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" class="dropset">
											<i class="fa fa-ellipsis-v"></i>
										</a>
										<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
											<li>
												<a href="productlist.html" class="dropdown-item">Metodo de pago</a>
											</li>
											<li>
												<a href="addproduct.html" class="dropdown-item">Total</a>
											</li>
										</ul>
									</div>
								</div>
								

    
								<div class="card-body">
									<div style="overflow-y: auto !important;  height: 300px !important;" class="table-responsive dataview">
										<table id="MetodosPago" class="table" id="miTabla">
											<thead >
												<tr>
												<th wire:click="OrdenarColumnaMetodos('banco')">Banco 
                                                @if ($columnaOrdenMetodos == 'banco')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenMetodos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
                                        
												<th wire:click="OrdenarColumnaMetodos('nombre')">Metodo de pago 
                                                @if ($columnaOrdenMetodos == 'nombre')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenMetodos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
                                        
                                                <th wire:click="OrdenarColumnaMetodos('total')">Total 
                                                @if ($columnaOrdenMetodos == 'total')
                                                @include('livewire.reports.flecha-' . ($direccionOrdenMetodos === 'asc' ? 'arriba' : 'abajo'))
                                                @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                                @endif
                                                </th>
												</tr>
											</thead>
											<tbody>
											    @foreach($metodos_pago as $mp)
												<tr >
													<td>
													{{$mp->banco}}
													</td>
													<td>{{$mp->nombre}}</td>
													<td>$ {{ number_format($mp->total,2)}}</td>
												</tr>
												@endforeach
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
							<h4 class="card-title">Ingresos y Egresos</h4>
							<div style="height: 350px;" class="table-responsive dataview">
								<table class="table">
									<thead>
										<tr>
								        	  <th></th>
                                            @foreach ($total_ingresos_egresos as $result)
                                                    @php
                                                          // Configurar Carbon en español
                                                        setlocale(LC_TIME, 'es_ES');
                                                
                                                        // Formatear la fecha y convertir el mes a mayúsculas
                                                        $fechaFormateada = mb_strtoupper(\Carbon\Carbon::createFromFormat('m-Y', $result->months)->formatLocalized('%b-%Y'));

                                                    @endphp
                                                <th>{{ $fechaFormateada }}</th>
                                                
                                            @endforeach
                                            <th>Total</th>
										</tr>
									</thead>
									<tbody>
									   <tr>
                                             <td>Ingresos</td>
                                            @foreach ($total_ingresos_egresos as $result)
                                                <td>${{ number_format($result->ingresos,2) }}</td>
                                            @endforeach
                                               <td>
                                                ${{ number_format(array_sum(array_column($total_ingresos_egresos->toArray(), 'ingresos')), 2) }}
                                            </td> <!-- Sumar todos los ingresos en una última columna -->
                                 
                                        </tr>
                                        <tr>
                                            <td>Egresos</td>
                                            @foreach ($total_ingresos_egresos as $result)
                                                <td>${{ number_format($result->egresos,2) }}</td>
                                            @endforeach
                                           <td>
                                                ${{ number_format(array_sum(array_column($total_ingresos_egresos->toArray(), 'egresos')), 2) }}
                                            </td> <!-- Sumar todos los egresos en una última columna -->
                                       

                                        </tr>
									</tbody>
									<tfoot>
									    <th>
									        <tr>
									            <td><b>Total</b></td>
									            @foreach ($total_ingresos_egresos as $result)
                                                <td>${{ number_format($result->ingresos-$result->egresos,2) }}</td>
                                                 @endforeach
                                                    <td>
                                                        ${{ number_format(array_sum(array_column($total_ingresos_egresos->toArray(), 'ingresos')) - array_sum(array_column($total_ingresos_egresos->toArray(), 'egresos')), 2) }}
                                                    </td> <!-- Calcular el total restando los egresos de los ingresos en una última columna -->
                                              
									        </tr>
									        
									    </th>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
            </div>
        </div>   