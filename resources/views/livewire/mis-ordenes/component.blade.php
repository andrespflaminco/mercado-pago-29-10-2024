<header class="header">
    <div class="header-top">
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
    </div>

    <div class="header-bottom">




        <div class="container">
            <div class="d-none d-lg-block">
                <nav class="menu-area d-flex align-items-center">
                    <div class="logo">
                        <a href="javascript:void(0)">
                          @if($imagen != null)
                          <img  style="max-height: 70px;" src="{{ asset('storage/users/'.$imagen) }}" alt="logo" />
                          @else
                          <h6></h6>
                          @endif
                        </a>
                    </div>
                    
        
                    <div class="menu-icon ml-auto">
                        <ul>

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
                        <img  style="max-height: 70px;" src="{{ asset('storage/users/'.$imagen) }}" alt="logo" />
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
                      <li>

                       
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


<main>

<section class="cart-area">


      <!-- BreadCrumb Start-->
      <section class="breadcrumb-area mt-0">
          <div class="container">
              <div class="row">
                  <div class="col-lg-12">
                      <nav aria-label="breadcrumb">
                          <ol class="breadcrumb">
                              <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Mis pedidos </li>
                          </ol>
                      </nav>
                      <h5>Mis pedidos</h5>
                  </div>
              </div>
          </div>
      </section>
      <!-- BreadCrumb Start-->

      <!--Acount Area Start -->
      <section class="account">
          <div class="container">
              <div class="row">
                  <div class="col-lg-12">
                      <!-- Dashboard-Nav  Start-->
                      <div style="margin-bottom: 40px !important;" class="dashboard-nav">
                          <ul class="list-inline">
                              <li class="list-inline-item"><a href="{{ url('ecommerce-orders/'.$slug)}} " class="active">Mis pedidos</a></li>
                              <li class="list-inline-item">
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit()"
                                class="mr-0">
                              Cerrar sesion</a></li>
                                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                    @csrf
                                </form>


                          </ul>
                      </div>
                      <!-- Dashboard-Nav  End-->
                  </div>
                  <div class="col-lg-12 col-md-12">
                    <div class="widget-content widget-content-area">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-4">
                                <thead>
                                    <tr>
                                        <th>Numero de pedido</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Forma de pago</th>
                                        <th class="text-center">Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($orders as $o)
                                    <tr>
                                        <td># {{$o->id}}</td>
                                        <td>{{\Carbon\Carbon::parse($o->created_at)->format('d-m-Y H:i')}}</td>
                                        <td>$ {{$o->total - $o->descuento}}</td>
                                        <td>{{$o->metodo_pago}}</td>
                                        <td class="text-center"><span class="text-success">{{$o->status}}</span></td>
                                        <td class="text-center">
                                        <a href="{{ url('ver-orden' . '/' . $o->id) }}" target="_blank"  style="padding:10px; border: 1px solid #eee; color: white; background-color:#333;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                                        </a>

                                          <a href="{{ url('report-factura/pdf' . '/' . $o->id) }}" target="_blank"  style="padding:10px; border: 1px solid #eee; color: white; background-color:#333;"><i class="flaticon-dots mr-1"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                         </a>

                                      
                                        </td>
                                    </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>


          </div>
      </section>
      <!--Acount Area End -->


        </main>
