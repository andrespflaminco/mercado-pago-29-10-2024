
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Cod proveedor</label>
									  <input maxlength="11" type="text" wire:model.lazy="id_proveedor" class="form-control" placeholder="" >
									  <p style="color:#637381;">* Maximo 11 digitos</p> 
                                         @error('id_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>	
								<div class="col-lg-8 col-sm-6 col-12">
									<div class="form-group">
										<label>Nombre del proveedor</label>
									  <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="" >
                                         @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>

								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>CUIT</label>
									     <input maxlength="11"  type="text" wire:model.lazy="cuit" class="form-control" placeholder="" >
									     <p style="color:#637381;">* Maximo 11 digitos</p> 
                                         @error('cuit') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Telefono</label>
									     <input maxlength="10"  type="text" wire:model.lazy="telefono" class="form-control" placeholder="" >
									     <p style="color:#637381;">* Maximo 10 digitos</p> 
                                         @error('telefono') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Email</label>
									  <input type="mail" wire:model.lazy="mail" class="form-control" placeholder="" >
                                      @error('mail') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Pais</label>
									<select class="form-control" wire:model.lazy="pais">
									    <option value="1">Argentina</option>
									</select>
									@error('pais') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Provincia</label>
									<select class="form-control" wire:model="provincia">
									    <option value="">Elegir</option>
									    @foreach($provincias as $p)
									    <option value="{{$p->provincia}}">{{$p->provincia}}</option>
									    @endforeach
									</select>
									@error('provincia') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								

								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Ciudad</label>
									<input type="text" wire:model.lazy="localidad" class="form-control" placeholder="" >
                                    @error('localidad') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>							

								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Calle</label>
									<input type="text" wire:model.lazy="direccion" class="form-control" placeholder="" >
                                     @error('direccion') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
																
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Altura</label>
									<input type="text" wire:model.lazy="altura" class="form-control" placeholder="" >
                                     @error('altura') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
																
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Piso</label>
									<input type="text" wire:model.lazy="piso" class="form-control" placeholder="" >
                                     @error('piso') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
																
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Departamento</label>
									<input type="text" wire:model.lazy="depto" class="form-control" placeholder="" >
                                     @error('depto') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>			
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Codigo postal</label>
									<input type="text" wire:model.lazy="codigo_postal" class="form-control" placeholder="" >
                                     @error('codigo_postal') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
							
																


							<h6><b>Cuenta corriente:</b></h6>
                            <div class="col-12 row mt-3 mb-3" style="margin-left: 0px !important; border: solid 1px #eee; padding-top:15px;">
                                							
							<div class="col-lg-4 col-sm-6 col-12">
							
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
							
							<div class="col-lg-4 col-sm-6 col-12">
							<h6></h6>
									<div class="form-group">
										<label>Saldo inicial en cuenta corriente</label>
									   <div class="input-group mb-0">				
                    				    <div class="input-group-prepend">
                                            <span style="height: 100% !important;" class="input-group-text input-gp">
                                                $
                                            </span> 
                                        </div>
                    				   <input maxlength="20" type="number" wire:model.lazy="saldo_inicial_cuenta_corriente" class="form-control" >
                    				</div>
                    				
                                       @error('saldo_inicial_cuenta_corriente') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
							</div>
							
							<div class="col-lg-4 col-sm-6 col-12">
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
