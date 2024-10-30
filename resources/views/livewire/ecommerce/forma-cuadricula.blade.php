
                @if(0 < $selected_id && ((new \Jenssegers\Agent\Agent())->isMobile()))
                @include('livewire.ecommerce.mobile.form')
                @else
                <div style="background: rgba(239, 239, 239, 0.4); height: auto !important; padding-bottom: 120px !important;">
                
                @if(((new \Jenssegers\Agent\Agent())->isMobile()))
                <div style="color: {{$color}} !important; background-color: {{$background_color}} !important; width: 100%;  padding: 10px 0px;">
                <p style="padding-left:30px; color: {{$color}} !important; background-color: {{$background_color}} !important; text-rendering: optimizeSpeed; line-height: 1.5; font-family: Anton, Impact, 'sans-serif'; font-size: 22px;">Productos</p>
                </div>
                @else
                
                <h1 class="text-center pt-4 pb-3">Productos</h1>
                @endif
                
                <div class="container" style="max-width:1400px !important; height: 100%;">

                
                  @if(count($prod) < 1)
                  <tr>
                  <td colspan="7"><h5>No hay productos que coincidan con la busqueda.</h5></td>
                  </tr>
                  @else 
                  <div class="row mt-4">
                  
                @foreach($prod as $product)
                <div id="productos" class="col-md-3 productos mb-4">
                        <div id="product-item" class="product-item">
                            <div id="product-item-image" class="product-item-image">
                                <a style="cursor:pointer;"href="javascript:void(0)" href="javascript:void(0)"  wire:click="Add({{$product->id}})">
                                @if($product->image)
                                  <img style="width:100% !important;" src="{{ asset('storage/products/' . $product->image) }}" alt="{{$product->name}}"
                                        class="img-fluid">
                                @else
                                 <img style="width:100% !important;" src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" class="img-fluid">
                                @endif
                                </a>
                                <div id="cart-icon" class="cart-icon">

                                    <a  style="cursor:pointer;" href="javascript:void(0)" wire:click="Add({{$product->id}})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16.75" height="16.75"
                                            viewBox="0 0 16.75 16.75">
                                            <g id="Your_Bag" data-name="Your Bag" transform="translate(0.75)">
                                                <g id="Icon" transform="translate(0 1)">
                                                    <ellipse id="Ellipse_2" data-name="Ellipse 2" cx="0.682" cy="0.714"
                                                        rx="0.682" ry="0.714" transform="translate(4.773 13.571)"
                                                        fill="none" stroke="#1a2224" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="1.5" />
                                                    <ellipse id="Ellipse_3" data-name="Ellipse 3" cx="0.682" cy="0.714"
                                                        rx="0.682" ry="0.714" transform="translate(12.273 13.571)"
                                                        fill="none" stroke="#1a2224" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="1.5" />
                                                    <path id="Path_3" data-name="Path 3"
                                                        d="M1,1H3.727l1.827,9.564a1.38,1.38,0,0,0,1.364,1.15h6.627a1.38,1.38,0,0,0,1.364-1.15L16,4.571H4.409"
                                                        transform="translate(-1 -1)" fill="none" stroke="#1a2224"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5" />
                                                </g>
                                            </g>
                                        </svg>
                                    </a>
                                </div>
                                @if($product->stock < 1)
                                <div hidden class="agotado">
                                  Agotado
                                </div>
                                @endif
                            </div>
                            <div class="product-item-info">
                                <a style="cursor:pointer;" href="javascript:void(0)" onclick="Add()" wire:click="Add({{$product->id}})">{{$product->name}}</a>
                                <p>{{$product->category}}</p>
                                <div id="descripcion" class="descripcion">
                                  {{$product->descripcion}}
                                </div>
                                <span onclick="Add()" wire:click="Add({{$product->id}})" >
                                
                                 <!---- si el producto es variable ---->
                                @if($product->producto_tipo == "v")
                                
                                Seleccione una variacion
                                @endif
                                
                                @if($product->producto_tipo == "s")
                                
                                @foreach($precios as $pr)
                                
                                <!---- si el producto es simple ---->
                                
                                @if($pr->product_id == $product->id)
                                $ {{$pr->precio_lista}}
                                @endif
                               
                                @endforeach
                                
                                 @endif
                                
                                </span> <del hidden>$999</del>
                            </div>
                        </div>
                    </div>
                @endforeach

                @endif

                </div>
                  @endif
                  
                </div>
                {{$prod->links()}}
                </div>