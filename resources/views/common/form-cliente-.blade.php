<div wire:ignore.self class="modal fade" id="ModalAgregarCliente" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>AGREGAR CLIENTE</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

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
									   <input type="text" wire:model.lazy="nombre_cliente" class="form-control" placeholder="Ej: Juan Perez" >
                                       @error('nombre_cliente') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                            		<label>Telefono</label>
                            		<input type="text" wire:model.lazy="telefono_cliente" class="form-control" placeholder="ej: 351 115 9550" maxlength="10" >
	                            	@error('telefono_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                            		<label >Email</label>
                            		<input type="text" wire:model.lazy="email_cliente"
                            		class="form-control" placeholder="ej: juanperez@gmail.com"  >
                            		@error('email_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
									</div>
								</div>
									
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                        			<label >CUIT </label>
                        		    <input maxlength="11" type="text" wire:model.lazy="dni_cliente" class="form-control" placeholder="">
                        		    <p style="color:#637381;">* Maximo 11 digitos</p> 
                        		    @error('dni_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
								    </div>
								   
								</div>
																
								<div class="col-lg-4 col-sm-6 col-12">
										<div class="form-group">
                                		<label >Pais</label>
                                		<select class="form-control" wire:model.lazy="pais_cliente">
                                		    @foreach($paises as $p)
                                		    <option value="{{$p->nombre}}">{{$p->nombre}}</option>
                                		    @endforeach
                                		</select>
                                		@error('pais_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                                	</div>
								</div>	
								
								<div class="col-lg-4 col-sm-6 col-12">
										<div class="form-group">
                                		<label >Provincia</label>
                                		<select class="form-control" wire:model.lazy="provincia_cliente">
                                		    <option value="">Elegir</option>
                                		    @foreach($provincias as $prov)
                                		    <option value="{{$prov->provincia}}">{{$prov->provincia}}</option>
                                		    @endforeach
                                		</select>
                                		@error('provincia_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                                	</div>
								</div>	
																
																
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
                            		<label >Ciudad</label>
                            		<input type="text" wire:model.lazy="localidad_cliente"
                            		class="form-control" placeholder="ej: Cordoba"  >
                            		@error('localidad_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
										<div class="form-group">
                                		<label >Barrio</label>
                                		<input type="text" wire:model.lazy="barrio_cliente"
                                		class="form-control" placeholder="ej: Nueva Cordoba"  >
                                		@error('barrio_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                                	</div>
								</div>
								
																
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Calle</label>
                            		<input type="text" wire:model.lazy="calle_cliente"
                            		class="form-control" placeholder="ej: Independencia"  >
                            		@error('calle_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
																								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Altura</label>
                            		<input type="text" wire:model.lazy="altura_cliente"
                            		class="form-control" placeholder="ej: 105"  >
                            		@error('altura_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
																								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Piso</label>
                            		<input type="text" wire:model.lazy="piso_cliente"
                            		class="form-control" placeholder="ej: 6"  >
                            		@error('piso_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Depto</label>
                            		<input type="text" wire:model.lazy="depto_cliente"
                            		class="form-control" placeholder="ej: A"  >
                            		@error('depto_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-2 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Cod postal</label>
                            		<input type="text" wire:model.lazy="codigo_postal_cliente"
                            		class="form-control" placeholder="ej: 5000"  >
                            		@error('codigo_postal_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
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
                                    <select class="form-control" {{auth()->user()->sucursal == 1 ? 'disabled' : '' }} wire:model.lazy="sucursal_agregar_cliente">
                                      <option value="{{$comercio_id}}"> {{auth()->user()->name}} </option>
                                      @foreach($sucursales as $item)
                                      <option value="{{$item->sucursal_id}}">{{$item->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                          
                            </div>
                                
                        <div class="col-lg-4 col-sm-6 col-12">
                        </div>
                        
                        <div class="col-sm-12 col-md-12">
                        	<div class="form-group">
                        		<label >Observaciones</label>
                        		<textarea class="form-control" wire:model="observaciones_cliente" style="width:100%;">
                        		    
                        		</textarea>
                        
                        	</div>
                        </div>
  
							</div>
						

     </div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUICliente()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
       <button type="button" wire:click.prevent="StoreCliente()" onclick="ocultarModal()" class="btn btn-submit" >GUARDAR</button>
    
     </div>
   </div>
 </div>
</div>
