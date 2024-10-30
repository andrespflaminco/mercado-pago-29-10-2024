
<tbody>
            @foreach($data as $product)
            <tr>
                <td>
                    @if(Auth::user()->profile != "Cajero" )
                    @can('accion en lote productos')  
                    <label class="checkboxs">
                        <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($product->id)}}"  class="mis-checkboxes" value="{{$product->id}}">
                        <span class="checkmarks"></span>
                    </label>
                    @endcan
                    @endif
                </td>
                <td class="productimgname">
                    <a href="javascript:void(0);" class="product-img">
                        @if($product->image != null)
                        <img src="{{ asset('storage/products/' . $product->image ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
                        @else
                        <img src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
                        @endif
                    </a>
                    <a href="javascript:void(0);">{{$product->name}}</a>
                </td>
                <td>{{$product->barcode}}</td>
                <td>{{$product->alerts}} unid.</td>
                <td {{ ($muestra_stock_casa_central == 0) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}> 
                    @foreach($stock_sucursales as $pl)
                    @if($pl->sucursal_id == 0)
                    @if($product->id == $pl->product_id)
                    @if($product->producto_tipo == "v")
                    <a href="javascript:void(0)" style="color: #007bff !important; cursor: pointer;" wire:click="MostrarStock({{$product->id}},0)">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, 3, ',', '.') }} Kg
                        @endif
                        <br>   
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, 3, ',', '.') }} Kg
                        @endif   
                    </a>
                    @else
                    @if($pl->stock < $product->alerts)
                    <text style="color:red;">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text style="color:red;">
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text> 
                    @else
                    <text>
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text>
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text>
                    @endif 
                    @endif
                    @endif
                    @endif
                    @endforeach   
                </td>
                @foreach($sucursales as $suc)
                <td {{ ($muestra_stock_otras_sucursales == 0) && ($suc->sucursal_id != $comercio_id) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>
                    @foreach($stock_sucursales as $pl)
                    @if($suc->sucursal_id == $pl->sucursal_id)
                    @if($product->id == $pl->product_id)
                    @if($product->producto_tipo == "v")
                    <a href="javascript:void(0)" style="color: #007bff !important; cursor: pointer;" wire:click="MostrarStock({{$product->id}},{{$pl->sucursal_id}})">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif   
                        <br> 
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif
                    </a>
                    @else
                    @if($pl->stock < $product->alerts)
                    <text style="color:red;">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text style="color:red;">
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text> 
                    @else
                    <text>
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text>
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, $digitos_cantidad_kg, ',', '.') }} Kg
                        @endif  
                    </text>
                    @endif 
                    @endif
                    @endif
                    @endif
                    @endforeach
                </td>
                @endforeach
                <td>
                    <a wire:click.prevent="Ver({{$product->id}})" class="me-3" href="javascript:void(0)">
                        <img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
                    </a>
                    
                    @if(Auth::user()->profile != "Cajero" )
                    
                    @can('editar productos')  
                    <a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$product->id}})" >
                        <img src="{{ asset('assets/pos/img/icons/edit.svg') }}" alt="img">
                    </a>					
					@endcan
												
					@can('eliminar productos')  
                    <a class="confirm-text" href="javascript:void(0)" onclick="Confirm('{{$product->id}}')"  >
                        <img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
                    </a>					
					@endcan 

                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>