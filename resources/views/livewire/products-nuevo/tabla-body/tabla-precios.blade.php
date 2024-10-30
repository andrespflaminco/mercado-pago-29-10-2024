                                    <tbody>
									    @foreach($data as $product)

										@if($product->producto_tipo == "s")
										@include('livewire.products-nuevo.tabla-body.listado-precios-producto-simple')
                                        @endif
                                        
                                		@if($product->producto_tipo == "v")
										@include('livewire.products-nuevo.tabla-body.listado-precios-producto-variable')
                                        @endif

                                        
								
										
										@endforeach
									</tbody>