<div id="div-mobile" style="display:none;" class="row justify-content-between">

					<div class="col-lg-3 col-md-3 col-sm-3">

						<div class="input-group mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text input-gp">
									<i class="fas fa-search"></i>
								</span>
							</div>
							<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar" class="form-control"
							>
						</div>

					</div>

						<div class="col-lg-3 col-md-3 col-sm-3">

									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text input-gp">
												<i class="fas fa-list"></i>
											</span>
										</div>
										<select wire:model='id_categoria' class="form-control">
											<option value="0" >Todas las Categorias</option>
											@foreach ($categories as $cat)
											<option value="{{$cat->id}}" >{{$cat->name}}</option>

											@endforeach
										</select>

									</div>

								</div>

				<div class="col-lg-3 col-md-3 col-sm-3">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								<i class="fas fa-home"></i>
							</span>
						</div>
						<select wire:model='id_almacen' class="form-control">
							<option value="0" >Todos los Almacenes</option>
							@foreach ($almacenes as $al)
							<option value="{{$al->id}}" >{{$al->nombre}}</option>

							@endforeach
						</select>

					</div>

				</div>

				<div class="col-lg-3 col-md-3 col-sm-3">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								<i class="fas fa-users"></i>
							</span>
						</div>
						<select wire:model='proveedor_elegido' class="form-control">
							<option value="0" >Todos los proveedores</option>
							@foreach($prov as $pr)
							<option value="{{$pr->id}}">{{$pr->nombre}}</option>
							@endforeach
						</select>

					</div>

				</div>

			</div>