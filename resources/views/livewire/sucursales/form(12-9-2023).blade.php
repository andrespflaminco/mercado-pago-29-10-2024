@include('common.modalHead')


<div class="row">

	<div class="row">
			<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
					<div class="general-info">
							<div style="border-bottom: solid 1px #eee;" class="info">
									<h6 class="">Informacion general</h6>

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
																				<label >Plan a usar</label>
																				<select wire:model.lazy="plan" class="form-control">
																					<option value="1" selected>Plan basico</option>
																					<option value="2" selected>Plan intermedio</option>
																					<option value="3" selected>Plan avanzado</option>
																				</select>
																				@error('plan') <span class="text-danger er">{{ $message}}</span>@enderror
																			</div>
																		</div>
																		<div class="col-sm-12 col-md-6">
																			<div class="form-group">
																				<label >Provincia</label>
																				<select type="text" wire:model.lazy="id_provincia"                                                                   class="form-control" >
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
																				@error('ciudad') <span class="text-danger er">{{ $message}}</span>@enderror
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
								<h6 class="">Datos de facturacion</h6>
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
																@error('iibb') <span class="text-danger er">{{ $message}}</span>@enderror
															</div>
														</div>

													</div>
											</div>
									</div>
							</div>
					</div>
			</div>

			<br>


	</div>


</div>



@include('common.modalFooter')
