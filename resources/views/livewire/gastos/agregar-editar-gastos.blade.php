	                <div class="page-header">
					<div class="page-title">
					    @if($selected_id < 1)
							<h4>Agregar gasto</h4>
							<h6>Agregue un nuevo gasto</h6>
						@else
						    <h4>Editar gasto</h4>
							<h6>Edite el gasto</h6>
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
										<label>Nombre del gasto</label>
									   <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Alquiler octubre 2021" >
                                       @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Fecha</label>
									     <input type="date" wire:model.lazy="fecha_gasto" class="form-control" >
                                         @error('fecha_gasto') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								<div class="col-sm-12 col-md-4">
                                <div class="form-group">
									<label>Caja</label>        
                                <div style="width:100%;" class="btn-group  mb-4 mr-2">
                                    
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if($caja == null)
                                
                                    <b style="color:red;"> Sin caja seleccionada. </b>
                                   
                                    @else
                                    <b style="color:green;"> Caja seleccionada: # {{$caja_seleccionada->nro_caja}} </b>
                                    @endif
                                     </button>
                                                   <div class="dropdown-menu">
                                                    
                                                    @if($caja_abierta == null)
                                                    @if($caja == null)
                                                    <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Abrir caja</p>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ModalAbrirCaja()">+ NUEVA CAJA </a>
                                
                                                    @endif
                                                    @endif
                                                    <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Ultimas cajas</p>
                                                      
                                                     <div class="dropdown-divider"></div>
                                                     @foreach($ultimas_cajas as $uc)
                                                   <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ElegirCaja({{$uc->id}})">Caja # {{$uc->nro_caja}} ( {{\Carbon\Carbon::parse($uc->created_at)->format('d/m/Y')}} )</a>
                                                    @endforeach
                                                     <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="SinCaja()"> SIN CAJA </a>
                                
                                
                                                   <div class="dropdown-divider"></div>
                                                   
                                                    <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja por fecha</p>
                                                   <div class="dropdown-divider"></div>
                                                      <input type="date" wire:change="CambioCaja()" wire:model="fecha_ap"  class="form-control " >
                                                   
                                                   </div>
                                                   </div>
                            
                                </div>    
                            </div>
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Categoria</label>
									    <select wire:model='categoria' wire:change.defer='ModalCategoria($event.target.value)'  class="form-control">
                                        <option value="Elegir" disabled >Elegir</option>
                                        <option value="1" >Sin categoria</option>
                                        @foreach($gastos_categoria as $gc)
                                          <option value="{{$gc->id}}" >{{$gc->nombre}}</option>
                                        @endforeach
                                        <option value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVA CATEGORIA</option>
                                      </select>
                                      @error('categoria') <span class="text-danger err">{{ $message }}</span> @enderror
									</div>
								</div>

								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
										<label>Proveedor</label>
									    <select wire:model='proveedor' wire:change.defer='ModalProveedor($event.target.value)'  class="form-control">
                                        <option value="Elegir" disabled >Elegir</option>
                                        <option value="1" >Sin proveedor</option>
                                        @foreach($gastos_proveedor as $gp)
                                          <option value="{{$gp->id}}" >{{$gp->nombre}}</option>
                                        @endforeach
                                        <option hidden value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVO PROVEEDOR</option>
                                      </select>
                                      @error('proveedor') <span class="text-danger err">{{ $message }}</span> @enderror
									</div>
								</div>

								<div class="col-lg-4 col-sm-6 col-12">
									
									<div class="form-group">
									<label>Etiquetas</label>
									
									<div wire:ignore>
                                    <select id="select2-etiquetas" class="form-control tagging" multiple="multiple">
                                    </select>
                                </div>
                                
                          
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Monto</label>
									  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                                        <div class="input-group-prepend">
                                          <span style="height:100%;" class="input-group-text input-gp">
                                            $
                                          </span>
                                        </div>
                                        <input type="text" wire:change="CambiarMonto()" wire:keyup.enter="CambiarMonto()"  wire:model.lazy="monto" class="form-control" placeholder="Ej: 10" >
                                          </div>
                                          @error('monto') <span class="text-danger err">{{ $message }}</span> @enderror
								</div>
								</div>
							
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>IVA</label>
									  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                                        <select wire:change="CambiarIVA()" class="form-control" wire:model="iva">
                                            <option value="0">Sin iva</option>
                                            <option value="0.105">10,5%</option>
                                            <option value="0.210">21%</option>
                                            <option value="0.270">27%</option>
                                        </select>                                    
                                      </div>
                                      @error('iva') <span class="text-danger err">{{ $message }}</span> @enderror
								</div>
								</div>
									
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Monto con IVA</label>
									  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                                        <div class="input-group-prepend">
                                          <span style="height:100%;" class="input-group-text input-gp">
                                            $
                                          </span>
                                        </div>
                                        <input type="text" wire:change="CambiarMontoConIVA()" wire:keyup.enter="CambiarMontoConIVA()" wire:model.lazy="monto_con_iva" class="form-control" placeholder="Ej: 10" >
                                          </div>
                                          @error('monto_con_iva') <span class="text-danger err">{{ $message }}</span> @enderror
								</div>
								</div>
								
								
								@include('livewire.gastos.metodo-pago')                              								
                                								
                                <br><br>
							
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
