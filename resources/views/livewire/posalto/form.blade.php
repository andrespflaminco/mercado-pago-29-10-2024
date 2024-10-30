<head>
  <style media="screen">
  	.monto:hover {
  		width: 80px;
      vertical-align: middle;
      color: #515365 !important;
      font-size: 13px !important;
      letter-spacing: 1px !important;
  		background-color:
  		transparent;
  		border-left: none;
  		border-top: none;
  		border-right: none;
  		text-align: center;
  		border-bottom: 1px solid #bfc9d4;
  	}
  	.monto:focus {
  		width: 80px;
  		background-color:
  		transparent;
      vertical-align: middle;
      color: #515365 !important;
      font-size: 13px !important;
      letter-spacing: 1px !important;
  		border-left: none;
  		border-top: none;
  		border-right: none;
  		text-align: center;
  		border-bottom: 1px solid #bfc9d4;
  	}
  	.monto {
  		width: 80px;
  		background-color:
  		transparent;
      vertical-align: middle;
      color: #515365 !important;
      font-size: 13px !important;
      letter-spacing: 1px !important;
  		border: none;
  		text-align: center;
  	}
  </style>
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









<div wire:ignore.self class="modal fade" id="theModal1" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>Factura # {{ $NroVenta }}</b>
        </h5>
        @foreach($mail as $m)
        <div style="justify-content: flex-end !important;" class="invoice-header-section">
          @if(session('status'))
          <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
          @endif



            <div style="float:right !important;">


              @if ($hojar != '')

              @foreach ($hoja_ruta as $hr)

              <button type="button" class="btn btn-dark" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" onclick="MostrarHojaRuta()">
                ENTREGA: {{\Carbon\Carbon::parse($hr->fecha)->format('d-m-Y')}}
                @if($hr->turno != '')
                ({{$hr->turno}})
                @else
                @endif
              </button>

              @endforeach

              @else
              <button type="button" class="btn btn-dark" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" onclick="MostrarHojaRuta()">
              AGREGAR A HOJA DE RUTA
              </button>
              @endif




      @include('livewire.pos.form-hoja-ruta')
      @include('livewire.pos.form-hoja-ruta-nueva')
      @include('livewire.factura.form-pagos')




      @if($m->status == 'Pendiente')
        <button onclick="cambiar()" style="   min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails3({{$ventaId}})"
          class="btn btn-warning mb-2">
          {{$m->status}}
      </button>
      @endif
      @if($m->status == 'Entregado')
        <button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails3({{$ventaId}})"
          class="btn btn-success mb-2">
          {{$m->status}}
      </button>
      @endif
      @if($m->status == 'Cancelado')
        <button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails3({{$ventaId}})"
          class="btn btn-danger mb-2">
          {{$m->status}}
      </button>
      @endif
      @if($m->status == 'En proceso')
        <button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails3({{$ventaId}})"
          class="btn btn-secondary mb-2">
          {{$m->status}}
      </button>
      @endif

              <div style="display:none; margin-bottom: 0 !important; " class="btn-group mb-4 mr-2" role="group">

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
                <a style="color: #515365 !important;" type="button" onclick="MostrarPagos()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>   </a>



              <a style="margin-left: 10px !important;" href="{{ url('report-email/pdf' . '/' . $ventaId  . '/' . $m->email) }}" > <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> </a>

              @endforeach

              <a style="margin-left: 10px !important;" href="{{ url('report-factura/pdf' . '/' . $ventaId) }}" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Imprimir"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg></a>

              <button type="button" style="background:transparent; border-radius: 50%; margin-left:20px;" wire:click.prevent="CerrarFactura()" data-dismiss="modal">
                <i class="fa fa-times" aria-hidden="true"></i>
              </button>
            </div>


        </div>

      </div>
      <div class="modal-body">




                                 <div class="invoice-container">
                                     <div class="invoice-inbox">

                                       <div style="{{$estado2}}" id="contenido-hr">
                                         <div class="invoice-header-section">
                                           <div class="row">

                                             <div class="col-sm-12 col-md-12">
                                         <h4> Hoja de ruta </h4>

                                         <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
                                             <table class="multi-table table table-hover" style="width:100%">
                                                 <thead>
                                                     <tr>
                                                         <th class="text-center">Hoja de Ruta</th>
                                                         <th class="text-center">Fecha</th>
                                                         <th class="text-center">Transportista</th>
                                                         <th class="text-center">Turno</th>
                                                         <th class="text-center">Acciones</th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                     @foreach ($listado_hojas_ruta as $lh)
                                                     <tr>
                                                        <td class="text-center">HOJA DE RUTA {{$lh->nro_hoja}}</td>
                                                         <td class="text-center">{{\Carbon\Carbon::parse($lh->fecha)->format('d-m-Y')}}</td>
                                                         <td class="text-center">{{$lh->nombre}}</td>
                                                         <td class="text-center">{{$lh->turno}}</td>
                                                         <td class="text-center"> <a wire:click="AsignarHojaRuta({{$lh->id}}, {{$ventaId}} )" class="btn btn-dark mb-2"> Agregar </a>  </td>

                                                     </tr>
                                                     @endforeach

                                                 </tbody>

                                             </table>
                                         </div>


                                         <a wire:click="SinAsignarHojaRuta({{$ventaId}} )" style="min-width:220px;" class="btn btn-light mb-2"> Sin asignar </a><br>
                                           <br><br><br>
                                            <a href="javascript:void(0)" style="color:blue;" data-toggle="modal" data-target="#theModal">Crear nueva Hoja de ruta</a>
                                       </div>
                                       </div>
                                       </div>
                                       </div>



                                       <div style="{{$estado}}" id="contenido" >
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
                                                                 <td class="text-center">$
                                                                    <input type="text" class="monto" id="p{{$p2->id}}"
                                                  									wire:change="UpdatePago({{$p2->id}}, $('#p' + {{$p2->id}}).val() )" value="{{ number_format($p2->monto,2) }}"> </td>
                                                                 <td class="text-center">


                                                                   <a href="javascript:void(0)" onclick="ConfirmPago('{{$p2->id}}')" >
                                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                                    </a>


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

                                              <div class="form-group">
                                                <label for="exampleInputEmail1">Agregar pago</label>

                                                <div class="input-group mb-4">

                                                <input autocomplete="off" type="text" id="title" wire:model.lazy="monto" class="form-control" required="">
                                                <div class="input-group-append">
                                                          <button type="button" wire:click="CreatePago({{$ventaId}})" class="btn btn-dark">+</button>

                                               </div>
                                               </div>
                                              </div>

                                            @endif
                                           </div>
                                         </div>
                                       </div>
                                       </div>

                                         <div class="">

                                             <div style="min-height: 800px !important;" id="seleccion" class="invoice-00001">
                                                 <div class="content-section  animated animatedFadeInUp fadeInUp">

                                                     <div style="width:100% !important;" class="row inv--head-section">

                                                         <div class="col-sm-6 col-12">
                                                             <h3 class="in-heading">DETALLE DE VENTA</h3>
                                                         </div>
                                                         <div class="col-sm-6 col-12 align-self-center text-sm-right">

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
     <div class="modal-footer">

       <button type="button" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

     </div>
   </div>
 </div>
</div>

<script type="text/javascript">
function ConfirmPago(id) {

  swal({
    title: 'CONFIRMAR',
    text: '¿CONFIRMAS ELIMINAR EL PAGO?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('deletePago', id)
      swal.close()
    }

  })
}
</script>
<script>
function MostrarPagos() {
  var MostrarPagos = document.getElementById("contenido");
  if (MostrarPagos.style.display === "none") {
    MostrarPagos.style.display = "block";
  } else {
    MostrarPagos.style.display = "none";
  }
}
</script>
<script>
function MostrarHojaRuta() {
  var MostrarHojaRuta = document.getElementById("contenido-hr");
  if (MostrarHojaRuta.style.display === "none") {
    MostrarHojaRuta.style.display = "block";
  } else {
    MostrarHojaRuta.style.display = "none";
  }
}
</script>
