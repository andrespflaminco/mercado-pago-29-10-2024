<div style="{{$paso2}}">
                                                <div>
                                                <div class="mb-4">
                                                    <h5>Productos seleccionados</h5>
                                                </div>
                                                <div class="table-responsive">
                								<table class="table">
                									<thead>
                										<tr>
                											<th hidden>
                												<input type="checkbox" id="select-all" checked>
                											</th>
                											<th>Nombre del producto</th>
                											<th>SKU</th>
                											<th>Categoria </th>
                											<th>Almacen</th>
                											<th>Proveedor</th>
                										</tr>
                									</thead>
                									<tbody>
                									    @foreach($data as $product)
                										<tr>
                											<td hidden>
                											    <input type="checkbox" tu-attr-id="{{($product->id)}}" wire:model="" class="mis-checkboxes" value="{{$product->id}}" checked>
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
                											<td>{{$product->nombre_categoria}}</td>
                											<td>{{$product->nombre_almacen}}</td>
                											<td>{{$product->nombre_proveedor}}</td>
                										</tr>
                										@endforeach
                									</tbody>
                								</table>
							</div>
                                                <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                    <li class="previous"><a href="javascript: void(0);" wire:click="Paso1()" class="btn btn-primary" onclick="nextTab()"><i
                                                        class="bx bx-chevron-left me-1"></i> ANTERIOR</a></li>
                                                    <li class="next"><a href="javascript: void(0);" wire:click="Paso3()" class="btn btn-primary" onclick="nextTab()">SIGUIENTE <i
                                                    class="bx bx-chevron-right ms-1"></i></a></li>
                                                </ul>
                                                </div>
                                            </div>