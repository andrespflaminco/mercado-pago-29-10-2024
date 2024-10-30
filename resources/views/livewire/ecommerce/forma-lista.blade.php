
                @if(0 < $selected_id && ((new \Jenssegers\Agent\Agent())->isMobile()))
                @include('livewire.ecommerce.mobile.form')
                @else
                <div style="background: rgba(239, 239, 239, 0.4); height: auto !important; padding-bottom: 120px !important;">
                
                @if(((new \Jenssegers\Agent\Agent())->isMobile()))
                <div style="color: {{$color}} !important; background-color: {{$background_color}} !important; width: 100%;  padding: 10px 0px;">
                <p style="padding-left:30px; color: {{$color}} !important; background-color: {{$background_color}} !important; text-rendering: optimizeSpeed; line-height: 1.5; font-family: Anton, Impact, 'sans-serif'; font-size: 22px;">Productos</p>
                </div>
                @endif
                
                <div class="container" style="max-width:1400px !important; height: 100%;">

                
                  @if(count($prod) < 1)
                  <tr>
                  <td colspan="7"><h5>No hay productos que coincidan con la busqueda.</h5></td>
                  </tr>
                  @else 
                  <div class="row mb-3">
                  
                  @foreach($prod as $product)

                  <a style="cursor:pointer;"href="javascript:void(0)" href="javascript:void(0)"  wire:click="Add({{$product->id}})" onclick="Add()">
                    <div  class="col-md-4 productos-list" >
                        <div class="product-item-list" style="border-radius: 7px;">
                            <div class="product-item-image-list">
                               
                                @if($product->image != null)
                               <img style="width: 121px !important; height: 100px; border-top-left-radius: 7px; border-bottom-left-radius: 7px;" src="{{ asset('storage/products/' . $product->image ) }}" alt="{{$product->name}}" class="img-fluid">
                                @else
                               <img style="width: 121px !important; height: 100px; border-top-left-radius: 7px; border-bottom-left-radius: 7px;" src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" class="img-fluid">
                                @endif
                                </a>
                                <div id="cart-icon" class="cart-icon-list">
                                </div>
                                @if($product->stock < 1)
                               <!--- <div hidden class="agotado">
                                  Agotado
                                </div>
                                -->
                                @endif
                            </div>
                            <div class="product-item-info-list">
                                <div style="padding:5%;">
                                <a style="cursor:pointer; color: #1A2224 !important; font-weight: 600 !important; font-size: 16px;" href="javascript:void(0)" onclick="Add()" wire:click="Add({{$product->id}})">{{$product->name}}</a>
                                <div style="margin-top:5%;">
                                 <!---- si el producto es variable ---->
                                @if($product->producto_tipo == "v")
                                <span onclick="Add()" >Seleccione una variacion</span> <del hidden>$999</del>
                                
                                @endif
                                
                                @if($product->producto_tipo == "s")
                                
                                @foreach($precios as $pr)
                                
                                <!---- si el producto es simple ---->
                                
                                @if($pr->product_id == $product->id)
                                <span onclick="Add()"  >$ {{$pr->precio_lista}}</span> <del hidden>$999</del>
                                @endif
                               
                                @endforeach
                                
                                 @endif   
                                 
                                 </div>
                                </div>
                               
                                 
                                
                            </div>
                        </div>
                    </div>
                     </a>

                  @endforeach


                @endif

                </div>
                  @endif
                  
                </div>
                
                <br>
                {{$prod->links()}}
                <br><br>
                
                </div>