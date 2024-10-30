<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }} </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
									<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
					<div class="general-info">
							<div style="border-bottom: solid 1px #eee;" class="info">
							    <br>
									<h6 class="">Informacion general</h6>
                                <br>
									<div class="row">
											<div class="col-lg-11 mx-auto">
													<div class="row">

															<div class="col-xl-12 col-lg-12 col-md-8 mt-md-0 mt-4">
																	<div class="form">
																		<div class="row">

																		<div class="col-sm-12 col-md-8">
																			<div class="form-group">
																				<label >Nombre del comercio</label>
																				<input type="text" wire:model.lazy="name"
																				class="form-control" placeholder="ej: Las gaviotas" >
																				@error('name') <span class="text-danger er">{{ $message}}</span>@enderror
																			</div>
																		</div>
																		<div class="col-sm-12 col-md-4">
																			<div class="form-group">
																				<label >Teléfono</label>
																				<input type="text" wire:model.lazy="phone"
																				class="form-control" placeholder="ej: 351 115 9550" maxlength="10" >
																				@error('phone') <span class="text-danger er">{{ $message}}</span>@enderror
																			</div>
																		</div>
																		<div class="col-sm-12 col-md-6">
																			<div class="form-group">
																				<label >Email</label>
																				<input type="text" wire:model.lazy="email"
																				class="form-control" placeholder="ej: lasgaviotas@gmail.com"  >
																				@error('email') <span class="text-danger er">{{ $message}}</span>@enderror
																			</div>
																		</div>
																		<div class="col-sm-12 col-md-6">
																			<div class="form-group">
																				<label >Contraseña</label>
																				<input type="password" wire:model.lazy="password"
																				class="form-control"   >
																				@error('password') <span class="text-danger er">{{ $message}}</span>@enderror
																			</div>
																		</div>
																		<div class="col-sm-12 col-md-6">
																			<div class="form-group">
																				<label >Provincia</label>
																				
																				<select type="text" wire:model.lazy="id_provincia" class="form-control">
																				    <option value="Elegir">Elegir</option>                                                                  class="form-control" >
																				@foreach($provincias as $p)
																				<option value="{{$p->id}}">{{$p->provincia}}</option>
																				@endforeach
																				</select>
																				@error('provincia') <span class="text-danger er">{{ $message}}</span>@enderror
																			</div>
																		</div>


																		<div class="col-sm-12 col-md-6">
																			<div class="form-group">
																				<label >Ciudad</label>
																				<input type="text" wire:model.lazy="ciudad"                                                                  class="form-control" >
																				@error('ciudad') <span class="text-danger er">{{ $message}}</span>@enderror
				 															</div>
																		</div>
																		<div class="col-sm-12 col-md-6">
																			<div class="form-group">
																				<label >Domicilio</label>
																				<input type="text" wire:model.lazy="domicilio_comercial"                                                                   class="form-control" placeholder="ej: Independencia 210"  >
																				@error('domicilio_comercial') <span class="text-danger er">{{ $message}}</span>@enderror
																			</div>
																		</div>
																		
																		<div class="col-sm-12 col-md-6">
																			<div class="form-group">
																				<label >Tipo</label>
                                                                            <select class="form-control" wire:model="tipo_local">
                                                                                <option value="Sucursal propia">Sucursal propia</option>
                                                                                <option value="Franquicia">Franquicia</option>
                                                                            </select>
																			</div>
																		</div>
																	
																	</div>
																	</div>
															</div>
													</div>
											</div>
									</div>

							</div>

			</div>

			<br>
			<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
					<div  class="general-info">
							<div style="border-bottom: solid 1px #eee;" class="info">
								<br>
								<h6 class="">Datos de facturacion</h6>
								<br>
									<div class="row">
											<div class="col-md-11 mx-auto">
													<div class="row">

														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Razon social</label>
																<input type="text" wire:model.lazy="razon_social"
																class="form-control" placeholder=""  >
																@error('razon_social') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >CUIT</label>
																<input type="text" wire:model.lazy="cuit"
																class="form-control" placeholder=""  >
																@error('cuit') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Condicion ante el IVA</label>
																<select wire:model.lazy="condicion_iva" class="form-control">
																	<option value="Elegir" selected>Elegir</option>
																	<option value="IVA Responsable inscripto" >IVA Responsable inscripto</option>
																	<option value="IVA exento" >IVA exento</option>
																	<option value="Monotributo" >Monotributo</option>

																</select>
																@error('condicion_iva') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Ingresos brutos</label>
																<input type="text" wire:model.lazy="iibb"
																class="form-control" placeholder=""  >
																@error('iibb') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Punto de venta</label>
																<input type="text" wire:model.lazy="pto_venta"
																class="form-control" placeholder=""  >
																@error('pto_venta') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Fecha de inicio de actividades</label>
																<input type="date" wire:model.lazy="fecha_inicio_actividades"
																class="form-control" >
																@error('fecha_inicio_actividades') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >IVA por defecto</label>
																<select wire:model.lazy="iva_defecto"
																class="form-control">
																		<option value="0">Sin IVA</option>
																		<option value="0.105">10,5%</option>
																		<option value="0.21">21%</option>
																		<option value="0.27">27%</option>
																</select>
																@error('iva_defecto') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>
														
														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Relacion precio IVA</label>
																<select wire:model.lazy="relacion_precio_iva"
																class="form-control">
																		<option value="0">Sin IVA</option>
																		<option value="1">Precio + IVA</option>
																		<option value="2">IVA incluido en el precio</option>
																</select>
																@error('relacion_precio_iva') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

													</div>
											</div>
									</div>
							</div>
					</div>
			</div>

			<br>
			
			<br>
			<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
					<div  class="general-info">
							<div style="border-bottom: solid 1px #eee;" class="info">
								<br>
								<h6 class="">Precios:</h6>
								<br>
									<div class="row">
											<div class="col-md-11 mx-auto">
													<div class="row">

                                                        
														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Lista de precio para la Compra a la casa central</label>
																<select wire:model.lazy="lista_precios_id" class="form-control">
																	<option value="1">Precio interno</option>
																	@foreach($lista_precios as $lpr)
																	<option value="{{$lpr->id}}" >{{$lpr->nombre}}</option>
																	@endforeach
																</select>
																@error("lista_precios_id") <!-- Utiliza el nombre del campo específico -->
                                                                <span class="text-danger er">{{ $message }}</span>
                                                                @enderror
															</div>
														</div>
														
                                                        
														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Lista de precio para la Venta</label>
																<select wire:model.lazy="lista_defecto" class="form-control">
																	<option value="0">Precio base</option>
																	@foreach($lista_precios as $lpr)
																	<option value="{{$lpr->id}}" >{{$lpr->nombre}}</option>
																	@endforeach
																</select>
																@error("lista_defecto") <!-- Utiliza el nombre del campo específico -->
                                                                <span class="text-danger er">{{ $message }}</span>
                                                                @enderror
															</div>
														</div>
													</div>
											</div>
									</div>
							</div>
					</div>
			</div>
			
			<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
					<div  class="general-info">
							<div style="border-bottom: solid 1px #eee;" class="info">
								<br>
								<h6 class="">Clientes y Proveedores:</h6>
								<br>
									<div class="row">
											<div class="col-md-11 mx-auto">
													<div class="row">

                                                        
														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label>Listado de clientes</label>
																<select wire:model.lazy="ver_listado_clientes" class="form-control">
																	<option value="0">ver listado de toda la cadena</option>
																	<option value="1">Ver solo los clientes de la sucursal</option>
																</select>
																@error("lista_precios_id") <!-- Utiliza el nombre del campo específico -->
                                                                <span class="text-danger er">{{ $message }}</span>
                                                                @enderror
															</div>
														</div>
														
                                                        
														<div hidden class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >Lista de precio para la Venta</label>
																
															</div>
														</div>
													</div>
											</div>
									</div>
							</div>
					</div>
			</div>
			<br>
						<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
					<div  class="general-info">
							<div style="border-bottom: solid 1px #eee;" class="info">
								<br>
								<h6 class="">Permisos</h6>
								<br>
									<div class="row">
											<div class="col-md-11 mx-auto">
													<div class="row">

                                                        @foreach($lista_permisos as $lp)
														<div class="col-sm-12 col-md-6">
															<div class="form-group">
																<label >{{$lp->nombre}}</label>
																<select wire:model.lazy="permiso.{{$lp->id}}" class="form-control">
																    <option value="Elegir">Elegir</option>
																	<option value="0">No</option>
																	<option value="1" >Si</option>
																</select>
																@error("permiso.{$lp->id}") <!-- Utiliza el nombre del campo específico -->
                                                                <span class="text-danger er">{{ $message }}</span>
                                                                @enderror
															</div>
														</div>
														@endforeach

													</div>
											</div>
									</div>
							</div>
					</div>
			</div>
			
			

			<br>


	</div>
						</div>
						<div class="col-lg-12">
							
							<a class="btn btn-cancel" wire:click.prevent="resetUI()"  data-bs-dismiss="modal">Cancelar</a>
							 @if($selected_id < 1)
							 <a class="btn btn-submit me-2" wire:click.prevent="Store()" >Guardar</a>
                             @else
                             <a class="btn btn-submit me-2" wire:click.prevent="Update()" >Actualizar</a>
                             @endif
						</div>
					</div>
				</div>
			</div>
		</div>

