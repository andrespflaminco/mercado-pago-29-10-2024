
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
                                                            $referenciaVariacion = $tipoProducto === 'V' ? $pvd->referencia_variacion : 0; // Usar la referencia si es variable, 0 si es simple
                                                        @endphp
                                                        
                                                        <label class="checkboxs">
                                                            <input type="checkbox" 
                                                                   wire:model.defer="id_check" 
                                                                   tu-attr-id="{{ $product->id . '-' . $referenciaVariacion }}" 
                                                                   class="mis-checkboxes" 
                                                                   value="{{ $product->id . '-' . $referenciaVariacion }}">
                                                            <span class="checkmarks"></span>
                                                        </label>
                                                    @endcan
                                                @endif
                                            </td>

                                    
                                    
                                                <td style="padding: 0px !important;" class="productimgname">
    												<a href="javascript:void(0);" class="product-img">
    												    @if($product->image != null)
                										<img src="{{ asset('storage/products/' . $product->image ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
                										@else
                										
                										@if($product->wc_image_url)
                										<img src="{{ $product->wc_image_url }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
                										@else
                										<img src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
                										@endif
                										
                										@endif
    												
    												</a>
    												<a href="javascript:void(0);" wire:click.prevent="Ver({{$product->id}})">{{$product->name}}</a>
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
                                                
                                                @if($lista_costo_defecto == 0)
                                                <!----------- COSTO --------------------->
                                                <td style="padding: 0px 10px !important;">
                                                    <div class="input-group mb-0">
                                                        <input type="text" 
                                                            value="{{ $pvd->cost }}" 
                                                            id="c{{ $pvd->id }}"
                                                            wire:keydown.enter="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            wire:change="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            class="form-control" style="width: 100px;"
                                                            {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                            >
                                                    </div>
                                                </td>
                                                @else
                                                <!------------- COSTO DE LISTA DE PRECIOS BASE -------------------->
                                                    <td style="padding: 0px 10px !important;">
                                                        @foreach($productos_lista_precios as $pl)
                                                            @if( $pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == $lista_costo_defecto && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                <div class="input-group mb-0">
                                                                    <input type="text" 
                                                                        style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                        value="{{ $pl->precio_lista }}" 
                                                                        id="p{{ $pl->id }}"
                                                                        wire:keydown.enter="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                        wire:change="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                        class="form-control"
                                                                        {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                                        >
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </td>                                                
                                                @endif
                                                
                                                @if($lista_costo_defecto == 0)
                                                
                                                <!----------- DESCUENTO COSTO --------------------->
                                                <td style="padding: 0px 10px !important;">
                                                    <div class="input-group mb-0">
                                                        <input type="text"  readonly
                                                            value="{{ round( $pvd->descuento_costo * 100 , 2) }}" 
                                                            id="c{{ $pvd->id }}"
                                                            wire:keydown.enter="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            wire:change="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            class="form-control" style="width: 100px;"
                                                            >
                                                    </div>
                                                </td>
                                                
                                                <!----------- COSTO DESPUES DE DESCUENTO --------------------->
                                                <td style="padding: 0px 10px !important;">
                                                    <div class="input-group mb-0">
                                                        <input type="text"  readonly
                                                            value="{{ round( $pvd->cost * (1 - $pvd->descuento_costo ) , 2) }}" 
                                                            id="c{{ $pvd->id }}"
                                                            wire:keydown.enter="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            wire:change="updateCost({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#c' + '{{ $pvd->id }}').val())"
                                                            class="form-control" style="width: 100px;">
                                                    </div>
                                                </td>
                                                @endif
                                                
                                                @if($columns['precio_interno'])
                                                    <!----------- REGLA PRECIOS / PRECIO INTERNO -------------->
                                                    <td {{$lista_costo_defecto != 0 ? 'hidden' : ''}} @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif style="padding: 0px 10px !important;">
                                                        <div class="input-group mb-0">
                                                            <input type="text" 
                                                                value="{{ $pvd->porcentaje_regla_precio_interno * 100 }}" 
                                                                id="rpi{{ $pvd->id }}"
                                                                wire:keydown.enter="updatePorcentajeReglaPrecioInterno({{ $pvd->product_id }}, '{{ $pvd->referencia_variacion }}', $('#rpi' + '{{ $pvd->id }}').val())"
                                                                wire:change="updatePorcentajeReglaPrecioInterno({{ $pvd->product_id }}, '{{ $pvd->referencia_variacion }}', $('#rpi' + '{{ $pvd->id }}').val())"
                                                                class="form-control" style="width: 100px;"
                                                                {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                                >
                                                        </div>
                                                    </td>
                                    
                                                    <!------------- PRECIO INTERNO -------------------->
                                                    <td {{$lista_costo_defecto != 0 ? 'hidden' : ''}} style="padding: 0px 10px !important;">
                                                        <div class="input-group mb-0">
                                                            <input type="text" 
                                                                value="{{ $pvd->precio_interno }}" 
                                                                id="pi{{ $pvd->id }}"
                                                                wire:keydown.enter="updatePrecioInterno({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#pi' + '{{ $pvd->id }}').val())"
                                                                wire:change="updatePrecioInterno({{ $pvd->id }}, '{{ $pvd->referencia_variacion }}', $('#pi' + '{{ $pvd->id }}').val())"
                                                                class="form-control" style="width: 100px;"
                                                                {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                                >
                                                        </div>
                                                    </td>
                                                @endif
                                    
                                                @if($columns['precio_base'])
                                                    <!----------- REGLA PRECIOS / PRECIO BASE -------------->
                                                    <td style="padding: 0px 10px !important;" @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif {{ isset($mapeoListaMuestra[0]) && $mapeoListaMuestra[0] == 0 ? 'hidden' : '' }}>
                                                        @foreach($productos_lista_precios as $pl)
                                                            @if($pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == 0 && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                <div class="input-group mb-0">
                                                                    <input type="text" 
                                                                        style="{{ $pl->regla_precio == 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                        value="{{ $pl->porcentaje_regla_precio * 100 }}" 
                                                                        id="regla{{ $pl->id }}"
                                                                        wire:keydown.enter="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                        wire:change="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                        {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                                        class="form-control">
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                    
                                                    <!------------- PRECIO BASE -------------------->
                                                    <td style="padding: 0px 10px !important;" @if($mapeoListaMuestra[0] == 0) hidden  @endif>
                                                        @foreach($productos_lista_precios as $pl)
                                                            @if( $pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == 0 && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                <div class="input-group mb-0">
                                                                    <input type="text" 
                                                                        style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                        value="{{ $pl->precio_lista }}" 
                                                                        id="p{{ $pl->id }}"
                                                                        wire:keydown.enter="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                        wire:change="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                        {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                                        class="form-control">
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                @endif
                                    
                                                @foreach($lista_precios as $list)
                                                    @if($columns['precio_'.$list->id])
                                                        <!---------- REGLA PRECIO LISTAS -------------->
                                                        <td style="padding: 0px 10px !important;" @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif  @if( (isset($mapeoListaMuestra[$list->id]) && $mapeoListaMuestra[$list->id] == 0) || ($lista_costo_defecto != 0 && $list->id == $lista_costo_defecto))  hidden @endif >
                                                            @foreach($productos_lista_precios as $pl)
                                                                @if($pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == $list->id && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                    
                                                                    @if($lista_costo_defecto == 0)
                                                                    <div class="input-group mb-0">
                                                                        <input type="text" 
                                                                            style="{{ $pl->regla_precio == 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                            value="{{ $pl->porcentaje_regla_precio * 100 }}" 
                                                                            id="regla{{ $pl->id }}"
                                                                            wire:keydown.enter="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                            wire:change="updatePorcentajeRegla({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#regla' + '{{ $pl->id }}').val())"
                                                                            {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                                            class="form-control">
                                                                    </div>
                                                                    @else
                                                                    
                                                                    @php
                                                                        // Busca el precio de la lista de costo por defecto
                                                                        $precioListaDefecto = $productos_lista_precios->where('product_id', $product->id)
                                                                                                                    ->where('referencia_variacion',$pl->referencia_variacion)
                                                                                                                    ->where('lista_id', $lista_costo_defecto)
                                                                                                                    ->first()->precio_lista ?? 1;
                                                                        $proporcionPrecio = $pl->precio_lista / $precioListaDefecto;
                                                                    @endphp
                                                                    <div class="text-center">
                                    									{{ round((($proporcionPrecio - 1) * 100),2) }} %    
                                    								</div>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </td>
                                    
                                                        <!------------- PRECIO LISTAS -------------------->
                                                        <td style="padding: 0px 10px !important;"  @if( (isset($mapeoListaMuestra[0]) && $mapeoListaMuestra[$list->id] == 0 ) || ($lista_costo_defecto != 0 && $list->id == $lista_costo_defecto))  hidden @endif  >
                                                            @foreach($productos_lista_precios as $pl)
                                                                @if($pl->referencia_variacion == $pvd->referencia_variacion &&  $pl->lista_id == $list->id && $pl->eliminado == 0 && $pl->product_id == $product->id)
                                                                    <div class="input-group mb-0">
                                                                        <input type="text" 
                                                                            style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
                                                                            value="{{ $pl->precio_lista }}" 
                                                                            id="p{{ $pl->id }}"
                                                                            wire:keydown.enter="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                            wire:change="updatePrice({{ $pl->id }}, '{{ $pl->referencia_variacion }}', $('#p' + '{{ $pl->id }}').val())"
                                                                            {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
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
                                        

