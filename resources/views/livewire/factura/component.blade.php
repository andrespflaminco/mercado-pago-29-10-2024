<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>SISTEMA PDV - Flaminco </title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>


		<script src="{{ asset('assets/js/loader.js') }}"></script>
		<link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />

		<link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">

		<link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/structure.css') }}" rel="stylesheet" type="text/css" class="structure" />

		<link href="{{ asset('plugins/font-icons/fontawesome/css/fontawesome.css') }}" rel="stylesheet" type="text/css">
		<link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" type="text/css" />

		<link href="{{ asset('assets/css/elements/avatar.css') }}" rel="stylesheet" type="text/css" />

		<link href="{{ asset('plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />


		<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />

		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/widgets/modules-widgets.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">

		 <link href="{{ asset('assets/css/apps/scrumboard.css') }}" rel="stylesheet" type="text/css" />
		 <link href="{{ asset('assets/css/apps/notes.css') }}" rel="stylesheet" type="text/css" />

		 <!--  BEGIN CUSTOM STYLE FILE  -->
		 <link href="{{ asset('assets/css/scrollspyNav.css') }} " rel="stylesheet" type="text/css" />
		 <link href="{{ asset('assets/css/components/tabs-accordian/custom-accordions.css') }} " rel="stylesheet" type="text/css" />
		 <!--  END CUSTOM STYLE FILE  -->
		 <!--  BEGIN CUSTOM STYLE FILE  -->
		 <link href="{{ asset('assets/css/scrollspyNav.css') }} " rel="stylesheet" type="text/css" />
		  <link href="{{ asset('plugins/select2/select2.min.css') }} " rel="stylesheet" type="text/css" />
		 <!--  END CUSTOM STYLE FILE  -->

		 <!--  BEGIN CUSTOM STYLE FILE  -->
		 <link href="{{ asset('assets/css/apps/invoice.css') }} " rel="stylesheet" type="text/css" />
		 <!--  END CUSTOM STYLE FILE  -->

		<style>
    .invoice-inbox .invoice-header-section {
    	display: flex !important;
    	justify-content: space-between;
    	padding: 17px 20px;
    	border-bottom: 1px solid #ebedf2;
    }
    .invoice-inbox {
        position: relative;
        overflow-x: hidden;
        overflow-y: auto;
        min-height: 1000px !important;
        max-width: 100%;
        width: 100%;
        height: calc(100vh - 213px);
    }
    .content-section {
        padding: 36px 55px !important;
    }
			aside {
				display: none!important;
			}
			.page-item.active .page-link {
				z-index: 3;
				color: #fff;
				background-color: #3b3f5c;
				border-color: #3b3f5c;
			}

			@media (max-width: 480px)
			{
				.mtmobile {
					margin-bottom: 20px!important;
				}
				.mbmobile {
					margin-bottom: 10px!important;
				}
				.hideonsm {
					display: none!important;
				}
				.inblock {
					display: block;
				}
			}

			/*sidebar background*/
			.sidebar-theme #compactSidebar {
				background: #191e3a!important;
			}

			/*sidebar collapse background */
			.header-container .sidebarCollapse {
				color: #3B3F5C!important;
			}

			.navbar .navbar-item .nav-item form.form-inline input.search-form-control {
				font-size: 15px;
				background-color: #3B3F5C!important;
				padding-right: 40px;
				padding-top: 12px;
				border: none;
				color: #fff;
				box-shadow: none;
				border-radius: 30px;
			}


		</style>


		 <link href="{{ asset('plugins/flatpickr/flatpickr.dark.css') }}" rel="stylesheet" type="text/css" />

		@livewireStyles

</head>

    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

		<head>
		  <style media="screen">
		    .navbar .navbar-item .nav-item.theme-logo a img {
		      width: 118px !important;
		      height: 40px !important;
		      border-radius: 5px !important;
		    }
		  </style>

		</head>
		<div class="header-container fixed-top">
		        <header class="header navbar navbar-expand-sm justify-content-between">

		          <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3" y2="6"></line><line x1="3" y1="12" x2="3" y2="12"></line><line x1="3" y1="18" x2="3" y2="18"></line></svg>
		          </a>

		            <ul class="navbar-item flex-row">

                  <li class="nav-item theme-logo">
                      <a href="pos">
                          <img src="assets/img/LOGO_03.png" class="navbar-logo" alt="logo">
                      </a>
                  </li>


		            </ul>


		            <ul class="navbar-item flex-row navbar-dropdown">


		                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
		                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                        <i class="far fa-user text-dark"></i>
		                    </a>
		                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="userProfileDropdown">
		                        <div class="user-profile-section">
		                            <div class="media mx-auto">

		                                <div class="media-body">
		                                    <h5>Bienvenido,</h5>
		                                    <h5>{{Auth::user()->name}}</h5>
		                                </div>
		                            </div>
		                        </div>


		                        <div class="dropdown-item">
		                            <a href="{{ route('logout') }}"
		                            onclick="event.preventDefault(); document.getElementById('logout-form').submit()"
		                            >
		                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> <span>Salir</span>
		                            </a>
		                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
		                                @csrf
		                            </form>
		                        </div>
		                    </div>
		                </li>
		            </ul>
		        </header>
		    </div>


    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
         @include('layouts.theme.sidebar')
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">

            <div class="layout-px-spacing">

							<div class="row sales layout-top-spacing">

								<div class="col-sm-12">
									<div class="widget widget-chart-one">

										<div class="widget-content">
											<div class="page-header">
													<div class="page-title">
															<h3>Factura {{$ventaId}}</h3>
													</div>
											</div>
                        @include('livewire.factura.sales-detail')
                        
											<div class="row invoice layout-top-spacing">
													<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

															<div class="doc-container">


																	<div class="invoice-container">
																			<div class="invoice-inbox">



                                          @foreach($mail as $m)
																					<div style="justify-content: flex-end !important;" class="invoice-header-section">
                                            @if(session('status'))
                                            <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                                            @endif



																							<div style="float:right !important;">

                                                @if ($hojar != '')

                                                @foreach ($hoja_ruta as $hr)

                                                <button type="button" class="btn btn-dark" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" data-toggle="modal" data-target="#tabsModal">
                                                  ENTREGA: {{\Carbon\Carbon::parse($hr->fecha)->format('d-m-Y')}} ({{$hr->turno}})
                                                </button>

                                                @endforeach

                                                @else
                                                <button type="button" class="btn btn-dark" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" data-toggle="modal" data-target="#tabsModal">
                                                AGREGAR A HOJA DE RUTA
                                                </button>
                                                @endif




                                      	@include('livewire.factura.form-hoja-ruta')
                                        @include('livewire.factura.form-hoja-ruta-nueva')
                                        @include('livewire.factura.form-pagos')






                                                <div style=" margin-bottom: 0 !important; " class="btn-group mb-4 mr-2" role="group">

                                                  @if($m->status == 'Pendiente')
                                                    <button id="btndefault" type="button" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> {{$m->status}} </button>

                                                    <div class="dropdown-menu" aria-labelledby="btndefault">
                                                      <a href="{{ url('cambio-estado/En proceso' . '/' . $ventaId) }}" style="width: 100%; padding: 3px !important;"
                                                          class="btn btn-secondary mb-2">
                                                          En proceso
                                                      </a>
                                                      <a href="{{ url('cambio-estado/Entregado' . '/' . $ventaId) }}" style="width: 100%; padding: 3px !important;"
                                                          class="btn btn-success mb-2">
                                                          Entregado
                                                      </a>
                                                      <a href="{{ url('cambio-estado/Cancelado' . '/' . $ventaId) }}" style="width: 100%; padding: 3px !important;"
                                                          class="btn btn-danger mb-2">
                                                          Cancelado
                                                      </a>

                                                    </div>
                                                    @endif

                                                    @if($m->status == 'En proceso')
                                                      <button id="btndefault" type="button" style="    min-width: 130px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; padding: 3px !important;" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> {{$m->status}} </button>

                                                      <div class="dropdown-menu" aria-labelledby="btndefault">
                                                        <a href="{{ url('cambio-estado/Pendiente' . '/' . $ventaId) }}"  style="width: 100%; padding: 3px !important;"
                                                            class="btn btn-warning mb-2">
                                                            Pendiente
                                                        </a>
                                                        <a href="{{ url('cambio-estado/Entregado' . '/' . $ventaId) }}"  style="width: 100%; padding: 3px !important;"
                                                            class="btn btn-success mb-2">
                                                            Entregado
                                                        </a>
                                                        <a href="{{ url('cambio-estado/Cancelado' . '/' . $ventaId) }}"  style="width: 100%; padding: 3px !important;"
                                                            class="btn btn-danger mb-2">
                                                            Cancelado
                                                        </a>

                                                      </div>
                                                      @endif

                                                      @if($m->status == 'Entregado')
                                                        <button id="btndefault" type="button" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> {{$m->status}} </button>

                                                        <div class="dropdown-menu" aria-labelledby="btndefault">
                                                          <a href="{{ url('cambio-estado/Pendiente' . '/' . $ventaId) }}"  style="width: 100%; padding: 3px !important;"
                                                              class="btn btn-warning mb-2">
                                                              Pendiente
                                                          </a>
                                                          <a href="{{ url('cambio-estado/En proceso' . '/' . $ventaId) }}" style="width: 100%; padding: 3px !important;"
                                                              class="btn btn-secondary mb-2">
                                                              En proceso
                                                          </a>
                                                          <a href="{{ url('cambio-estado/Cancelado' . '/' . $ventaId) }}"  style="width: 100%; padding: 3px !important;"
                                                              class="btn btn-danger mb-2">
                                                              Cancelado
                                                          </a>

                                                        </div>
                                                        @endif

                                                        @if($m->status == 'Cancelado')
                                                          <button id="btndefault" type="button" style="    min-width: 130px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; padding: 3px !important;" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> {{$m->status}} </button>

                                                          <div class="dropdown-menu" aria-labelledby="btndefault">
                                                            <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('store-form')}}">
                                                            @csrf

                                                            <button style="width: 100%; padding: 3px !important;"
                                                                class="btn btn-warning mb-2">
                                                                Pendiente
                                                            </button>
                                                            <a  href="{{ url('cambio-estado/En proceso' . '/' . $ventaId) }}"  style="width: 100%; padding: 3px !important;"
                                                                class="btn btn-secondary mb-2">
                                                                En proceso
                                                            </a>
                                                            <a href="{{ url('cambio-estado/Entregado' . '/' . $ventaId) }}" style="width: 100%;  padding: 3px !important;"
                                                                class="btn btn-success mb-2">
                                                                Entregado
                                                            </a>

                                                          </div>
                                                          @endif





                                                </div>
                                                  <a style="color: #515365 !important;" type="button" data-toggle="modal" data-target="#exampleModal"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>   </a>



																							  <a style="margin-left: 10px !important;" href="{{ url('report-email/pdf' . '/' . $ventaId  . '/' . $m->email) }}" > <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> </a>

                                                @endforeach

																							  <a style="margin-left: 10px !important;" href="{{ url('report-factura/pdf' . '/' . $ventaId) }}" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Imprimir"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg></a>

																							</div>


																					</div>








                                          <div id="contenido" >
                                          <div class="invoice-header-section">
                                            <div class="row">

                                              <div class="col-sm-12 col-md-12">
                                                <div class="col-sm-12 col-md-12">
                                                <h4> $ Pagos </h4>
                                                </div>

                                                <div class="col-sm-12 col-md-12">
                                                  <div class="form-group">
                                                    <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
                                                        <table class="multi-table table table-hover" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Fecha</th>
                                                                    <th class="text-center">Pago</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($pagos1 as $p1)
                                                                <tr>
                                                                    <td class="text-center">{{\Carbon\Carbon::parse( $p1->fecha_factura)->format('d-m-Y')}}</td>
                                                                    <td class="text-center">$ {{ number_format($p1->cash,2) }}</td>

                                                                </tr>
                                                                @endforeach
                                                                @foreach($pagos2 as $p2)
                                                                @if ($p2->monto > 0)
                                                                <tr>
                                                                    <td class="text-center">{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
                                                                    <td class="text-center">$ {{ number_format($p2->monto,2) }}</td>
                                                                    <td class="text-center">

                                                                      <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('store-form')}}">
                                                                      @csrf

                                                                      <a href="javascript:void(0)" onclick="Confirm('{{$ventaId}}')" >
                                                                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                                       </a>

                                                                       </form>


                                                                    </td>


                                                                </tr>

                                                                  @endif
                                                                  @endforeach





                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th class="text-center">Total </th>
                                                                    <th class="text-center">$ {{number_format($suma_monto+$suma_cash,2)}}</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>



                                                 </div>

                                                </div>
                                                @if(($suma_monto+$suma_cash) < $tot)

                                                <strong>Deuda: $ {{$tot - ($suma_monto+$suma_cash) }}</strong>
                                                <br><br>
                                                <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('store-form')}}">
                                                @csrf
                                                 <div class="form-group">
                                                   <label for="exampleInputEmail1">Agregar pago</label>
                                                   <input hidden name="id_factura" value="{{$ventaId}}" class="form-control" required="">
                                                   <div class="input-group mb-4">

                                                   <input autocomplete="off" type="text" id="title" name="monto" class="form-control" required="">
                                                   <div class="input-group-append">
                                                             <button type="submit" class="btn btn-dark">+</button>

                                                  </div>
                                                  </div>
                                                 </div>

                                               </form>
                                               @endif
                                              </div>
                                            </div>
                                          </div>
                                          </div>



																					<div class="">

																							<div style="min-height: 800px !important;" id="seleccion" class="invoice-00001">
																									<div class="content-section  animated animatedFadeInUp fadeInUp">

																											<div class="row inv--head-section">

																													<div class="col-sm-6 col-12">
																															<h3 class="in-heading">DETALLE DE VENTA</h3>
																													</div>
																													<div class="col-sm-6 col-12 align-self-center text-sm-right">
																															<div class="company-info">
                                                                @foreach ($usuario as $u)

                                                                @if($u->image != null)
                                                                <img  width="100" class="rounded"
                                                                src="{{ asset('storage/users/'.$u->image) }}"
                                                                >
                                                                @else
                                                                <h5 class="inv-brand-name">{{$u->name}}</h5>
                                                                @endif


																															</div>

                                                              @endforeach
																													</div>

																											</div>

																											<div class="row inv--detail-section">

																													<div class="col-sm-7 align-self-center">
																															<p class="inv-to">Cliente:</p>
																													</div>

																													<div class="col-sm-5 align-self-center  text-sm-right order-sm-0 order-1">
																															<p class="inv-detail-title"> Vendedor : {{Auth::user()->name}} </p>
																													</div>

																													<div class="col-sm-7 align-self-center">
																															@foreach($detalle_cliente as $c)
																															<p class="inv-customer-name">{{$c->nombre}}</p>
																															<p class="inv-street-addr">{{$c->direccion}},{{$c->localidad}}. {{$c->provincia}}</p>
																															<p class="inv-email-address">{{$c->email}}</p>
																															<p class="inv-email-address">{{$c->telefono}}</p>
																														@endforeach
																													</div>
																													<div class="col-sm-5 align-self-center  text-sm-right order-2">
																															<p class="inv-list-number"><span class="inv-title">Numero de detalle de venta : </span> <span class="inv-number"># {{$ventaId}}</span></p>
																															<p class="inv-created-date"><span class="inv-title">Fecha : </span>
																																@foreach($fecha as $f)
																																 <span class="inv-date">{{\Carbon\Carbon::parse($f->created_at)->format('d-m-Y')}}</span></p>
																																 @endforeach

																													</div>
																											</div>

																											<div class="row inv--product-table-section">
																													<div class="col-12">
																															<div class="table-responsive">
																																	<table class="table">
																																			<thead class="">
																																					<tr>
																																							<th scope="col">Fila</th>
																																							<th scope="col">Producto</th>
                                                                              <th scope="col">Observaciones</th>
																																							<th class="text-right" scope="col">Precio</th>
																																							<th class="text-right" scope="col">Cantidad</th>
																																							<th class="text-right" scope="col">Subtotal</th>
																																					</tr>
																																			</thead>
																																			<tbody>
																																				<?php $i = 1; ?>
																																				@foreach($detalle_venta as $item)
																																					<tr>
																																							<td><?php echo $i++; ?></td>
																																							<td>{{$item->product}}</td>
                                                                              <td>{{$item->comentario}}</td>
																																							<td class="text-right"> $ {{number_format($item->price,2)}}</td>
																																							<td class="text-right">{{number_format($item->quantity,2)}}</td>
																																							<td class="text-right">$ {{number_format($item->price*$item->quantity,2)}}</td>
																																					</tr>
																																					@endforeach
																																			</tbody>
																																	</table>
																															</div>
																													</div>
																											</div>

																											<div class="row mt-4">
																													<div class="col-sm-5 col-12 order-sm-0 order-1">
																															<div class="inv--payment-info">
																																	<div class="row">
																																			<div class="col-sm-12 col-12">
																																					<h6 class=" inv-title">Informacion adicional:</h6>
																																			</div>
																																			@foreach($total_total as $t)
																																			<div class="col-sm-4 col-12">
																																					<p class=" inv-subtitle">Forma de pago: </p>
																																			</div>
																																			<div class="col-sm-8 col-12">
																																					<p class="">{{$t->metodo_pago}}</p>
																																			</div>
																																			@if ($t->observaciones != '')
																																			<div class="col-sm-4 col-12">
																																					<p class=" inv-subtitle">Observaciones: </p>
																																			</div>
																																			<div class="col-sm-8 col-12">
																																					<p class="">{{$t->observaciones}}</p>
																																			</div>
																																			@else
																																			<div class="col-sm-4 col-12">
																																					<p class=" inv-subtitle"> </p>
																																			</div>

																																			@endif
                                                                      @if ($t->nota_interna != '')
                                                                      <div style="width: 100%;     border: solid 1px  #e0e6ed; margin-top: 15% !important;">


																																			<div   class="col-sm-4 col-12">
																																					<p class=" inv-subtitle">Nota interna: </p>
																																			</div>
																																			<div  class="col-sm-8 col-12">
																																					<p class="">{{$t->nota_interna}}</p>
																																			</div>
                                                                      </div>
																																			@else
																																			<div class="col-sm-4 col-12">
																																					<p class=" inv-subtitle"> </p>
																																			</div>

																																			@endif
																																	</div>
																															</div>
																													</div>
																													<div class="col-sm-7 col-12 order-sm-1 order-0">
																															<div class="inv--total-amounts text-sm-right">
																																	<div class="row">
																																			<div class="col-sm-8 col-7">
																																					<p class="">Sub Total: </p>
																																			</div>
																																			<div class="col-sm-4 col-5">
																																					<p class=""> $ {{$t->total}}</p>

																																			</div>

																																			<div class="col-sm-8 col-7 grand-total-title">
																																					<h4 class="">Total : </h4>
																																			</div>
																																			<div class="col-sm-4 col-5 grand-total-amount">
																																					<h4 class=""> $ {{$t->total}}</h4>
																																			</div>
																																				@endforeach
																																	</div>
																															</div>
																													</div>
																											</div>

																									</div>
																							</div>

																					</div>


																			</div>



																	</div>

															</div>

													</div>
											</div>
											</div>



									</div>


								</div>

							</div>

            </div>


            @include('layouts.theme.footer')
        </div>
        <!--  END CONTENT AREA  -->


    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    @include('layouts.theme.scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

<script>
    document.addEventListener('DOMContentLoaded', function(){
        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },

            }

        })


        //EVENTOS
        window.livewire.on('hide-modal', Msg =>{
            $('#modalDetails').modal('hide')
        })
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })
    })

    function rePrint(saleId)
    {
        window.open("print://" + saleId,  '_self').close()
    }
</script>
<script type="text/javascript">
function muestra_oculta(id){
if (document.getElementById){ //se obtiene el id
var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
}
}
window.onload = function(){/*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la función nuevamente*/
muestra_oculta('contenido');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */
}
</script>
