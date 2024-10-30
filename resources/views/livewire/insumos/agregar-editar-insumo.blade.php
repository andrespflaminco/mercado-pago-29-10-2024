                    <div>
                        <div class="page-header">

						<div class="page-title">
						    @if($forma_edit != 1)
							<h4>{{ $selected_id > 0 ? 'Editar insumo' : 'Agregar insumo' }} </h4>
							<h6>{{ $selected_id > 0 ? 'Modificar el insumo' : 'Crear un nuevo insumo' }} </h6>
							@else
							<h4>Detalle del insumo. </h4>
							@endif
						</div>
					</div>
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">

                              <div class="form-group  col-12">
                               <label>Nombre</label>
                                 <input type="text" {{ $es_sucursal == 1? 'readonly' : '' }}  wire:model.lazy="name" class="form-control" placeholder="Nombre del insumo" >
                               @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
                             </div>
                             <div class="form-group col-6">
                              <label>Codigo</label>
                                <input type="text" {{ $es_sucursal == 1? 'readonly' : '' }}  wire:model.lazy="barcode" class="form-control" placeholder="ej: 02589" >
                              @error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-6">
                             <label>Costo</label>
                               <input type="text" {{ $es_sucursal == 1? 'readonly' : '' }}  wire:model.lazy="cost" class="form-control" placeholder="ej: 0.00" >
                             @error('cost') <span class="text-danger er">{{ $message }}</span> @enderror
                           </div>

                        <div class="col-sm-12 col-md-4">
                         <div class="form-group">
                          <label>Inv. minimo</label>
                            <input {{ $es_sucursal == 1? 'readonly' : '' }} type="number" wire:model.lazy="alerts" class="form-control" placeholder="ej: 10" >
                          @error('alerts') <span class="text-danger er">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-4">
                         <div class="form-group">
                          <label>Proveedor</label>
                            <select {{ $es_sucursal == 1? 'disabled' : '' }}  wire:model='proveedor' class="form-control">
                              <option value="Elegir" disabled >Elegir</option>
                              <option value="1" >Sin proveedor</option>
                              @foreach($prov as $pr)
                              <option value="{{$pr->id}}">{{$pr->nombre}}</option>
                              @endforeach
                            </select>
                            @error('proveedor') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-12" style="border-bottom: solid 1px #eee; padding: 15px; margin-bottom: 10px;">
                        Contenido del insumo
                        </div>
                        
                        <div class="col-sm-12 col-md-4">
                         <div class="form-group">
                          <label>Cantidad</label>
                            <input type="number" {{ $es_sucursal == 1? 'readonly' : '' }}  wire:model.lazy="cantidad" class="form-control" placeholder="ej: 0" >
                          @error('cantidad') <span class="text-danger er">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        
                        <div class="col-sm-12 col-md-4">
                         <div class="form-group">
                          <label>Tipo de unidad de medida</label>
                            <select wire:model='tipo_unidad_medida' {{ $es_sucursal == 1? 'disabled' : '' }}  class="form-control">
                              <option value="Elegir" selected >Elegir</option>
                              @foreach($tipo_unidad_medida_select as $tu)
                              <option value="{{$tu->id}}">{{$tu->nombre}}</option>
                              @endforeach
                            </select>
                            @error('tipo_unidad_medida') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        @if($tipo_unidad_medida != "Elegir")
                        <div class="col-sm-12 col-md-4">
                         <div class="form-group">
                          <label>Unidad de medida</label>
                            <select {{ $es_sucursal == 1? 'disabled' : '' }}  wire:model='unidad_medida' class="form-control">
                              <option value="Elegir" selected >Elegir</option>
                              @foreach($unidad_medida_select as $pr)
                              <option value="{{$pr->id}}">{{$pr->nombre_completo}} ( {{$pr->nombre}} )</option>
                              @endforeach
                            </select>
                            @error('unidad_medida') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        @endif



                        <div class="col-12">
                            <div class="table-responsive">
                                    <label></label>
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Sucursal</th>
                                                 <th>Stock </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(auth()->user()->sucursal != 1) 
                                            <tr>
                                                <td>{{auth()->user()->name}}</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" {{ ($es_sucursal == 1) && (auth()->user()->id != $sucu->sucursal_id ) ? 'readonly' : '' }}  class="form-control" wire:model="stock.{{auth()->user()->id}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            
                                            @foreach ($sucursales as $llave => $sucu)
                                            <tr>
                                                <td>{{$sucu->name}}</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number"{{ ($es_sucursal == 1) && (auth()->user()->id != $sucu->sucursal_id ) ? 'readonly' : '' }}  class="form-control" wire:model="stock.{{$sucu->sucursal_id}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                        </div>							
								<div class="col-lg-12 mt-3">
								    
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
                    </div>

