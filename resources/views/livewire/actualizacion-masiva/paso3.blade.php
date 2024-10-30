
                                            <div style="{{$paso3}}">
                                                
                                                
                                                <div class="mb-4">
                                                <h5>Actualizacion</h5>
                                                </div>
                                                <div class="row">                  
                            			    
                            			        <!----- ELEGIR ------>
                            					<div class="col col-lg-6 col-sm-12">
                            					<div class="form-group">
                            					    <label>Actualizar</label>
                            					    <select class="form-control" wire:model="elegir_actualizar">
                            					        <option value="0">Elegir</option>
                            					        <option value="1">Precios</option>
                            					        <option value="2">Precio interno</option>
                            					        <option value="3">Costos</option>
                            					    </select>
                            					</div>	    
                            					</div>
                            					<div class="col col-lg-6 col-sm-12"></div>
                            				    <!------/ELEGIR ------>
                            				    
                            				    
                            				    @if($elegir_actualizar == 1)
                            				    <!----- LISTA DE PRECIOS ------>
                            					<div class="col col-lg-6 col-sm-12">
                            					<div class="form-group">
                            					    <label>Lista de precios</label>
                            					    <select class="form-control" wire:model.defer="lista_id">
                            					        <option value="all">Todos</option>
                            					        <option value="0">Precio base</option>
                            					        @foreach($lista_precios as $lp)
                            					        <option value="{{$lp->id}}">{{$lp->nombre}}</option>
                            					        @endforeach
                            					    </select>
                            					</div>	    
                            					</div>
                            					<div class="col col-lg-6 col-sm-12"></div>
                            				     @endif
                            				    <!------/LISTA DE PRECIOS ------>
                            				    
                            				    
                            					<div class="col col-lg-6 col-sm-12">
                            					@if($elegir_actualizar != 0)    
                            					<div class="row">
                            					    <label>Actualizar por %</label>
                            					    <div class="col-6">
    					        						<div class="input-group input-group-md mb-0" style="width:100%;">
                                						<input type="text" class="form-control" wire:model.defer="numero_actualizar">
                                						<div class="input-group-append">
                                						<span class="input-group-text" style="background-color: #e9ecef; color: #212529; border: 1px solid #ced4da;">
                                						 %
                                						</span>
                                						</div>
                                						</div>
                                						@error('numero_actualizar') <span class="text-danger er">{{ $message }}</span> @enderror
                            					    </div>
                            					    <div class="col-6">
                                					    <div class="form-group">
                                					    <select class="form-control" wire:model.defer="redondeo_actualizar">
                                					        <option value="1">Redondear para arriba</option>
                                					        <option value="2">Redondear para abajo</option>
                                					    </select>
                                					    </div>                            					        
                            					    </div>
                            					</div>
                            					@endif
	    
                            					</div>
                            					<div class="col col-lg-6 col-sm-12"></div>
                            					
                            				    </div>
                            				    
                            				    @if($elegir_actualizar != 0)
                                                <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                    <li class="next"><a class="btn btn-submit" onclick="ConfirmarActualizar(1)">ACTUALIZAR <i class="bx bx-chevron-right ms-1"></i></a></li>
                                                </ul>
                                                @endif
                                                
                                                <div>


                                                
                                            </div>
                                        </div>