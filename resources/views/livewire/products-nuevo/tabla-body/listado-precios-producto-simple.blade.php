<tr>
                                            <td style="padding: 0px 10px !important;">
                                                @if(Auth::user()->profile != "Cajero")
                                                    @can('accion en lote productos')
                                                        @php
                                                            $tipoProducto = 'S';
                                                            $referenciaVariacion = 0; // Usar la referencia si es variable, 0 si es simple
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
                                            
                                            <td style="padding: 0px 10px !important;"></td>
                                            
                                            
											<td style="padding: 0px 10px !important;">
								                <p style="font-size:16px">{{$product->barcode}}</p>
                                			</td>
                                            @if($lista_costo_defecto == 0)
                                            <!----------- COSTO --------------------->
                                            
                                            <td style="padding: 0px 10px !important;">
                                                <div class="input-group mb-0">
            									<input type="text" 
            									{{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
        									    value="{{ $product->cost }}" 
                                                id="c{{$product->id}}"
                                                wire:keydown.enter="updateCost({{ $product->id }} ,0, $('#c' + '{{$product->id}}').val())"
                                                wire:change="updateCost({{ $product->id }} ,0, $('#c' + '{{$product->id}}').val())"
                                                class="form-control" style="width: 100px;">
            									</div>
                                            </td>
                                            
                                            @else
                                            
                                            <!---------- COSTO DE UNA LISTA DE PRECIOS ----------------->
                                            
                                            <td style="padding: 0px 10px !important;">
                                            
        									@foreach($productos_lista_precios as $pl)
                                           	@if($pl->product_id == $product->id)
                                            
        									@if($pl->lista_id == $lista_costo_defecto)
                                            
                                            @if(0 < $lista_costo_defecto)
                                            <div class="input-group mb-0" >
                                            
        									<input type="text" 
        									{{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
        									style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
        									value="{{ $pl->precio_lista }}" 
                                            id="p{{$pl->id}}"
                                            class="form-control">
        									</div>
        									
                                            @endif
        									@endif
        
        									@endif
        
        									@endforeach
        				
        									</td>
    
                                            @endif
                                            
                                            @if($lista_costo_defecto == 0)
                                             <!----------- DESCUENTO COSTO --------------------->
                                            
                                            <td style="padding: 0px 10px !important;">
                                                <div class="input-group mb-0">
            									<input type="text" 
                                                value="{{ round( $product->descuento_costo * 100 , 2) }}" 
                                                class="form-control" style="width: 100px;" readonly>
            									</div>
                                            </td>
                                            
                                            <!----------- COSTO DESPUES DE DESCUENTO --------------------->
                                            
                                            <td style="padding: 0px 10px !important;">
                                                <div class="input-group mb-0">
            									<input type="text" 
                                                value="{{ round( $product->cost * (1 - $product->descuento_costo ) , 2) }}" 
                                                class="form-control" style="width: 100px;" readonly>
            									</div>
                                            </td>
                                            
                                            @endif
                                           
                                            
                                            @if($columns['precio_interno'])
                                            <!----------- REGLA PRECIOS / PRECIO INTERNO -------------->
                                            
                                            <td {{$lista_costo_defecto != 0 ? 'hidden' : ''}} @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif style="padding: 0px 10px !important;">
                                                
                                                <div class="input-group mb-0">
            									<input type="text" 
            									value="{{ $product->porcentaje_regla_precio_interno * 100}} " 
                                                id="rpi{{$product->id}}"
                                                wire:keydown.enter="updatePorcentajeReglaPrecioInterno({{ $product->id }} ,0, $('#rpi' + '{{$product->id}}').val())"
                                                wire:change="updatePorcentajeReglaPrecioInterno({{ $product->id }} ,0, $('#rpi' + '{{$product->id}}').val())"
                                                class="form-control" style="width: 100px;"
                                                {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                                >
            									</div>
                                            </td>
                                            
                                            <!------------- PRECIO INTERNO -------------------->
											<td {{$lista_costo_defecto != 0 ? 'hidden' : ''}} style="padding: 0px 10px !important;">
                                       
                                                <div class="input-group mb-0">
            									<input type="text" 
                                                value="{{ $product->precio_interno }}" 
                                                id="pi{{$product->id}}"
                                                wire:keydown.enter="updatePrecioInterno({{ $product->id }} ,0, $('#pi' + '{{$product->id}}').val())"
                                                wire:change="updatePrecioInterno({{ $product->id }} ,0, $('#pi' + '{{$product->id}}').val())"
                                                class="form-control" style="width: 100px;"
                                                {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
        									    >
            									</div>
            						
        									</td>
        									
        									@endif
        									
        									@if($columns['precio_base'])
        									
        									<!----------- REGLA PRECIOS / PRECIO BASE -------------->
                                            
                                            <td style="padding: 0px 10px !important;" @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif @if(isset($mapeoListaMuestra[0]) && $mapeoListaMuestra[0] == 0) hidden  @endif>
                                            
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == 0)
        
                                            <div class="input-group mb-0">
        									<input type="text" 
        									{{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
        									style="{{ $pl->regla_precio == 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
        									value="{{ $pl->porcentaje_regla_precio * 100 }}" 
                                            id="regla{{$pl->id}}"
                                            wire:keydown.enter="updatePorcentajeRegla({{ $pl->id }} ,0, $('#regla' + '{{$pl->id}}').val())"
                                            wire:change="updatePorcentajeRegla({{ $pl->id }} ,0, $('#regla' + '{{$pl->id}}').val())"
                                            class="form-control" >
        									</div>
        									
        
        									@endif
        
        									@endif
        
        									@endforeach
      
                                            </td>
                                            
                                            <!------------- PRECIO BASE -------------------->
											<td style="padding: 0px 10px !important;" @if(isset($mapeoListaMuestra[0]) && $mapeoListaMuestra[0] == 0) hidden  @endif>
                                            
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == 0)
                                            <div class="input-group mb-0" >
                                            
        									<input type="text" 
        									style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
        									value="{{ $pl->precio_lista }}" 
                                            id="p{{$pl->id}}"
                                            wire:keydown.enter="updatePrice({{ $pl->id }} ,0, $('#p' + '{{$pl->id}}').val())"
                                            wire:change="updatePrice({{ $pl->id }} ,0, $('#p' + '{{$pl->id}}').val())"
        									{{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                            class="form-control">
        									</div>
        									
        
        									@endif
        
        									@endif
        
        									@endforeach
        				
        									</td>
        									
        									@endif
        									
 											@foreach($lista_precios as $list)
        									
        									@if($columns['precio_'.$list->id])
        									<!---------- REGLA PRECIO LISTAS -------------->
                                            
                                            
        									<td style="padding: 0px 10px !important;"  @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif @if((isset($mapeoListaMuestra[$list->id]) && $mapeoListaMuestra[$list->id] == 0) || ($lista_costo_defecto != 0 && $list->id == $lista_costo_defecto))  hidden @endif  >
                    
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == $list->id)

                                            @if($lista_costo_defecto == 0)
                                            
                                            <div class="input-group mb-0">
        									<input type="text" 
        									style="{{ $pl->regla_precio == 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
        									value="{{ $pl->porcentaje_regla_precio * 100 }}" 
        									{{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                            id="regla{{$pl->id}}"
                                            wire:keydown.enter="updatePorcentajeRegla({{ $pl->id }} ,0, $('#regla' + '{{$pl->id}}').val())"
                                            wire:change="updatePorcentajeRegla({{ $pl->id }} ,0, $('#regla' + '{{$pl->id}}').val())"
                                            class="form-control">
        									</div>
        									
        									@else
        									
        									@php
                                                // Busca el precio de la lista de costo por defecto
                                                $precioListaDefecto = $productos_lista_precios->where('product_id', $product->id)
                                                                                            ->where('lista_id', $lista_costo_defecto)
                                                                                            ->first()->precio_lista ?? 1;
                                                $proporcionPrecio = $pl->precio_lista / $precioListaDefecto;
                                                $proporcionPrecio1 = $pl->precio_lista - $precioListaDefecto;
                                            @endphp
        									
        									<div class="text-center">
        									
        									{{ round((($proporcionPrecio - 1) * 100),2) }} %    
        									</div>
        									
        									
        									@endif
        									
        									@endif
        
        									@endif
        
        									@endforeach
        									
        									<!--------------------------------------->
        
        									</td>


        									<td style="padding: 0px 10px !important;" 	 @if( (isset($mapeoListaMuestra[$list->id]) && $mapeoListaMuestra[$list->id] == 0 )|| ($lista_costo_defecto != 0 && $list->id == $lista_costo_defecto))  hidden @endif  >
                        
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == $list->id)

                                            <div class="input-group mb-0">

        									<input type="text" 
                                            value="{{ $pl->precio_lista }}" 
                                            style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
        									{{auth()->user()->sucursal != 1 ? '' : 'readonly'}} 
                                            id="p{{$pl->id}}"
                                            wire:keydown.enter="updatePrice({{ $pl->id }} ,0, $('#p' + '{{$pl->id}}').val())"
                                            wire:change="updatePrice({{ $pl->id }} ,0, $('#p' + '{{$pl->id}}').val())"
                                            class="form-control">
        									</div>
        									@endif
        
        									@endif
        
        									@endforeach
        									
        									<!--------------------------------------->
        
        									</td>

                                            @endif

        								    @endforeach
        								    
        								    <!----------- ACCIONES ------------------>
											<td style="padding: 0px 10px !important; width: auto;">
												<a wire:click.prevent="Ver({{$product->id}})" class="me-3" href="javascript:void(0)">
													<img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
												</a>
												
												@if($estado_filtro == 0)
												
												@if(Auth::user()->profile != "Cajero" )
												
												@can('editar productos')  
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$product->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												@endcan
												
											    @can('eliminar productos')  
												<a class="confirm-text" href="javascript:void(0)" onclick="ConfirmEliminarProducto('{{$product->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img"> --
												</a>
												@endcan 
												
												@endif
												
												@else
												<button class="btn btn-light">Restaurar</button>
												@endif
											</td>
										</tr>
