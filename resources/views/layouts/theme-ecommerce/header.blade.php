<!-- Header Area Start -->


<header class="header">
    <div class="header-top d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="header-top-wrapper">
                        <div class="header-top-info">
                            <div class="email">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16.95" height="13.4"
                                        viewBox="0 0 16.95 13.4">
                                        <g id="Mail" transform="translate(0.975 0.7)">
                                            <path id="Path_1" data-name="Path 1"
                                                d="M3.5,4h12A1.5,1.5,0,0,1,17,5.5v9A1.5,1.5,0,0,1,15.5,16H3.5A1.5,1.5,0,0,1,2,14.5v-9A1.5,1.5,0,0,1,3.5,4Z"
                                                transform="translate(-2 -4)" fill="none" stroke="#1a2224"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
                                            <path id="Path_2" data-name="Path 2" d="M17,6,9.5,11.25,2,6"
                                                transform="translate(-2 -4.5)" fill="none" stroke="#1a2224"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="text">
                                    <span>{{$data_e->email}}</span>
                                </div>
                            </div>
                            <div class="cta">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13.401" height="13.401"
                                        viewBox="0 0 13.401 13.401">
                                        <g id="Phone_Icon" data-name="Phone Icon" transform="translate(0.7 0.7)">
                                            <path id="Phone_Icon-2" data-name="Phone Icon"
                                                d="M14.111,10.984v1.806A1.206,1.206,0,0,1,12.8,14a11.956,11.956,0,0,1-5.207-1.849,11.754,11.754,0,0,1-3.62-3.613A11.9,11.9,0,0,1,2.117,3.313,1.205,1.205,0,0,1,3.317,2h1.81A1.206,1.206,0,0,1,6.334,3.036a7.719,7.719,0,0,0,.422,1.692A1.2,1.2,0,0,1,6.485,6l-.766.765a9.644,9.644,0,0,0,3.62,3.613l.766-.765a1.208,1.208,0,0,1,1.273-.271,7.76,7.76,0,0,0,1.7.422,1.205,1.205,0,0,1,1.038,1.222Z"
                                                transform="translate(-2.112 -2)" fill="none" stroke="#1a2224"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="text">
                                    <span>{{$data_e->phone}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="header-top-switcher">
                          <div class="text">
                              <span style="font-size: 14px;  color: #989BA7; line-height: 1.3;">
                                Powered by Flaminco
                              </span>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <!--Banner Area Start -->
          <div hidden class="banner-area">
            <div class="widget-content widget-content-area">
                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active m"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img class="d-block w-100" style="max-height: 400px; " src="https://fondosmil.com/fondo/17735.jpg" alt="First slide">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100" style="max-height: 400px; " src="https://fondosmil.com/fondo/74066.jpg" alt="Second slide">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100" style="max-height: 400px; " src="https://fondosmil.com/fondo/17735.jpg" alt="Third slide">
                                </div>
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>


              </div>
    </div>
    <div class="header-bottom">




        <div class="container">
            <div class="d-none d-lg-block">
                <nav class="menu-area d-flex align-items-center">
                    <div class="logo">
                        <a href="javascript:void(0)">
                          @if($imagen != null)
                          <a class="active" href="{{ url('tienda/'.$slug)}}" ><img  style="max-height: 80px;" src="{{ asset('storage/users/'.$imagen) }}" alt="logo" /></a>
                          @else
                          <h6></h6>
                          @endif
                        </a>
                    </div>
                    <ul class="main-menu d-flex align-items-center">
                        <li><a class="active" href="{{ url('tienda/'.$slug)}}" >Inicio</a></li>
                        <li>
                            <a href="javascript:void(0)">Categorias
                                <svg xmlns="http://www.w3.org/2000/svg" width="9.98" height="5.69"
                                    viewBox="0 0 9.98 5.69">
                                    <g id="Arrow" transform="translate(0.99 0.99)">
                                        <path id="Arrow-2" data-name="Arrow" d="M1474.286,26.4l4,4,4-4"
                                            transform="translate(-1474.286 -26.4)" fill="none" stroke="#1a2224"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
                                    </g>
                                </svg>
                            </a>
                            <ul class="sub-menu" style="max-height:400px; overflow: auto; width: auto;">
                              @foreach($categories_menu as $cm)

                              @if(0 < $cm->count())
                                <form name="add-blog-post-form"  method="post" action="{{ url('tienda/'.$slug)}}">
                                @csrf
                                <li style="border-bottom: solid 1px #eee;">
                               
                                <input hidden type="number" name="categoria" value="{{$cm->id}}"> 
                                <input hidden type="number" name="comercio_id" value="{{$cm->comercio_id}}"> 
                                <input style="padding: 5px 20px; display: block; color: #989BA7; font-size: 14px; line-height: 1.3; -webkit-transition: .3s; transition: .3s;     background: white;
                                  font-weight: 500; border:none !important; " type="submit" value="{{$cm->name}}">
                                
                                </li>
                                
                                
                                </form>
                                @else
                                <li>No hay categorias </li>
                                @endif
                              @endforeach
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:void(0)">Etiquetas
                                <svg xmlns="http://www.w3.org/2000/svg" width="9.98" height="5.69"
                                    viewBox="0 0 9.98 5.69">
                                    <g id="Arrow" transform="translate(0.99 0.99)">
                                        <path id="Arrow-2" data-name="Arrow" d="M1474.286,26.4l4,4,4-4"
                                            transform="translate(-1474.286 -26.4)" fill="none" stroke="#1a2224"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
                                    </g>
                                </svg>
                            </a>
                            <ul class="sub-menu" style="max-height:400px; overflow: auto; width: auto;">
                              @foreach($etiquetas_menu as $em)

                              @if(0 < $cm->count())
                                <form name="add-blog-post-form"  method="post" action="{{ url('tienda/'.$slug)}}">
                                @csrf
                                <li style="border-bottom: solid 1px #eee;">
                               
                                <input hidden type="number" name="etiqueta" value="{{$em->id}}"> 
                                <input hidden type="number" name="comercio_id" value="{{$em->comercio_id}}"> 
                                <input style="padding: 5px 20px; display: block; color: #989BA7; font-size: 14px; line-height: 1.3; -webkit-transition: .3s; transition: .3s;     background: white;
                                  font-weight: 500; border:none !important; " type="submit" value="{{$em->nombre}}">
                                
                                </li>
                                
                                
                                </form>
                                @else
                                <li>No hay categorias </li>
                                @endif
                              @endforeach
                            </ul>
                        </li>
                        @guest
                          <li><a href="{{ url('ecommerce-login/'.$slug)}}">Mi cuenta</a></li>
                        @else
                          <li><a href="{{ url('ecommerce-account/'.$slug)}}">Mi cuenta</a></li>
                        @endguest

                    </ul>
                    
                    
                    <form name="add-blog-post-form" id="form-id"  method="post" action="{{ url('tienda/'.$slug)}}">
                    @csrf
                    <input hidden type="number" name="comercio_id" value="{{$comercio_id}}"> 
                    <div class="search-bar">
                        
                        <input type="text" name="search" value="{{$search}}" placeholder="Busca por producto...">
                        
                        <div id="your-id" class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20.414" height="20.414"
                                viewBox="0 0 20.414 20.414">
                                <g id="Search_Icon" data-name="Search Icon" transform="translate(1 1)">
                                    <ellipse id="Ellipse_1" data-name="Ellipse 1" cx="8.158" cy="8" rx="8.158"
                                        ry="8" fill="none" stroke="#1a2224" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                    <line id="Line_4" data-name="Line 4" x1="3.569" y1="3.5"
                                        transform="translate(14.431 14.5)" fill="none" stroke="#1a2224"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </g>
                            </svg>
                        </div>
                    </div>
                    </form>
                    <div class="menu-icon ml-auto">
                        <ul>

                            <ul class="main-menu" style="padding-left:3px;">
                            <li class="d-none d-lg-block">
                                <a style="padding:0; margin-top:3px;" href="javascript:void(0)">

                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                    viewBox="0 0 22 22">
                                    <g id="Icon" transform="translate(-1524 -89)">
                                    <ellipse id="Ellipse_2" data-name="Ellipse 2" cx="0.909" cy="0.952"
                                    rx="0.909" ry="0.952" transform="translate(1531.364 108.095)"
                                    fill="none" stroke="#1a2224" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" />
                                    <ellipse id="Ellipse_3" data-name="Ellipse 3" cx="0.909" cy="0.952"
                                    rx="0.909" ry="0.952" transform="translate(1541.364 108.095)"
                                    fill="none" stroke="#1a2224" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" />
                                    <path id="Path_3" data-name="Path 3"
                                    d="M1,1H4.636L7.073,13.752a1.84,1.84,0,0,0,1.818,1.533h8.836a1.84,1.84,0,0,0,1.818-1.533L21,5.762H5.545"
                                    transform="translate(1524 89)" fill="none" stroke="#1a2224"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </g>
                                    </svg>

                                    <span class="cart">{{ $cart_ecommerce->totalCantidad() }}</span>
                                </a>
                                <ul style="display:block; width:330px; left: -289px;" class="sub-menu">
                                  @if ($cart_ecommerce->getContent()->count() > 0)

                                  @foreach ($cart_ecommerce->getContent()->sortByDesc('orderby_id') as $product)
                                  <li style="padding:10px;">
                                  <div class="item" style="display:flex;">
                                      <div class="image">
                                           @if($product['image'] != null)
                                          <img style="min-height: 90px;" src="{{ asset('storage/products/' . $product['image'] ) }}">
                                          @else
                                           <img style="min-height: 90px;" src="{{ asset('storage/products/noimg.png') }}">
                                          @endif
                                          
                                      </div>
                                      <div style="padding:0px 25px; font-size: 14px; box-sizing: border-box; display:block;">

                                      <div style="margin-top:7%;" class="name">
                                          <div class="name-text">
                                              <p> {{$product['name']}} </p>
                                          </div>

                                      </div>
                                      <div class="price">
                                          <span> $ {{$product['price']}}</span> <del hidden>$499.99</del>
                                      </div>
                                      <div class="quantity">
                                          <div class="product-pricelist-selector-quantity">

                                              <div  class="wan-spinner wan-spinner-4">
                                                  <a  style="max-width:35px !important;"href="javascript:void(0)" wire:click="Decrecer({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="minus">
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="11.98" height="6.69"
                                                          viewBox="0 0 11.98 6.69">
                                                          <path id="Arrow" d="M1474.286,26.4l5,5,5-5"
                                                              transform="translate(-1473.296 -25.41)" fill="none"
                                                              stroke="#989ba7" stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="1.4" />
                                                      </svg>
                                                  </a>
                                                  <input style="max-width:30px !important;" type="text" value="{{$product['qty']}}" min="1">
                                                  <a style="max-width:35px !important;" href="javascript:void(0)" wire:click="Incrementar({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="plus"><svg
                                                          xmlns="http://www.w3.org/2000/svg" width="11.98" height="6.69"
                                                          viewBox="0 0 11.98 6.69">
                                                          <g id="Arrow" transform="translate(10.99 5.7) rotate(180)">
                                                              <path id="Arrow-2" data-name="Arrow" d="M1474.286,26.4l5,5,5-5"
                                                                  transform="translate(-1474.286 -26.4)" fill="none"
                                                                  stroke="#1a2224" stroke-linecap="round"
                                                                  stroke-linejoin="round" stroke-width="1.4" />
                                                          </g>
                                                      </svg></a>

                                                      <a style="border-left: solid 1px #eee;" wire:click="removeProductFromCart({{$product['product_id']}} , '{{$product['referencia_variacion']}}')">
                                                        <svg style="margin: 0 auto !important; margin-right: 3px !important;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                      </a>


                                              </div>

                                          </div>
                                      </div>
                                      </div>

                                  </div>
                                  </li>
                                  @endforeach
                                  <li style="padding:15px 10px;  border-top: solid 1px #eee; " >
                                    <a type="button" href="{{ url('ecart/'.$slug)}}" class="btn btn-primary" style="color: white;" name="button">IR A FINALIZAR COMPRA</a>
                                  </li>
                                  @else
                                  <li>
                                  <div style="padding:25px;" class="item">
                                    No hay productos en el carrito
                                  </div>
                                  </li>
                                  @endif
                                  </ul>
                            </li>
                            </ul>

                            <li>
                                <a href="cart.html">


                                </a>
                            </li>
                            <li>
                              @guest

                          <a href="{{ url('ecommerce-login/'.$slug)}}"><svg xmlns="http://www.w3.org/2000/svg" width="18"
                                  height="20" viewBox="0 0 18 20">
                                  <g id="Account" transform="translate(1 1)">
                                      <path id="Path_86" data-name="Path 86"
                                          d="M20,21V19a4,4,0,0,0-4-4H8a4,4,0,0,0-4,4v2"
                                          transform="translate(-4 -3)" fill="none" stroke="#000"
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                      <circle id="Ellipse_9" data-name="Ellipse 9" cx="4" cy="4" r="4"
                                          transform="translate(4)" fill="#fff" stroke="#000"
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                  </g>
                              </svg></a>


                                     @else
                                     <a href="{{ url('ecommerce-account/'.$slug)}}"><svg xmlns="http://www.w3.org/2000/svg" width="18"
                                             height="20" viewBox="0 0 18 20">
                                             <g id="Account" transform="translate(1 1)">
                                                 <path id="Path_86" data-name="Path 86"
                                                     d="M20,21V19a4,4,0,0,0-4-4H8a4,4,0,0,0-4,4v2"
                                                     transform="translate(-4 -3)" fill="none" stroke="#000"
                                                     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                 <circle id="Ellipse_9" data-name="Ellipse 9" cx="4" cy="4" r="4"
                                                     transform="translate(4)" fill="#fff" stroke="#000"
                                                     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                             </g>
                                         </svg></a>
                                           @endguest
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <!-- Mobile Menu -->
            <aside class="d-lg-none">
                <div id="mySidenav" class="sidenav">
                    <div class="close-mobile-menu">
                        <a href="javascript:void(0)" id="menu-close" class="closebtn"
                            onclick="closeNav()">&times;</a>
                    </div>
                     <form id="form-id2" name="add-blog-post-form" id="form-id"  method="post" action="{{ url('tienda/'.$slug)}}">
                    @csrf
                    <input hidden type="number" name="comercio_id" value="{{$comercio_id}}"> 
                    
                    <div class="search-bar">
                        <input type="text"  name="search" value="{{$search}}" placeholder="Buscar un producto...">
                        <div id="your-id2" class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20.414" height="20.414"
                                viewBox="0 0 20.414 20.414">
                                <g id="Search_Icon" data-name="Search Icon" transform="translate(1 1)">
                                    <ellipse id="Ellipse_1" data-name="Ellipse 1" cx="8.158" cy="8" rx="8.158"
                                        ry="8" fill="none" stroke="#1a2224" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                    <line id="Line_4" data-name="Line 4" x1="3.569" y1="3.5"
                                        transform="translate(14.431 14.5)" fill="none" stroke="#1a2224"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </g>
                            </svg>
                        </div>
                    </div>
                    
                    </form>
                    <li><a href="{{ url('tienda/'.$slug)}}" >Inicio</a></li>
                    @guest
                      <li><a href="{{ url('ecommerce-login/'.$slug)}}">Mi cuenta</a></li>
                    @else
                      <li><a href="{{ url('ecommerce-account/'.$slug)}}">Mi cuenta</a></li>
                    @endguest
                    <li>
                        <a href="javascript:void(0)">Categorias
                            <svg xmlns="http://www.w3.org/2000/svg" width="9.98" height="5.69"
                                viewBox="0 0 9.98 5.69">
                                <g id="Arrow" transform="translate(0.99 0.99)">
                                    <path id="Arrow-2" data-name="Arrow" d="M1474.286,26.4l4,4,4-4"
                                        transform="translate(-1474.286 -26.4)" fill="none" stroke="#1a2224"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
                                </g>
                            </svg>
                        </a>
                        <ul class="sub-menu">
                         @foreach($categories_menu as $cm)

                              @if($cm->count() > 0)
                                <form name="add-blog-post-form"  method="post" action="{{ url('tienda/'.$slug)}}">
                                @csrf
                                <li style="border-bottom: solid 1px #eee;">
                               
                                <input hidden type="number" name="categoria" value="{{$cm->id}}"> 
                                <input hidden type="number" name="comercio_id" value="{{$cm->comercio_id}}"> 
                                <input style="padding: 5px 20px; display: block; color: #989BA7; font-size: 14px; line-height: 1.3; -webkit-transition: .3s; transition: .3s;     background: #FCFCFC;
                                  font-weight: 500; border:none !important; " type="submit" value="{{$cm->name}}">
                                
                                </li>
                                
                                
                                </form>
                                @else
                                <li>No hay categorias </li>
                                @endif
                              @endforeach
                        </ul>
                    </li>
                </div>
                <div class="mobile-nav d-flex align-items-center justify-content-between">
                    
                    <div class="logo">
                        <a href="javascript:void(0)">
                        @if($imagen != null)
                        <a class="active" href="{{ url('tienda/'.$slug)}}" ><img  style="max-height: 80px;" src="{{ asset('storage/users/'.$imagen) }}" alt="logo" /></a>
                        @else
                        <h6></h6>
                        @endif

                    </div>
                    <div class="search-bar">
                        <input type="text" placeholder="Buscar por producto...">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20.414" height="20.414"
                                viewBox="0 0 20.414 20.414">
                                <g id="Search_Icon" data-name="Search Icon" transform="translate(1 1)">
                                    <ellipse id="Ellipse_1" data-name="Ellipse 1" cx="8.158" cy="8" rx="8.158"
                                        ry="8" fill="none" stroke="#1a2224" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" />
                                    <line id="Line_4" data-name="Line 4" x1="3.569" y1="3.5"
                                        transform="translate(14.431 14.5)" fill="none" stroke="#1a2224"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div style="display: flex !important; " class="menu-icon">
                      <ul class="main-menu" style="padding-left:3px;">
                      <li class="d-none d-lg-block">
                          <a style="padding:0 !important; margin-top:0px;" href="javascript:void(0)">

                              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                              viewBox="0 0 22 22">
                              <g id="Icon" transform="translate(-1524 -89)">
                              <ellipse id="Ellipse_2" data-name="Ellipse 2" cx="0.909" cy="0.952"
                              rx="0.909" ry="0.952" transform="translate(1531.364 108.095)"
                              fill="none" stroke="#1a2224" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" />
                              <ellipse id="Ellipse_3" data-name="Ellipse 3" cx="0.909" cy="0.952"
                              rx="0.909" ry="0.952" transform="translate(1541.364 108.095)"
                              fill="none" stroke="#1a2224" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" />
                              <path id="Path_3" data-name="Path 3"
                              d="M1,1H4.636L7.073,13.752a1.84,1.84,0,0,0,1.818,1.533h8.836a1.84,1.84,0,0,0,1.818-1.533L21,5.762H5.545"
                              transform="translate(1524 89)" fill="none" stroke="#1a2224"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                              </g>
                              </svg>

                              <span class="cart">{{ $cart_ecommerce->totalCantidad() }}</span>
                          </a>
                          <ul style="display:block;  width: 350px;  top: 65px; left: -218px !important;" class="sub-menu">
                            @if ($cart_ecommerce->getContent()->count() > 0)

                            @foreach ($cart_ecommerce->getContent() as $product)
                            <li style="padding:10px;">
                            <div class="item" style="display:flex;">
                                <div class="image">
                                   
                                           @if($product['image'] != null)
                                          <img style="min-height: 90px; width: 90px;" src="{{ asset('storage/products/' . $product['image'] ) }}">
                                          @else
                                         <img style="min-height: 90px; width: 90px;" src="{{ asset('storage/products/noimg.png') }}">
                                          @endif
                                </div>
                                <div style="padding:0px 25px; font-size: 14px; box-sizing: border-box; display:block;">

                                <div style="margin-top:7%;" class="name">
                                    <div class="name-text">
                                        <p> {{$product['name']}} </p>
                                    </div>

                                </div>
                                <div class="price">
                                    <span> $ {{$product['price']}}</span> <del hidden>$499.99</del>
                                </div>
                                <div class="quantity">
                                    <div class="product-pricelist-selector-quantity">

                                        <div  class="wan-spinner wan-spinner-4">
                                            <a  style="max-width:35px !important;"href="javascript:void(0)" wire:click="Decrecer({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="minus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11.98" height="6.69"
                                                    viewBox="0 0 11.98 6.69">
                                                    <path id="Arrow" d="M1474.286,26.4l5,5,5-5"
                                                        transform="translate(-1473.296 -25.41)" fill="none"
                                                        stroke="#989ba7" stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.4" />
                                                </svg>
                                            </a>
                                            <input style="max-width:30px !important;" type="text" value="{{$product['qty']}}" min="1">
                                            <a style="max-width:35px !important;" href="javascript:void(0)" wire:click="Incrementar({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="plus"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="11.98" height="6.69"
                                                    viewBox="0 0 11.98 6.69">
                                                    <g id="Arrow" transform="translate(10.99 5.7) rotate(180)">
                                                        <path id="Arrow-2" data-name="Arrow" d="M1474.286,26.4l5,5,5-5"
                                                            transform="translate(-1474.286 -26.4)" fill="none"
                                                            stroke="#1a2224" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="1.4" />
                                                    </g>
                                                </svg></a>

                                                <a style="border-left: solid 1px #eee;" wire:click="removeProductFromCart({{$product['product_id']}} , '{{$product['referencia_variacion']}}')">
                                                  <svg style="margin: 0 auto !important; margin-right: 3px !important;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>


                                        </div>

                                    </div>
                                </div>
                                </div>

                            </div>
                            </li>
                            @endforeach
                            <li style="padding:15px 10px;  border-top: solid 1px #eee; " >
                              <a type="button" href="{{ url('ecart/'.$slug)}}" class="btn btn-primary" style="color: white;" name="button">IR A FINALIZAR COMPRA</a>
                            </li>
                            @else
                            <li>
                            <div style="padding:25px;" class="item">
                              No hay productos en el carrito
                            </div>
                            </li>
                            @endif
                            </ul>
                      </li>
                      </ul>

                        <ul>

                            <li>
                                 @guest
                                <a href="{{ url('ecommerce-login/'.$slug)}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="20"
                                        viewBox="0 0 18 20">
                                        <g id="Account" transform="translate(1 1)">
                                            <path id="Path_86" data-name="Path 86"
                                                d="M20,21V19a4,4,0,0,0-4-4H8a4,4,0,0,0-4,4v2"
                                                transform="translate(-4 -3)" fill="none" stroke="#000"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            <circle id="Ellipse_9" data-name="Ellipse 9" cx="4" cy="4" r="4"
                                                transform="translate(4)" fill="#fff" stroke="#000"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </g>
                                    </svg>
                                </a>
                                @else 
                                 <a href="{{ url('ecommerce-account/'.$slug)}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="20"
                                        viewBox="0 0 18 20">
                                        <g id="Account" transform="translate(1 1)">
                                            <path id="Path_86" data-name="Path 86"
                                                d="M20,21V19a4,4,0,0,0-4-4H8a4,4,0,0,0-4,4v2"
                                                transform="translate(-4 -3)" fill="none" stroke="#000"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            <circle id="Ellipse_9" data-name="Ellipse 9" cx="4" cy="4" r="4"
                                                transform="translate(4)" fill="#fff" stroke="#000"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </g>
                                    </svg>
                                </a>
                                @endguest
                            </li>
                        </ul>
                    </div>
                    <div class="hamburger-menu">
                        <a style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</a>
                    </div>
                </div>
            </aside>
            <!-- Body overlay -->
            <div class="overlay" id="overlayy"></div>
        </div>
    </div>
</header>
<!-- Header Area End -->
