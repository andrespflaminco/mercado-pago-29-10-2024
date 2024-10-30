								
								
								<h6 class="mt-2 mb-2">Precios</h6>
								<div class="table-responsive" style="width: 80%;">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Lista de precios</th>
                                                <th @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif class="text-center">% margen sobre Costo</th>
                                                <th>Precio</th>
                                                <th>Al modificar costos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(Auth::user()->sucursal != 1 && Auth::user()->profile != "Cajero")
                                            <tr>
                                                <td>
                                                    <label>Costo</label>
                                                </td>
                                                <td @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif></td>   
                                                <td>
                                                    <input wire:keydown.enter="CambiarCostoReglaPrecio(0)" wire:change="CambiarCostoReglaPrecio(0)" {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model.lazy="cost" class="form-control" {{ $tipo_producto == 2 || $tipo_producto == 3 ? 'disabled' : '' }}>
                                                </td>
                                                <td></td>
                                            </tr>
                                            @endif
                                
                                            @if(Auth::user()->profile != "Cajero" && 0 < $sucursales->count())
                                            <tr {{ $mapeoListaMuestra[0] == 0 ? 'hidden' : '' }}>
                                                <td>
                                                    <label class="d-flex">
                                                        Precio de venta a sucursales
                                                        <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el precio de venta a las sucursales">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                                            </svg>
                                                        </div>
                                                    </label>
                                                </td>
                                                <td @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif>
                                                        <div class="input-group">
                                                        <input {{ $forma_edit  == 1 ? 'readonly' : '' }}  type="number" wire:model="porcentaje_regla_precio_interno" wire:change="CambiarPorcentajeReglaPrecio(0,0,1)" wire:keydown.enter="CambiarPorcentajeReglaPrecio(0,0,1)"  class="form-control">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">% </span>
                                                            </div>
                                                        </div>
                                                    
                                                </td>   
                                                <td>
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }}  {{ $forma_edit == 1 ? 'readonly' : '' }} wire:change="CambiarPorcentajePorCambioPrecio(0,0,1)" wire:keydown.enter="CambiarPorcentajePorCambioPrecio(0,0,1)" type="number" wire:model.lazy="precio_interno" class="form-control">
                                                </td>
                                                <td>{{ $regla_precio_interno == 1 ? 'Precio fijo' : '% Utilidad sobre el costo' }} </td>
                                            </tr>
                                            @endif
                                
                                            <tr>
                                                <td>
                                                    <label class="d-flex">
                                                        Precio de venta

                                                    </label>
                                                </td>
                                                <td @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif>
                                                    <div>
                                                        <div class="input-group">
                                                        <input {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model="porcentaje_regla_precio.0|0" class="form-control" wire:change="CambiarPorcentajeReglaPrecio(0,0,2)" wire:keydown.enter="CambiarPorcentajeReglaPrecio(0,0,2)">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">% </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>                                           
                                                <td>
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" required wire:model.lazy="precio_lista.0|0|0|0" wire:keydown.enter="CambiarPorcentajePorCambioPrecio(0,0,2)" wire:change="CambiarPorcentajePorCambioPrecio(0,0,2)"  class="form-control" >
                                                    @error('precio_lista')
                                                    <span class="text-danger er">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>{{ $regla_precio[0] == 1 ? 'Precio fijo' : '% Utilidad sobre el costo' }} </td>
                                            </tr>
                                
                                            @if($lista_precios != null)
                                            @foreach($lista_precios as $key => $lp)
                                            
                                            @if($lp->tipo == 2 && auth()->user()->can('ver costos defecto'))

                                            <tr {{ $mapeoListaMuestra[$lp->id] == 0 ? 'hidden' : '' }} >
                                                <td>
                                                    <label>Precio {{$lp->nombre}}</label>
                                                </td>
                                                <td @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif>
                                                    <div>
                                                        <div class="input-group">
                                                        <input {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model="porcentaje_regla_precio.0|{{$lp->id}}" wire:change="CambiarPorcentajeReglaPrecio(0,{{$lp->id}},2)" wire:keydown.enter="CambiarPorcentajeReglaPrecio({{$lp->id}},2)" class="form-control">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">% </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" required class="form-control" wire:keydown.enter="CambiarPorcentajePorCambioPrecio(0,{{$lp->id}},2)" wire:change="CambiarPorcentajePorCambioPrecio(0,{{$lp->id}},2)"  wire:model="precio_lista.0|{{ $lp->id }}|0|0">
                                                    @error('precio_lista')
                                                    <span class="text-danger er">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    @foreach($lista_precios_reglas as $lpr)
                                                    @if($lp->id == $lpr->lista_id)
                                                    @if($lpr->regla == 2) % Utilidad sobre el costo @endif
                                                    @if($lpr->regla == 1) Precio fijo @endif
                                                    @endif
                                                    @endforeach
                                                </td>
                                            </tr>

                                            @endif
                                            
                                                                                        
                                            @if($lp->tipo == 1)

                                            <tr {{ $mapeoListaMuestra[$lp->id] == 0 ? 'hidden' : '' }} >
                                                <td>
                                                    <label>Precio {{$lp->nombre}}</label>
                                                </td>
                                                <td @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif>
                                                    <div>
                                                        <div class="input-group">
                                                        <input {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" wire:model="porcentaje_regla_precio.0|{{$lp->id}}" wire:change="CambiarPorcentajeReglaPrecio(0,{{$lp->id}},2)" wire:keydown.enter="CambiarPorcentajeReglaPrecio({{$lp->id}},2)" class="form-control">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">% </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} type="number" required class="form-control" wire:keydown.enter="CambiarPorcentajePorCambioPrecio(0,{{$lp->id}},2)" wire:change="CambiarPorcentajePorCambioPrecio(0,{{$lp->id}},2)"  wire:model="precio_lista.0|{{ $lp->id }}|0|0">
                                                    @error('precio_lista')
                                                    <span class="text-danger er">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    @foreach($lista_precios_reglas as $lpr)
                                                    @if($lp->id == $lpr->lista_id)
                                                    @if($lpr->regla == 2) % Utilidad sobre el costo @endif
                                                    @if($lpr->regla == 1) Precio fijo @endif
                                                    @endif
                                                    @endforeach
                                               
                                                </td>
                                            </tr>

                                            @endif
                                            
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <br>
								<br>
								<div class="row">
								<div class="col-lg-12 col-sm-12 col-12">
								<div class="form-group">
                                <h6 class="mt-2 mb-2">Stocks</h6>								
                                <div class="table-responsive mt-3">
                                    <label></label>
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Sucursal</th>
                                                <th>Almacen</th>
                                                <th>Stock real <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stock real a encontrar en un recuento fisico"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></th>
                                                <th>Stock disponible  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Cantidad de unidades disponibles para vender. Es el stock real, menos las unidades ya vendidas pero aun no entregadas"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div> </th>
                                                <th>Stock comprometido <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el stock aun en nuestro poder, pero que ya esta vendido, esperando por ser entregado."><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>  </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(auth()->user()->sucursal != 1) 
                                            <tr>
                                                <td>{{auth()->user()->name}}</td>
                                                <td>
                                                    <select wire:model.defer='almacen_id.0|0|0|0' {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
                                                        <option value="1" >Sin almacen</option>
                                                        @foreach($almacenes as $a)
                                                            <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" wire:model="real_stock_sucursal.0|0|0|0" {{ $forma_edit == 1 ? 'readonly' : '' }} required wire:change="CambiarStockDisponible('0|0|0|0')" class="form-control">
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg </span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif

                                                    </div>
                                                    @error('real_stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal.0|0|0|0">
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal_comprometido.0|0|0|0">
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            
                                            @foreach ($sucursales as $llave => $sucu)
                                            <tr {{ ($muestra_stock_otras_sucursales == 0) && ($sucu->sucursal_id != $comercio_id) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>
                                                <td>{{$sucu->name}}</td>
                                                <td>
                                                    <select wire:model.defer='almacen_id.0|{{ $sucu->sucursal_id }}|0|0' {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
                                                        <option value="1" >Sin almacen</option>
                                                        @foreach($almacenes as $a)
                                                            <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                        @endforeach
                                                    </select>    
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" {{ ($es_sucursal == 1) && (auth()->user()->id != $sucu->sucursal_id ) ? 'readonly' : '' }}  {{ $forma_edit == 1 ? 'readonly' : '' }} required class="form-control" wire:change="CambiarStockDisponible('0|{{ $sucu->sucursal_id }}|0|0')" wire:model="real_stock_sucursal.0|{{ $sucu->sucursal_id }}|0|0" />
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @error('real_stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal.0|{{ $sucu->sucursal_id }}|0|0">
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td> 
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" wire:model="stock_sucursal_comprometido.0|{{ $sucu->sucursal_id }}|0|0">
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

    
								</div>
                                </div>
                                </div>
                                