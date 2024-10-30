	<div class="card mb-0"  @if(!$mostrarFiltros) hidden @endif >
								<div class="card-body pb-0">
									<div class="row">
										<div class="col-lg-12 col-sm-12">
											<div class="row">
										
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
													<label>Categoria</label>
														<select wire:model='id_categoria' class="form-control">
														<option value="0" >Todas las categorias</option>
														<option value="1" >Sin categoria</option>
														@foreach ($categories as $cat)
            											<option value="{{$cat->id}}" >{{$cat->name}}</option>
            											@endforeach
														</select>
													</div>
												</div>
												
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
													<label>Marcas</label>
														<select wire:model='id_marca' class="form-control">
														<option value="0" >Todas las marcas</option>
														<option value="1" >Sin marca</option>
														@foreach ($marcas as $marc)
            											<option value="{{$marc->id}}" >{{$marc->name}}</option>
            											@endforeach
														</select>
													</div>
												</div>
												
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
														<label>Etiquetas</label>
                                    					    <div wire:ignore>
                                                            <select class="form-control tagging"  multiple="multiple" id="select2-buscar-etiquetas">
                                                                <option value="" >Sin etiqueta</option>
                                                         
                                                            </select>
                                                            </div>
                                  
													</div>
												</div>
												
												@can('ver proveedores en catalogo') 
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
													    <label>Proveedor</label>
														<select wire:model='proveedor_elegido' class="form-control">
														<option value="0" >Todos los proveedores</option>
														<option value="1" >Sin proveedor</option>
                            							@foreach($prov as $pr)
                            							<option value="{{$pr->id}}">{{$pr->nombre}}</option>
                            							@endforeach
														</select>
													</div>
												</div>
												@endcan
												
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
													<label>Tipo</label>
														<select wire:model='es_insumo_elegido' class="form-control">
														<option value="0" >Todos</option>
														<option value="2" >Producto</option>
														<option value="1" >Insumo</option>
														</select>
													</div>
												</div>
												
												<div class="col-lg-1 col-sm-6 col-12">
													<div class="form-group">
													    <label style="margin-top: 28px !important;"></label>
													    <button class="btn btn-light" wire:click="LimpiarFiltros()" >
													     LIMPIAR 
													    </button>
													</div>
												</div>
												
								
												
												<div class="col-lg col-sm-6 col-12 ">
													<div class="form-group">
													<label style="margin-top: 28px !important;"></label>
													    <button class="btn btn-added" wire:click="SeleccionarEnLoteFiltrado()" >
													     SELECCIONAR EN LOTE PRODUCTOS FILTRADOS
													    </button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
    @include('common.accion-lote')