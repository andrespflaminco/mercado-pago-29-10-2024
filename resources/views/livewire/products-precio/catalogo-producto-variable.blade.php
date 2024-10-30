                                    
                                        @php
                                            $productos_variaciones_datos = \App\Models\productos_variaciones_datos::where('product_id', $product->id)
                                                ->where('eliminado', 0)
                                                ->get();
                                        @endphp
                                    
                                        @foreach($productos_variaciones_datos as $pvd)
                                            <tr>
                                                <td style="padding: 0px 10px !important;">
                                                    @if(Auth::user()->profile != "Cajero")
                                                        @can('accion en lote productos')
                                                            @php
                                                                $tipoProducto = count($productos_variaciones_datos) > 0 ? 'V' : 'S';
                                                            @endphp
                                                            
                                                            <label class="checkboxs">
                                                                <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="checkbox" wire:model.defer="id_check" tu-attr-id="{{ $tipoProducto . $pvd->id }}" class="mis-checkboxes" value="{{ $tipoProducto . $pvd->id }}">
                                                                <span class="checkmarks"></span>
                                                            </label>
                                                        @endcan
                                                    @endif
                                                </td>
                                    
                                    
                                                <td style="padding: 0px 10px !important;">
                                                    <div class="input-group mb-0">
                                                        <div style="text-align:center;" class="input-group-prepend">
                                                            <span style="padding: .375rem 0rem; color: #637381; font-size:16px; height: 100% !important; background: white; margin-left: 1px !important; border:none;" class="input-group-text input-gp">
                                                                {{ $product->name }} 
                                                            </span> 
                                                        </div>
                                                    </div>
                                                </td>
                                    
                                                <!----------- VARIACIONES --------------------->
                                                <td style="padding: 0px 10px !important;">
                                                    <div class="input-group mb-0">
                                                        <div style="text-align:center;" class="input-group-prepend">
                                                            <span style="padding: .375rem 0rem; color: #637381; font-size:16px; height: 100% !important; background: white; margin-left: 1px !important; border:none;" class="input-group-text input-gp">
                                                                {{ $pvd->variaciones }}
                                                            </span> 
                                                        </div>
                                                    </div>
                                                </td>
                                    
                                                <td style="padding: 0px 10px !important;">
                                                    <div class="input-group mb-0">
                                                        <div style="text-align:center;" class="input-group-prepend">
                                                            <span style="padding: .375rem 0rem; color: #637381; font-size:16px; height: 100% !important; background: white; margin-left: 1px !important; border:none;" class="input-group-text input-gp">
                                                                {{ $pvd->codigo_variacion }}
                                                            </span> 
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                 @if(auth()->user()->sucursal != 1)
                                                <!----------- COSTO --------------------->
                                                <td style="padding: 0px 10px !important;">
                                                    <div class="input-group mb-0">
                                                        <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                            value="{{ $pvd->cost }}" 
                                                            id="c{{ $pvd->id }}"
                                                            wire:keydown.enter="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            wire:change="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            class="form-control" style="width: 100px;">
                                                    </div>
                                                </td>
                                                @endif
                                    
                                                @if($columns['precio_interno'])
                                                    <!----------- REGLA PRECIOS / PRECIO INTERNO -------------->
                                                    <td style="padding: 0px 10px !important;">
                                                        <div class="input-group mb-0">
                                                            <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                                value="{{ $pvd->porcentaje_regla_precio_interno * 100 }}" 
                                                                id="rpi{{ $pvd->id }}"
                                                                wire:keydown.enter="updatePorcentajeReglaPrecioInterno({{ $pvd->product_id }}, '{{ $pvd->referencia_variacion }}', $('#rpi' + '{{ $pvd->id }}').val())"
                                                                wire:change="updatePorcentajeReglaPrecioInterno({{ $pvd->product_id }}, '{{ $pvd->referencia_variacion }}', $('#rpi' + '{{ $pvd->id }}').val())"
                                                                class="form-control" style="width: 100px;">
                                                        </div>
                                                    </td>
                                    
                                                    <!------------- PRECIO INTERNO -------------------->
                                                    <td style="padding: 0px 10px !important;">
                                                        <div class="input-group mb-0">
                                                            <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                                value="{{ $pvd->precio_interno }}" 
                                                                id="pi{{ $pvd->id }}"
                                                                wire:keydown.enter="updatePrecioInterno({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#pi' + '{{ $pvd->id }}').val())"
                                                                wire:change="updatePrecioInterno({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#pi' + '{{ $pvd->id }}').val())"
                                                                class="form-control" style="width: 100px;">
                                                        </div>
                                                    </td>
                                                @endif
                                    
                                                @if($columns['precio_base'])
                                                    <!----------- REGLA PRECIOS / PRECIO BASE -------------->
                                                    <td style="padding: 0px 10px !important;">
                                                        @foreach($productos_lista_precios as $pl)
                                                            @if($pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == 0 && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                <div class="input-group mb-0">
                                                                    <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                                        style="{{ $pl->regla_precio == 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                        value="{{ $pl->porcentaje_regla_precio * 100 }}" 
                                                                        id="regla{{ $pl->id }}"
                                                                        wire:keydown.enter="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                        wire:change="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                        class="form-control">
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                    
                                                    <!------------- PRECIO BASE -------------------->
                                                    <td style="padding: 0px 10px !important;">
                                                        @foreach($productos_lista_precios as $pl)
                                                            @if( $pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == 0 && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                <div class="input-group mb-0">
                                                                    <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                                        style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                        value="{{ $pl->precio_lista }}" 
                                                                        id="p{{ $pl->id }}"
                                                                        wire:keydown.enter="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                        wire:change="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                        class="form-control">
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                @endif
                                    
                                                @foreach($lista_precios as $list)
                                                    @if($columns['precio_'.$list->id])
                                                        <!---------- REGLA PRECIO LISTAS -------------->
                                                        <td 
                                                         @if($mapeoListaMuestra[$list->id] == 0) 
                                                                hidden 
                                                            @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))
                                                                hidden 
                                                            @endif
                                                        style="padding: 0px 10px !important;">
                                                            @foreach($productos_lista_precios as $pl)
                                                                @if($pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == $list->id && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                    <div class="input-group mb-0">
                                                                        <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                                            style="{{ $pl->regla_precio == 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                            value="{{ $pl->porcentaje_regla_precio * 100 }}" 
                                                                            id="regla{{ $pl->id }}"
                                                                            wire:keydown.enter="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                            wire:change="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                            class="form-control">
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </td>
                                    
                                                        <!------------- PRECIO LISTAS -------------------->
                                                        <td 
                                                         @if($mapeoListaMuestra[$list->id] == 0) 
                                                                hidden 
                                                            @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))
                                                                hidden 
                                                            @endif
                                                        style="padding: 0px 10px !important;">
                                                            @foreach($productos_lista_precios as $pl)
                                                                @if($pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == $list->id && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                    <div class="input-group mb-0">
                                                                        <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                                            style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                            value="{{ $pl->precio_lista }}" 
                                                                            id="p{{ $pl->id }}"
                                                                            wire:keydown.enter="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                            wire:change="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                            class="form-control">
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                    @endif
                                                @endforeach
                                    
        								    
        								    <!----------- ACCIONES ------------------>
											<td style="padding: 0px 10px !important; width: auto;">
												<a wire:click.prevent="Ver({{$product->id}})" class="me-3" href="javascript:void(0)">
													<img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
												</a>
												@if(Auth::user()->profile != "Cajero" )
												
												@can('editar productos')  
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$product->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												@endcan
												
											    @can('eliminar productos')  
												<a class="confirm-text" href="javascript:void(0)" onclick="ConfirmVariacion({{$pvd->referencia_variacion}})"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img"> 
												</a>
												@endcan 
												
												@endif
											</td>
                                            </tr>
                                        @endforeach
