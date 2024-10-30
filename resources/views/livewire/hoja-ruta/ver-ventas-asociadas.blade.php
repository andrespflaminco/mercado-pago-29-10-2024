
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							
        	                <div class="page-header">
        					<div class="page-title">
        							<h4> <a class="btn btn-sucess" href="{{ url('hoja-ruta') }}"><</a>  Hoja de ruta # {{$hoja_selected}}</h4>
        						</div>
        						<div class="page-btn d-flex">
            	                <a class="btn btn-added" style="margin-right: 10px !important; background: #FF9F43; padding: 7px 15px; color: #fff; font-weight: 700; font-size: 14px;"wire:click="BuscarVenta"> Asociar Ventas</a>
                  				<a style="color: black !important; background: #FAFBFE !important;  padding: 7px 15px; font-weight: 700; font-size: 14px; border-radius: 8px; border: 1px solid #E9ECEF;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Exportar</a>
                                <div class="dropdown-menu"> 
			     		            <a href="{{ url('report-hoja-ruta/pdf' . '/' . $hoja_selected .'/'.uniqid()) }}" target="_blank" class="dropdown-item"> PDF Hoja de ruta</a>
			     		            <a href="{{ url('report-hoja-ruta-consolidado/pdf' . '/' . $hoja_selected .'/'.uniqid()) }}" target="_blank" class="dropdown-item"> PDF Consolidado</a>
	    						
			     		        </div>				    
        						</div>
        					</div>
							
							 <div hidden class="row">
                               <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                 <label>Nombre del transportista</label>
                                 <div class="input-group mb-4">
                            
                                   <input disabled type="text" wire:model="nombre" class="form-control">
                            
                            
                                     </div>
                            
                               </div>
                               </div>
                               <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                 <label>Tipo de transporte</label>
                                 <select disabled wire:model='tipo' class="form-control">
                                   <option value="Elegir" disabled >Elegir</option>
                                    <option value="" >SIN ASIGNAR</option>
                                   <option value="PROPIO" >PROPIO</option>
                                   <option value="TERCEROS" >DE TERCEROS</option>
                            
                                 </select>
                               </div>
                               </div>
                            
                               <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                 <label>Fecha de entrega</label>
                                 <div class="input-group mb-4">
                            
                                   <input disabled type="date" wire:model="fecha" class="form-control" placeholder="Click para elegir">
                            
                            
                                     </div>
                            
                               </div>
                               </div>
                            
                            <div class="col-sm-12 col-md-6">
                             <div class="form-group">
                              <label>Turno</label>
                              <select disabled wire:model='turno' class="form-control">
                                <option value="Elegir" disabled >Elegir</option>
                                <option value="" >SIN ASIGNAR</option>
                                <option value="MAÑANA" >MAÑANA</option>
                                <option value="TARDE" >TARDE</option>
                            
                              </select>
                            </div>
                            </div>

                            </div>
                            
							<div class="row">
                            <div class="col-sm-12 col-md-12 mb-3">
                            </div>
                            <label>Detalle de ventas asociadas</label>
							     <div class="col-12">
							    <div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										 	<th>Nro Venta</th>
											<th>Fecha de venta</th>
											<th>Cliente</th>
											<th>Total</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($ventas_hoja_ruta as $venta)
									    <tr>
									        <td>{{$venta->nro_venta}}</td>
									        <td>{{$venta->created_at}}</td>
									        <td>{{$venta->nombre_cliente}}</td>
									        <td>$ {{ number_format($venta->total,2)  }}</td>
									    </tr>
									    @endforeach
        							</tbody>
        							</table>
							    </div> 
							    </div>    
							         
							         
							     
							    
							</div>
							
						</div>
					</div>