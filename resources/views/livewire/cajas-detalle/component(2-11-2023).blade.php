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
		                    <a href="index.html">
		                        <img src="../assets/img/LOGO_03.png" class="navbar-logo" alt="logo">
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
					<div style="margin-top:1%;" class="row sales layout-px-spacing">


						<div class="col-sm-12">
							<div class="widget widget-chart-one">
								<div class="widget-heading">
									<h4 class="card-title">
										<b> Detalle de caja </b>
									</h4>


                  <a  class="btn btn-dark  }}"
                  href="{{ url('report/excel-cajas' . '/' . $caja_elegida  . '/'. uniqid() ) }}" target="_blank">Exportar a Excel</a>



								</div>
								<div class="card component-card_1">
								<div hidden class="card-body">
							<div class="row">

									<div class="col-lg-3 col-md-4 col-sm-12">
						<label>Buscar</label>
										<div class="input-group mb-3">

											<div class="input-group-prepend">
												<span class="input-group-text input-gp">
													<i class="fas fa-search"></i>
												</span>
											</div>
											<input type="text" wire:model="search" placeholder="Buscar" class="form-control">
										</div>

									</div>

								<div class="col-sm-3 col-md-3">
								 <div class="form-group">
									<label>Fecha desde</label>
									<input type="text" wire:model="dateFrom" class="form-control flatpickr" placeholder="Click para elegir">

								</div>
								</div>

								<div class="col-sm-3 col-md-3">
								 <div class="form-group">
									<label>Fecha hasta</label>
									<input type="text" wire:model="dateTo" class="form-control flatpickr" placeholder="Click para elegir">

								</div>
								</div>
								</div>
								</div>



								</div>

								<div class="widget-content">

									<div class="table-responsive">
										<table class="table table-bordered table striped mt-1">
											<thead class="text-white" style="background: #3B3F5C;">
												<tr>
												    	<th class="table-th text-white">ID </th>
													<th class="table-th text-white">ID VENTA / COMPRA / GASTO</th>
														<th class="table-th text-white">DETALLE</th>
													<th class="table-th text-white">FECHA </th>
													<th class="table-th text-white">METODO DE PAGO</th>
													<th class="table-th text-white">USUARIO</th>
													<th class="table-th text-white">MONTO</th>
												</tr>
											</thead>
											<tbody>
												@foreach($data as $metodo)
												<tr>
												        <td>
												            {{$metodo->id_id}}
												        </td>
														<td>
														<h6 class="text-left">
														    @if($metodo->id_factura != 0)
                                                           VENTA {{$metodo->id_factura}}
                                                            @endif
                                                            
                                                            @if($metodo->id_gasto != 0)
                                                              GASTO {{$metodo->id_gasto}}
                                                            @endif
                                                            
                                                            @if($metodo->id_compra != 0)
                                                              COMPRA {{$metodo->id_compra}}
                                                            @endif
														    </H6>
														 </td>
														 
														 												
														<td>
														<h6 class="text-left">
														    
														    <!---------- VENTAS -------->
														    
														   
														    
														    @if($metodo->id_factura != 0)
														    
														    @foreach($ventas as $v)
														    
														    @if($v->id == $metodo->id_factura)
														    
														    {{$v->nombre}}
														    
														    @endif
														    
														    @endforeach
														    
														    @endif
														    
														    <!---------- COMPRAS -------->
														    
														    @if($metodo->id_compra != 0)
														    
														    @foreach($compras as $c)
														    
														    @if($c->id == $metodo->id_compra)
														    
														    {{$c->nombre}}
														    
														    @endif
														    
														    @endforeach
														    
														    @endif
														    
														    <!---------- GASTOS -------->
														    
														    @if($metodo->id_gasto != 0)
														    
														    @foreach($gastos as $g)
														     
														    @if($g->id == $metodo->id_gasto)
														    
														    {{$g->nombre}}
														    
														    @endif
														    
														    @endforeach
														    
														    @endif
														    
														    
														    
														    
														    <!---------- COMPRAS -------->
														    </H6>
														 </td>
													<td>
														<h6 class="text-left">{{\Carbon\Carbon::parse( $metodo->created_at)->format('d-m-Y H:i')}}</h6>
													</td>
													<td>
														<h6 class="text-left">
                              @if($metodo->nombre_banco)
                              {{$metodo->nombre_banco}} -
                              @endif
                              {{$metodo->metodo_pago}}</h6>
													</td>
													<td>
														<h6 class="text-left">{{$metodo->name}}</h6>
													</td>
													<td>
														<h6 class="text-left">$
                              @if($metodo->id_factura != 0)
                              {{number_format($metodo->monto+$metodo->recargo,2)}}
                              @endif
                              
                              @if($metodo->id_gasto != 0)
                              {{number_format(-1*$metodo->monto_gasto,2)}}
                              @endif
                              
                              @if($metodo->id_compra != 0)
                              {{number_format(-1*$metodo->monto_compra,2)}}
                              @endif
                              
                              
                            </h6>
													</td>

												</tr>
												@endforeach
											</tbody>

										</table>
									
									</div>
								</div>


							</div>


						</div>


					</div>

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
