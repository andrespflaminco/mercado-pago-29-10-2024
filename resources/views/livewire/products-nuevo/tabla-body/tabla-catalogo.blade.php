<tbody>
									    @foreach($data as $product)
										<tr>
											<td>
											    @can('accion en lote productos')  
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($product->id)}}"  class="mis-checkboxes" value="{{$product->id}}">
													<span class="checkmarks"></span>
												</label>
												@endcan
											</td>
											<td class="productimgname">
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
											<td>{{$product->barcode}}</td>
											<td>{{$product->category->name}}</td>
											<td>{{$product->nombre_marca}}</td>
											@if(Auth::user()->profile != "Cajero" )
										    @can('ver proveedores en catalogo')  
											<td>{{$product->nombre_proveedor}}</td>
											@endcan
											@endif
											<td>
											    @if($estado_filtro == 0)
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
												<a href="javascript:void(0)" onclick="ConfirmEliminarProducto('{{$product->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@endcan
												
												@endif
												
												@else
												<button class="btn btn-light" wire:click="RestaurarProductoProduct('{{$product->id}}')">Restaurar</button>
												@endif
												
											</td>
										</tr>
										@endforeach
									</tbody>