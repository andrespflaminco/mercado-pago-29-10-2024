
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12 col-sm-12 col-12">
									<div class="form-group">
										<label>Nombre de la Subcategoria</label>
										<input type="text" wire:model.defer="name" class="form-control" placeholder="" maxlength="255">
										@error('name') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-12 col-sm-12 col-12">
									<div class="form-group">
										<label>Categoria a la que pertenece</label>
									    <select wire:model="categoria_id" class="form-control">
									        <option value="Elegir">Elegir</option>
									        @foreach($categorias as $categoria)
									        <option value="{{$categoria->id}}">{{$categoria->name}}</option>
									        @endforeach
									    </select>
									    @error('categoria_id') <span class="text-danger er">{{ $message }}</span> @enderror
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
