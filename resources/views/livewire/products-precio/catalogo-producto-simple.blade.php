<tr>
											<td style="padding: 0px 10px !important;">
											    
											    @if(Auth::user()->profile != "Cajero" )
												
												@can('accion en lote productos')
												
												@php
                                                    $tipoProductoSimple = 'S';
                                                @endphp
                                                            
												<label class="checkboxs">
												    <input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="checkbox" wire:model.defer="id_check" tu-attr-id="{{ $tipoProductoSimple . $product->id}}"  class="mis-checkboxes" value="{{$tipoProductoSimple . $product->id}}">
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
                                            
                                            <td style="padding: 0px 10px !important;"></td>
                                            
                                            
											<td style="padding: 0px 10px !important;">
								                <p style="font-size:16px">{{$product->barcode}}</p>
                                			</td>

                                            <!----------- COSTO --------------------->
                                            @if(auth()->user()->sucursal != 1)
                                            
                                            <td style="padding: 0px 10px !important;">
                                                <div class="input-group mb-0">
            									<input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                value="{{ $product->cost }}" 
                                                id="c{{$product->id}}"
                                                wire:keydown.enter="updateCost({{ $product->id }} ,0, $('#c' + '{{$product->id}}').val())"
                                                wire:change="updateCost({{ $product->id }} ,0, $('#c' + '{{$product->id}}').val())"
                                                class="form-control" style="width: 100px;">
            									</div>
                                            </td>
                                            @endif
                                            
                                            @if($columns['precio_interno'])
                                            <!----------- REGLA PRECIOS / PRECIO INTERNO -------------->
                                            
                                            <td @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif {{$lista_costo_defecto != 0 ? 'hidden' : ''}}  style="padding: 0px 10px !important;">
                                                
                                                <div class="input-group mb-0">
            									<input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                value="{{ $product->porcentaje_regla_precio_interno * 100}} " 
                                                id="rpi{{$product->id}}"
                                                wire:keydown.enter="updatePorcentajeReglaPrecioInterno({{ $product->id }} ,0, $('#rpi' + '{{$product->id}}').val())"
                                                wire:change="updatePorcentajeReglaPrecioInterno({{ $product->id }} ,0, $('#rpi' + '{{$product->id}}').val())"
                                                class="form-control" style="width: 100px;">
            									</div>
                                            </td>
                                            
                                            <!------------- PRECIO INTERNO -------------------->
											<td {{$lista_costo_defecto != 0 ? 'hidden' : ''}}  style="padding: 0px 10px !important;">
                                            
                                                <div class="input-group mb-0">
            									<input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                                value="{{ $product->precio_interno }}" 
                                                id="pi{{$product->id}}"
                                                wire:keydown.enter="updatePrecioInterno({{ $product->id }} ,0, $('#pi' + '{{$product->id}}').val())"
                                                wire:change="updatePrecioInterno({{ $product->id }} ,0, $('#pi' + '{{$product->id}}').val())"
                                                class="form-control" style="width: 100px;">
            									</div>
            						
        									</td>
        									
        									@endif
        									
        									@if($columns['precio_base'])
        									
        									<!----------- REGLA PRECIOS / PRECIO BASE -------------->
                                            
                                            <td @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif style="padding: 0px 10px !important;">
                                            
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == 0)
        
                                            <div class="input-group mb-0">
        									<input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
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
											<td style="padding: 0px 10px !important;">
                                            
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == 0)
                                            <div class="input-group mb-0" >
                                            
        									<input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
        									style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
        									value="{{ $pl->precio_lista }}" 
                                            id="p{{$pl->id}}"
                                            wire:keydown.enter="updatePrice({{ $pl->id }} ,0, $('#p' + '{{$pl->id}}').val())"
                                            wire:change="updatePrice({{ $pl->id }} ,0, $('#p' + '{{$pl->id}}').val())"
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

        									<td
        									@if($mapeoListaMuestra[$list->id] == 0) 
                                                hidden 
                                            @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))
                                                hidden 
                                            @endif
                                                     
                                            @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif
                                            
        									style="padding: 0px 10px !important;">
                    
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == $list->id)

                                           
                                            <div   {{$list->id == $lista_costo_defecto ? 'hidden' : '' }} class="input-group mb-0">
        									<input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
        									style="{{ $pl->regla_precio == 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
        									value="{{ $pl->porcentaje_regla_precio * 100 }}" 
                                            id="regla{{$pl->id}}"
                                            wire:keydown.enter="updatePorcentajeRegla({{ $pl->id }} ,0, $('#regla' + '{{$pl->id}}').val())"
                                            wire:change="updatePorcentajeRegla({{ $pl->id }} ,0, $('#regla' + '{{$pl->id}}').val())"
                                            class="form-control">
        									</div>
        									
        									
        									@endif
        
        									@endif
        
        									@endforeach
        									
        									<!--------------------------------------->
        
        									</td>


        									<td 
        									@if($mapeoListaMuestra[$list->id] == 0) 
                                                hidden 
                                            @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))
                                                hidden 
                                            @endif
        									style="padding: 0px 10px !important;">
                        
        									@foreach($productos_lista_precios as $pl)
        
        									@if($pl->product_id == $product->id)
        
        									@if($pl->lista_id == $list->id)

                                            <div class="input-group mb-0">

        									<input {{auth()->user()->sucursal != 1 ? '' : 'readonly'}} type="text" 
                                            value="{{ $pl->precio_lista }}" 
                                            style="{{ $pl->regla_precio != 1 ? 'background: #fbfbfb;' : '' }} width: 100px;"
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
											</td>
										</tr>