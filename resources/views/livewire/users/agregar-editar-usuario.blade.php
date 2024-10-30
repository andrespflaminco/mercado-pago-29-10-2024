	                <div class="page-header">
					<div class="page-title">
					    @if($selected_id < 1)
							<h4>Agregar usuario</h4>
							<h6>Agregue un nuevo usuario</h6>
						@else
						    <h4>Editar usuario</h4>
							<h6>Edite el usuario</h6>
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
								<label>Nombre y apellido</label>
								<input type="text" wire:model.lazy="name"
                                class="form-control" placeholder="ej: Luis Gomez"  >
                                @error('name') <span class="text-danger er">{{ $message}}</span>@enderror
								</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Teléfono</label>
                            		<input type="text" wire:model.lazy="phone"
                            		class="form-control" placeholder="ej: 351 115 9550" maxlength="10" >
                            		@error('phone') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Email</label>
                            		<input type="text" wire:model.lazy="email"
                            		class="form-control" placeholder="ej: luisfaax@gmail.com"  >
                            		@error('email') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
									
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Contraseña</label>
                            		<input type="password" wire:model.lazy="password"
                            		class="form-control"   >
                            		@error('password') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
							
																
								<div class="col-lg-4 col-sm-6 col-12">
                                	<div class="form-group">
                                		<label >Estado</label>
                                		<select wire:model.lazy="status" class="form-control">
                                			<option value="Elegir" selected>Elegir</option>
                                			<option value="Activo">Activo</option>
                                			<option value="Inactivo">Inactivo</option>
                                		</select>
                                		@error('status') <span class="text-danger er">{{ $message}}</span>@enderror
                                	</div>
								</div>
							
							    @if(auth()->user()->id === 1)
								
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Estado de pago</label>
                            		<select wire:model.lazy="confirmed" class="form-control">
                            			<option value="0">Elegir</option>
                            			<option value="1">Pago</option>
                            			<option value="0">No pago</option>
                            		</select>
                            		@error('estado_pago') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
																
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Plan a usar</label>
                            		<select wire:model.lazy="plan_admin" class="form-control">
                            			<option value="1">Plan basico</option>
                            			<option value="2">Plan intermedio</option>
                            			<option value="3">Plan avanzado</option>
                            			<option value="4">Plan premium</option>
                            		</select>
                            		@error('plan') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Validacion de email</label>
                            		<select wire:model.lazy="validar_email" class="form-control">
                            			<option value="0">No validado</option>
                            			<option value="1">Validado</option>
                            		</select>
                            		@error('plan') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								
								@endif
								
																
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Asignar Rol</label>
                            		
                            		@if( ($profile == "Comercio" || $profile == "Franquicia") && (1 < $selected_id) ) 
                            		<select wire:model.lazy="profile" class="form-control" class="form-control">
                            			<option value="Comercio">Comercio</option>
                            			<option value="Franquicia">Franquicia</option>
                            		</select>
                            		@else
                            			
                            			
                            		<select wire:model.lazy="profile" class="form-control" @if($profile == "Comercio" && (1 < $selected_id) ) disabled @endif class="form-control">
                            			<option value="Elegir" selected>Elegir</option>
                            		    
                            		    <option hidden value="Comercio">Comercio</option>
                            		    
                            		    
                            			@foreach($roles as $role)
                            			@if($profile == "Comercio" && $role->name == "Comercio" &&  (1 < $selected_id) ) 
                            			<option value="{{$role->name}}" selected>{{$role->name}}</option>
                            			@endif 
                            		
                            			@if(auth()->user()->profile != "Admin" && $role->name == "Comercio" && $role->name == "Sucursal")
                            			<option hidden value="{{$role->name}}" selected>{{$role->name}}</option>
                            			@else
                            			
                            			@if($role->name != "Sucursal" && $role->name != "Comercio")
                            			<option value="{{$role->name}}" selected>{{$role->name}}</option>
                            			@endif
                            			
                            			@endif
                            			
                            			@endforeach
                            		
                            		</select>
                            		
                            		@endif
                            		
                            		
                            		@if($profile == "Comercio" &&  (1 < $selected_id) ) 
                            		<p style="color: #637381;">No se puede editar el rol de un comercio</p>
                            		@endif 
                            			
                            		@error('profile') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
                                
                                @if($selected_id < 1)
                                 
                                @if(auth()->user()->sucursal != 1)
                                @if(auth()->user()->profile == "Comercio")
								<div class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label >Sucursal</label>
                            		<select wire:model.lazy="sucursal_id" class="form-control">
                            			<option value="Elegir" selected>Elegir</option>
                            			<option value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                            			@foreach($sucursales as $s)
                            			<option value="{{$s->sucursal_id}}">{{$s->name}}</option>
                            			@endforeach
                            		</select>
                            		@error('sucursal_id') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								@endif
								@endif
								
								@endif


                                <div hidden class="col-sm-12 col-md-6">
                                	<div class="form-group">
                                		<label >Imágen de Perfil</label>
                                		<input type="file" wire:model="image" accept="image/x-png, image/jpeg, image/gif" class="form-control">
                                		@error('image') <span class="text-danger er">{{ $message}}</span>@enderror
                                
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
