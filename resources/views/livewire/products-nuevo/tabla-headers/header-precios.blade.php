                            <thead>
								<tr>
											<th>
											    @if(Auth::user()->profile != "Cajero" )
											    
											    @can('accion en lote productos')
												<label class="checkboxs">
													<input type="checkbox" id="select-all" >
													<span class="checkmarks"></span>
												</label>
												@endcan
												
												@endif
											</th>
											<th colspan="2">Nombre del producto</th>
											<th>SKU</th>
											<th class="text-center">Costo</th>
											@if($lista_costo_defecto == 0)
											<th class="text-center">% Descuento costo</th>
											<th class="text-center">Costo despues descuento</th>
											@endif
											
											@if($columns['precio_interno'])
											<th {{$lista_costo_defecto != 0 ? 'hidden' : ''}} @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif class="text-center">Utilidad %</th>
											<th {{$lista_costo_defecto != 0 ? 'hidden' : ''}} class="text-center">
								            
								            <a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Precio de venta a sucursales </a>
                                         	<div class="dropdown-menu">
                                         	    <button  wire:click="UpdateRegla(1,1)"   class="dropdown-item">Precio fijo</button>
                                         	    <button  wire:click="UpdateRegla(1,2)"   class="dropdown-item">% sobre el costo</button>
                                         	    <button hidden wire:click="UpdateRegla(1,3)"   class="dropdown-item">Precio fijo</button>
                                            </div>
                                            @foreach($lista_precios_reglas as $lpr)
                                            @if($lpr->lista_id == 1)
                                            <p style="font-size:9px;">@if($lpr->regla == 1) Precio fijo @else % sobre costo @endif</p>
                                            @endif
                                            @endforeach
											
											</th>
											@endif
											
											@if($columns['precio_base'])
											<th class="text-center" @if( isset($mapeoListaMuestra[0]) && $mapeoListaMuestra[0] == 0) hidden  @endif @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif>Utilidad %</th>
											<th class="text-center" @if(isset($mapeoListaMuestra[0]) && $mapeoListaMuestra[0] == 0) hidden  @endif>
								            
								            <a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Precio </a>
                                         	<div class="dropdown-menu">
                                         	    <button  wire:click="UpdateRegla(0,1)"   class="dropdown-item">Precio fijo</button>
                                         	    <button  wire:click="UpdateRegla(0,2)"   class="dropdown-item">% sobre el costo</button>
                                         	    <button hidden wire:click="UpdateRegla(0,3)"   class="dropdown-item">Precio fijo</button>
                                            </div>
                                            @foreach($lista_precios_reglas as $lpr)
                                            @if($lpr->lista_id == 0)
                                            <p style="font-size:9px;">@if($lpr->regla == 1) Precio fijo @else % sobre costo @endif</p>
                                            @endif
                                            @endforeach
											
											</th>
											@endif
											
											@foreach($lista_precios as $list)
											
											@if($columns['precio_'.$list->id])
											<th class="text-center"  @if( (isset($mapeoListaMuestra[$list->id]) && $mapeoListaMuestra[$list->id] == 0) || ($lista_costo_defecto != 0 && $list->id == $lista_costo_defecto))  hidden @endif @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif  >Utilidad %</th>
                							<th class="text-center"  @if( (isset($mapeoListaMuestra[$list->id]) && $mapeoListaMuestra[$list->id] == 0) || ($lista_costo_defecto != 0 && $list->id == $lista_costo_defecto))  hidden @endif  >
                							
                							<a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Precio {{$list->nombre}} </a>
                                         	<div class="dropdown-menu">
                                         	    <button  wire:click="UpdateRegla({{$list->id}},1)"   class="dropdown-item">Precio fijo</button>
                                         	    <button  wire:click="UpdateRegla({{$list->id}},2)"   class="dropdown-item">% sobre el costo</button>
                                         	    <button hidden wire:click="UpdateRegla({{$list->id}},3)"   class="dropdown-item">Precio fijo</button>
                                            </div>
											@foreach($lista_precios_reglas as $lpr)
                                            @if($lpr->lista_id == $list->id)
                                            <p style="font-size:9px;"> @if($lpr->regla == 1) Precio fijo @else % sobre costo @endif </p>
                                            @endif
                                            @endforeach
                							</th>
                							@endif
                							
                							@endforeach
											<th>Acciones</th>
										</tr>
							</thead>