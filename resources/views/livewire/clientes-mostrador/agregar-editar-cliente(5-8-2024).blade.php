	                <div class="page-header">
					<div class="page-title">
					    @if($selected_id < 1)
							<h4>Agregar cliente</h4>
							<h6>Agregue un nuevo cliente</h6>
						@else
						    <h4>Editar cliente</h4>
							<h6>Edite el cliente</h6>
						@endif
						</div>
						<div class="page-btn">
						</div>
					</div>
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Cod del cliente</label>
									   <input maxlength="11" type="text" wire:model.lazy="id_cliente" class="form-control" >
									   <p style="color:#637381;">* Maximo 11 digitos</p> 
                                       @error('id_cliente') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre del cliente</label>
									   <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Juan Perez" >
                                       @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                            		<label>Telefono</label>
                            		<input type="text" wire:model.lazy="telefono" class="form-control" placeholder="ej: 351 115 9550" maxlength="10" >
	                            	@error('telefono') <span class="text-danger er">{{ $message}}</span>@enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                            		<label >Email</label>
                            		<input type="text" wire:model.lazy="email"
                            		class="form-control" placeholder="ej: juanperez@gmail.com"  >
                            		@error('email') <span class="text-danger er">{{ $message}}</span>@enderror
									</div>
								</div>
									
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                        			<label >CUIT </label>
                        		    <input maxlength="11" type="text" wire:model.lazy="dni" class="form-control" placeholder="">
                        		    <p style="color:#637381;">* Maximo 11 digitos</p> 
                        		    @error('dni') <span class="text-danger er">{{ $message}}</span>@enderror
								    </div>
								   
								</div>
																
								<div class="col-lg-4 col-sm-6 col-12">
										<div class="form-group">
                                		<label >Pais</label>
                                		<select class="form-control" wire:model.lazy="pais">
                                		    @foreach($paises as $p)
                                		    <option value="{{$p->nombre}}">{{$p->nombre}}</option>
                                		    @endforeach
                                		</select>
                                		@error('pais') <span class="text-danger er">{{ $message}}</span>@enderror
                                	</div>
								</div>	
								
								<div class="col-lg-4 col-sm-6 col-12">
										<div class="form-group">
                                		<label >Provincia</label>
                                		<select class="form-control" wire:model.lazy="provincia">
                                		    <option value="">Elegir</option>
                                		    @foreach($provincias as $prov)
                                		    <option value="{{$prov->provincia}}">{{$prov->provincia}}</option>
                                		    @endforeach
                                		</select>
                                		@error('provincia') <span class="text-danger er">{{ $message}}</span>@enderror
                                	</div>
								</div>	
																
																
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                            		<label >Ciudad</label>
                            		<input type="text" wire:model.lazy="localidad"
                            		class="form-control" placeholder="ej: Cordoba"  >
                            		@error('localidad') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
										<div class="form-group">
                                		<label >Barrio</label>
                                		<input type="text" wire:model.lazy="barrio"
                                		class="form-control" placeholder="ej: Nueva Cordoba"  >
                                		@error('barrio') <span class="text-danger er">{{ $message}}</span>@enderror
                                	</div>
								</div>
								
																
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Calle</label>
                            		<input type="text" wire:model.lazy="calle"
                            		class="form-control" placeholder="ej: Independencia"  >
                            		@error('calle') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
																								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Altura</label>
                            		<input type="text" wire:model.lazy="altura"
                            		class="form-control" placeholder="ej: 105"  >
                            		@error('altura') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
																								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Piso</label>
                            		<input type="text" wire:model.lazy="piso"
                            		class="form-control" placeholder="ej: 6"  >
                            		@error('piso') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Depto</label>
                            		<input type="text" wire:model.lazy="depto"
                            		class="form-control" placeholder="ej: A"  >
                            		@error('depto') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Cod postal</label>
                            		<input type="text" wire:model.lazy="codigo_postal"
                            		class="form-control" placeholder="ej: 5000"  >
                            		@error('codigo_postal') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
						
								@if($lista_precios != null)

                            	<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Lista de precios del cliente</label>
                            		<select wire:model.lazy="lista_precio_cliente" class="form-control">
                            			<option value="Elegir" selected>Elegir</option>
                            			<option value="0"> Precio base </option>
                            			@foreach($lista_precios as $lp)
                            			<option value="{{$lp->id}}">{{$lp->nombre}}</option>
                            			@endforeach
                            		</select>
                            		@error('lista_precio_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
                                </div>
                            
                            @endif
								
                            <div class="col-lg-4 col-sm-6 col-12">
                          		<div class="form-group">
                                    <label>Sucursal:</label>
                                    <select class="form-control" {{auth()->user()->sucursal == 1 ? 'disabled' : '' }} wire:model.lazy="sucursal_agregar">
                                      <option value="{{$comercio_id}}"> {{auth()->user()->name}} </option>
                                      @foreach($sucursales as $item)
                                      <option value="{{$item->sucursal_id}}">{{$item->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                          
                            </div>
                            
                            @if($wc_customer_id != null)
                            <div class="col-lg-4 col-sm-6 col-12">
                          		<div class="form-group">
                                    <label>Wocommerce cliente ID:</label>
                                  <input class="form-control" wire:model="wc_customer_id" disabled>
                                  </div>
                          
                            </div>
                            @endif
                            
                            
							<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Recontacto luego de la ultima compra</label>
									   <div class="input-group mb-0">				
                    				   <input maxlength="11" type="number" wire:model.lazy="recontacto" class="form-control" >
					                    <div class="input-group-prepend">
                                            <span style="height: 100% !important;" class="input-group-text input-gp">
                                                DIAS
                                            </span> 
                                        </div>
                    				</div>
                    				
                                       @error('recontacto') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
							</div>
							
							<h6><b>Cuenta corriente:</b></h6>
                            <div class="col-12 row mt-3" style="margin-left: 0px !important; border: solid 1px #eee; padding-top:15px;">
                                							
							<div class="col-lg-3 col-sm-6 col-12">
							
									<div class="form-group">
										<label>Plazo cuenta corriente</label>
									   <div class="input-group mb-0">				
                    				   <input maxlength="11" type="number" wire:model.lazy="plazo_cuenta_corriente" class="form-control" >
					                    <div class="input-group-prepend">
                                            <span style="height: 100% !important;" class="input-group-text input-gp">
                                                DIAS
                                            </span> 
                                        </div>
                    				</div>
                    				
                                       @error('plazo_cuenta_corriente') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
							</div>
							
							<div class="col-lg-3 col-sm-6 col-12">
							
									<div class="form-group">
										<label>Monto maximo de la cuenta corriente</label>
									   <div class="input-group mb-0">				
					                    <div class="input-group-prepend">
                                            <span style="height: 100% !important;" class="input-group-text input-gp">
                                                $
                                            </span> 
                                        </div>
                    				   <input  type="number" wire:model.lazy="monto_maximo_cuenta_corriente" class="form-control" >
                    				</div>
                    				
                                       @error('monto_maximo_cuenta_corriente') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
							</div>
							
							
							<div class="col-lg-3 col-sm-6 col-12">
							<h6></h6>
									<div class="form-group">
										<label>Saldo inicial en cuenta corriente</label>
									   <div class="input-group mb-0">				
                    				    <div class="input-group-prepend">
                                            <span style="height: 100% !important;" class="input-group-text input-gp">
                                                $
                                            </span> 
                                        </div>
                    				   <input maxlength="11" type="number" wire:model.lazy="saldo_inicial_cuenta_corriente" class="form-control" >
                    				</div>
                    				
                                       @error('saldo_inicial_cuenta_corriente') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
							</div>
							
							<div class="col-lg-3 col-sm-6 col-12">
							<h6></h6>
									<div class="form-group">
										<label>Fecha de saldo inicial</label>
									   <div class="input-group mb-0">				
                    				   <input maxlength="11" type="date" wire:model.lazy="fecha_inicial_cuenta_corriente" class="form-control" >
					         		</div>
                                       @error('fecha_inicial_cuenta_corriente') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
							</div>
							
							
                            </div>

                            
                                
                                
                        <div class="col-lg-4 col-sm-6 col-12">
                        </div>
                        
                        <div class="col-sm-12 col-md-12">
                        	<div class="form-group">
                        		<label >Observaciones</label>
                        		<textarea class="form-control" wire:model="observaciones" style="width:100%;">
                        		    
                        		</textarea>
                        
                        	</div>
                        </div>
                                								
                                								
							
								<div class="col-lg-12">
								      <a wire:click.prevent="resetUI()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
								      @if($selected_id < 1)
								      <a href="javascript:void(0);" wire:click.prevent="Store()" class="btn btn-submit me-2">GUARDAR</a>
                                       @else
                                       <a wire:click.prevent="Update()" href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                       @endif
                                       
									
									
								</div>
							</div>
						</div>
					</div>
					<!-- /add -->
