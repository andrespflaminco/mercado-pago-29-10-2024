	                <div class="page-header">
					<div class="page-title">
						    <h4>Editar punto de venta</h4>
							<h6>Edite el punto de venta</h6>
						</div>
						<div class="page-btn">
						</div>
					</div>
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
					
								<p>{{$nombre_comercio_a_agregar}}</p>

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

								<div class="col-lg-6 col-sm-6 col-12">
								<div class="form-group">
								<label>Habilitado en AFIP</label>
								<select class="form-control" wire:model="habilitado_afip">
								    <option value="0">NO</option>
								    <option value="1">SI</option>
								</select>
								</div>
								</div>
								
							  <div class="col-lg-12">
								    
                                      <a wire:click.prevent="resetUI()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
								      @if($selected_id < 1)
								      
								      @if($nombre_comercio_a_agregar != null)
								      <a href="javascript:void(0);" wire:click.prevent="StoreMostrador()" class="btn btn-submit me-2">GUARDAR</a>
								      @else
								      <a href="javascript:void(0);" wire:click.prevent="Store()" class="btn btn-submit me-2">GUARDAR</a>
								      @endif
								      
                                      @else
                                      <a wire:click.prevent="Update()" href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                      @endif
									
									
								</div>
							</div>
						</div>
					</div>
					<!-- /add -->
