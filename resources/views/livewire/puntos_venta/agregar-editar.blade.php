
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <div  class="section contact">
                                        <div class="info">
                                            <h5 class="">Datos de facturacion</h5>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12 mx-auto">
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
                                                      		<label >Domicilio</label>
                                                      		<input type="text" wire:model.lazy="domicilio_comercial"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('domicilio_comercial') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Ciudad</label>
                                                      		<input type="text" wire:model.lazy="ciudad"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('ciudad') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Provincia</label>
                                                      		<select class="form-control" wire:model="id_provincia">
                                                      		@foreach($provincias as $provincia)
                                                      		<option value="{{$provincia->id}}">{{$provincia->provincia}}</option>
                                                      		@endforeach
                                                      		</select>
                                                      		@error('id_provincia') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>


                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >CUIT</label>
                                                      		<input type="text" maxlength="11" wire:model.lazy="cuit"
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
                                                      		<label >Relacion Precio -> Iva</label>
                                                      		<select wire:model.lazy="relacion_precio_iva"
                                                      		class="form-control">
                                                      		  <option value="0">Sin IVA</option>
                                                              <option value="1">IVA + Precio</option>
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
