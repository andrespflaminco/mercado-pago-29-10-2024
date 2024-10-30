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
    
    
    
    @media (max-width: 680px)
	{
	
	.content-section {
	    padding: 10px 15px !important;
	}
    
    
    
    		    
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












<div wire:ignore.self class="modal fade" id="theModal1"  style="overflow-y: visible !important;" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>Venta # {{ $NroVenta }} </b>
        </h5>
        @foreach($detalle_cliente as $m)
        <div style="justify-content: flex-end !important;" class="invoice-header-section">
          @if(session('status'))
          <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
          @endif



            <div style="float:right !important;">

            @if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet()  )
            
              @if ($hojar != '')

              <button type="button" class="btn btn-dark" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" onclick="MostrarHojaRuta()">
                ENTREGA: {{\Carbon\Carbon::parse($hojar->fecha)->format('d-m-Y')}}
                @if($hojar->turno != '')
                ({{$hojar->turno}})
                @else
                @endif
              </button>

              @else
              
              @if(2 < Auth::user()->plan)
              <button hidden type="button" class="btn btn-dark" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" onclick="MostrarHojaRuta()">
              AGREGAR A HOJA DE RUTA
              </button>
              @endif
              
              @endif

            






    @endif

        
         @foreach($total_total as $tf)
             @if($tf->nro_factura != null)
             @if($nota_credito == null)
             <button type="button" class="btn btn-warning" style="    min-width: 130px; margin-bottom: 0 !important;  margin-right: 15px; margin-bottom: 0 !important;  padding: 3px !important;" onclick="ConfirmAnularFactura('{{$NroVenta}}')">
              ANULAR FACTURA
              </button>
             
            @else
            Nota de credito: {{$nota_credito->nro_nota_credito}}
            @endif
            @endif
            @endforeach

        @if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet()  )
    
              @if(($suma_monto+$suma_cash+$rec) < ($tot))

              <strong color="red">Deuda: $ {{($tot) - ($suma_monto) - $rec }}</strong>

              @endif
    
              @endif
              
              
              <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; color: #515365 !important; margin-left: 10px !important;" type="button" wire:click="EditarPedido('{{$style}}')">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              EDITAR
              </a>

             <a hidden style="box-shadow: none; border: solid 1px #515365; background:transparent; color: #515365 !important; margin-left: 10px !important;" type="button" wire:click="ObtenerInfoFactura('{{$NroVenta}}')"> .  </a>

                <a class="btn"  style="box-shadow: none; border: solid 1px #3b3f5c; background:transparent; color: #515365 !important; margin-left: 10px !important;" type="button" onclick="MostrarPagos()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                PAGOS
                </a>


            @if(2 < Auth::user()->plan)

              @if($m->email != null)
              <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" href="{{ url('report-email/pdf' . '/' . $ventaId  . '/' . $m->email) }}" >
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
              ENVIAR
              </a>
                @else
             <a class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;" href="javascript:void(0)" wire:click="MailModal({{$ventaId}})" >
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            ENVIAR
            </a>

                @endif
                
            @endif

            @endforeach


            @if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet()  )            
            
              <button href="javascript:void(0)" class="btn" style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;"  id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Imprimir"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                IMPRIMIR 
            </button>
    
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                  <a class="dropdown-item" href="javascript:void(0)" wire:click="AbrirImprimir({{$ventaId}})">IMPRIMIR A4</a>
                  <a class="dropdown-item" target="_blank" href="{{ url('ticket' . '/' . $ventaId ) }}">IMPRIMIR TICKET</a>
            </div>
            
            @endif
            
            <button type="button" style="background:transparent; border-radius: 50%; margin-left:20px;" data-dismiss="modal">
                <i class="fa fa-times" aria-hidden="true"></i>
              </button>
              
              
            </div>


        </div>

      </div>
      <div class="modal-body">

        <div style="display: {{$style}};" class="col-lg-8 col-md-4 col-sm-12">
        <div style="margin-bottom: 0 !important;" class="input-group mb-4">
          <div class="input-group-prepend">
            <span class="input-group-text input-gp">
              <i class="fas fa-clipboard-list"></i>
            </span>
          </div>


            <input
                style="font-size:14px !important;"
                type="text"
                class="form-control"
                placeholder="Agregar un producto"
                wire:model="query_product"
                wire:keydown.escape="resetProduct"
                wire:keydown.tab="resetProduct"
                wire:keydown.enter="selectProduct"
            />


                <select hidden style="max-width:200px;" wire:model.lazy="iva_agregar" class="form-control">
                  @foreach($total_total as $f)
                  @if($f->tipo_comprobante == "A" || $f->tipo_comprobante == "B" )

                  <option value="0.105">10,5%</option>
                  <option value="0.21">21%</option>
                  <option value="0.27">27%</option>
                  @else
                  <option value="0">Sin IVA</option>

                  @endif
                  @endforeach
                </select>
                 </div>
            


            @if(!empty($query_product))
                <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>

                <div style="position:absolute;" class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                    @if(!empty($products_s))
                        @foreach($products_s as $i => $product)
                        <a style="z-index: 9999;" href="javascript:void(0)"
                        wire:click="selectProduct({{$product['id']}})"
                        class="btn" title="Edit">{{ $product['barcode'] }} - {{ $product['name'] }}
                        </a>

                        @endforeach

                    @else

                    @endif
                </div>
            @endif



        </div>


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
                                                         <td class="text-center">

                                                           @if ( $hojar != null)

                                                           @if ( $hojar->id == $lh->id)

                                                           <P class="btn btn-success btn-sm "> HOJA DE RUTA ELEGIDA</P>
                                                           @else
                                                           <a wire:click="AsignarHojaRuta({{$lh->id}}, {{$ventaId}} )" class="btn btn-dark  btn-sm"> Elegir </a>
                                                           @endif


                                                           @else

                                                          <a wire:click="AsignarHojaRuta({{$lh->id}}, {{$ventaId}} )" class="btn btn-dark  btn-sm"> Agregar </a>


                                                           @endif


                                                           </td>

                                                     </tr>
                                                     @endforeach

                                                 </tbody>

                                             </table>
                                         </div>


                                         <a wire:click="SinAsignarHojaRuta({{$ventaId}} )" style="min-width:220px;" class="btn btn-light mb-2"> Sin asignar </a><br>
                                           <br><br><br>
                                            <a href="javascript:void(0)" style="color:blue;" data-toggle="modal" wire:click="AbrirHRNueva({{$ventaId}})">Crear nueva Hoja de ruta</a>
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
                                                                 <th class="text-center">Caja</th>
                                                                 <th class="text-center">Fecha</th>
                                                                 <th class="text-center">Metodo de pago</th>
                                                                 <th class="text-center">Pago</th>
                                                                 <th class="text-center">Recargo</th>
                                                                 <th class="text-center">Total</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                             @foreach($pagos2 as $p2)
                                                             @if ($p2->monto > 0)
                                                             <tr>
                                                               <td class="text-center">Caja # {{$p2->nro_caja}}</td>
                                                                 <td class="text-center">{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
                                                                 <td class="text-center">{{$p2->metodo_pago}}</td>
                                                                 <td class="text-center">$
                                                                    {{number_format($p2->monto,2) }} </td>

                                                                   <td class="text-center">$
                                                                      {{number_format($p2->recargo,2) }} </td>
                                                                   <td class="text-center">$
                                                                        {{number_format($p2->monto + $p2->recargo,2) }} </td>
                                                                     <td class="text-center">

                                                                   <a href="javascript:void(0)" wire:click="EditPago({{$p2->id}})" >
                                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                                   </a>


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
                                                                 <th class="text-center"> </th>
                                                                 <th class="text-center"> </th>
                                                                 <th class="text-center">$ {{number_format($suma_monto,2)}}</th>
                                                                 <th class="text-center">$ {{number_format($rec,2)}}</th>
                                                                 <th class="text-center">$ {{number_format($suma_monto+$rec,2)}}</th>
                                                             </tr>
                                                         </tfoot>
                                                     </table>
                                                 </div>



                                              </div>

                                             </div>


                                             <strong>Deuda: $ {{($tot) - ($suma_monto) - $rec }}</strong>
                                             <br><br>

                                              <div class="form-group">
                                              <a href="javascript:void(0);" wire:click.prevent="AgregarPago({{$ventaId}})">Agregar pago </a>
                                              </div>

                                           </div>
                                         </div>
                                       </div>
                                       </div>

                                         <div class="">

                                             <div style="min-height: 800px !important;" id="seleccion" class="invoice-00001">
                                                 <div class="content-section  animated animatedFadeInUp fadeInUp">
                                                    
                                                    @if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet()  )
                                                     
                                                     <div style="width:100% !important;" class="row inv--head-section">
                                                       <div class="col-sm-5 col-12 align-self-center text-sm-left">
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
                                                    
                                                    
                                                         <div class="col-sm-2 col-12 text-center">
                                                            @foreach($total_total as $f)

                                                           <div style="border:solid 1px; margin:0 auto; width:  78px;">

                                                             <h1 >

                                                               <b>
                                                                 @if($f->tipo_comprobante != NULL)
                                                                 {{$f->tipo_comprobante}}
                                                                 @else
                                                                 X
                                                                 @endif
                                                               </b>
                                                             </h1>

                                                             @if($f->tipo_comprobante == 'C')
                                                             <p>
                                                               <b> COD. 011 </b>
                                                             </p>
                                                             @endif
                                                             @if($f->tipo_comprobante == 'A')
                                                             <p>
                                                               <b> COD. 01 </b>
                                                             </p>
                                                             @endif
                                                             @if($f->tipo_comprobante == 'B')
                                                             <p>
                                                               <b> COD. 06 </b>
                                                             </p>
                                                             @endif
                                                             @if($f->tipo_comprobante == NULL)
                                                             <p>
                                                               <b> </b>
                                                             </p>
                                                             @endif


                                                           </div>


                                                            @endforeach

                                                         </div>
                                                         <div style="text-align:right;" class="col-sm-5 col-12">
                                                             <h3 class="in-heading">DETALLE DE VENTA</h3>
                                                         </div>
                                                         
                                                 

                                                         <br>


                                                     </div>

                                                     <div class="row inv--detail-section">

                                                         <div class="col-sm-6 align-self-center">
                                                           @foreach($detalle_facturacion as $df)
                                                           <p class="inv-customer-name"> <h5> <b>{{$df->razon_social}}</b>
                                                           </h5> </p>
                                                           <p class="inv-customer-name">Direccion: {{$df->domicilio_fiscal}} , {{$df->localidad}} ,{{$df->provincia}} </p>
                                                           <p class="inv-customer-name">Telefono: {{$df->phone}}</p>
                                                           <p class="inv-customer-name">Email: {{$df->email}}</p>
                                                           @endforeach
                                                         </div>
                                                         <div  class="col-sm-1 align-self-center  text-sm-right order-sm-0 order-1">

                                                          </div>
                                                         <div  class="col-sm-5 align-self-center  text-sm-right order-sm-0 order-1" style="display:flex;">
                                                           <br>
                                                           <div style="display:block; padding-left:17%; padding-right:0;" class="col-sm-8 align-self-left  text-sm-left order-sm-0 order-1">


                                                             <p class="inv-customer-name"> <b>NRO FACTURA:</b></p>
                                                               <p class="inv-customer-name"> <b>CUIT:</b></p>
                                                               <p class="inv-street-addr"> <b>INGRESOS BRUTOS:</b></p>
                                                               <p class="inv-street-addr"> <b>INICIO ACTIVIDADES:</b></p>
                                                               <p class="inv-email-address"><b>IVA:</b></p>
                                                            </div>

                                                           <div style="display:block; padding:0;"  class="col-sm-4 align-self-center  text-sm-right order-sm-0 order-1">
                                                             @foreach($total_total as $tf)
                                                             @if($tf->nro_factura)
                                                             <?php
                                                             $porciones = explode("-", $tf->nro_factura);
                                                             $tipo_factura = $porciones[0]; // porción1
                                                             $pto_venta = $porciones[1]; // porción2
                                                             $nro_factura_ = $porciones[2]; // porción2
                                                             echo str_pad($pto_venta, 5, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 8, "0", STR_PAD_LEFT); ?>
                                                             @else
                                                             -
                                                             @endif
                                                             @endforeach

                                                                 @foreach($detalle_facturacion as $df)
                                                               <p class="inv-customer-name">{{$df->cuit}}</p>
                                                               <p class="inv-street-addr">{{$df->iibb}}</p>
                                                               <p class="inv-street-addr">{{\Carbon\Carbon::parse($df->fecha_inicio_actividades)->format('d-m-Y')}}</p>
                                                               <p class="inv-email-address">{{$df->condicion_iva}}</p>
                                                             @endforeach

                                                            </div>

                                                         </div>

                                                     </div>
                                                    
                                                    @endif
                                                    
                                                   
                                                     <div style="border-top: 1px solid #dee2e6;" class="row inv--detail-section">
                                                        
                                                         <div class="col-sm-6 align-self-center">
                                                        
                                                        @if((new \Jenssegers\Agent\Agent())->isMobile())
                                                         <div style="padding: 15px 0px; border-bottom: solid 1px #e0e6ed !important; margin-bottom:30px;">
                                                         <b style="font-size:16px; color: #3b3f5c !important; ">Datos del cliente:</b>    
                                                         </div>
                                                         @endif
                                                             <!---- Si es un cliente regular ---->
                                                             
                                                             @if(0 < count($detalle_cliente) )
                                                             @foreach($detalle_cliente as $c)
                                                             <p class="inv-customer-name">Cliente: {{$c->nombre}}  
                                                              
                                                             @if($ecommerce_envio_form != null)
                                                            |  Destinatario: {{$ecommerce_envio_form->nombre_destinatario}} 
                                                             @endif
                                                              <a style="color: #515365 !important; margin-left: 10px !important;" type="button" wire:click="AbrirModalEditarCliente('{{$style2}}')">
                                                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                              </a>
                                                            </p>
                                                             <p class="inv-customer-name">CUIT: {{$c->dni}}</p>
                                                             <p class="inv-street-addr">{{$c->direccion}},{{$c->localidad}}. {{$c->provincia}}</p>
                                                             <p class="inv-email-address"> Tel: {{$c->telefono}}</p>
                                                            @endforeach
                                                             @endif
                                                           
                                                          
                                                         </div>

                                                         <div class="col-sm-6 align-self-center  text-sm-right order-2">
                                                             <p class="inv-list-number"><span class="inv-title">Numero de detalle de venta : </span> <span class="inv-number"># {{$ventaId}}</span></p>



                                                             <p class="inv-created-date"><span class="inv-title">Fecha : </span>
                                                               @foreach($total_total as $f)
                                                                <span class="inv-date">{{\Carbon\Carbon::parse($f->created_at)->format('d-m-Y')}}</span></p>
                                                                @endforeach


                                                         </div>
                                                     </div>
                                                    
                                                     
                                                         
                                                    <div class="row inv--product-table-section">
                                            
                                                         <div class="col-12">
                                                                     
                                                    @if((new \Jenssegers\Agent\Agent())->isMobile())
                                                    
                                                         <div style="margin-top: 30px; padding: 15px 0px; border-top: solid 1px #e0e6ed !important;">
                                                         <b style="font-size:16px; color: #3b3f5c !important;">Detalle del pedido:</b>    
                                                         </div>
                                                    @endif
                                                    
                                                    
                                                             <div class="table-responsive">
                                                                 <table class="table">
                                                                     <thead class="">
                                                                         <tr>
                                                                             <th scope="col">Fila</th>
                                                                             <th scope="col">Codigo</th>
                                                                             <th scope="col">Producto</th>

                                                                             <th scope="col">Observaciones</th>
                                                                             <th class="text-right" scope="col">Cantidad</th>
                                                                             <th class="text-right" scope="col">Precio</th>
                                                                            
                                                                           
                                                                                 <th class="text-right" scope="col"> Descuento</th>
                       
                                                                               
                                                                               @foreach($total_total as $f)

                                                                               @if($f->tipo_comprobante == "A")
                                                                                <th style="display:{{$style2}};" class="text-right" scope="col">% IVA</th>

                                                                               @endif


                                                                               @endforeach
                                                                               
                                                                               
                                                                             <th style="display:{{$style}};" class="text-center" scope="col">IVA</th>
                                                                             
                                                                             <th class="text-right" scope="col">Subtotal</th>
                                                                             <th class="text-right" scope="col"></th>
                                                                         </tr>
                                                                     </thead>
                                                                     <tbody>
                                                                       <?php $i = 1; ?>
                                                                       @foreach($detalle_venta as $item)
                                                                         <tr>
                                                                             <td><?php echo $i++; ?></td>
                                                                             <td>



                                                                               {{$item->product_barcode}}
                                                                             </td>
                                                                             
                                                                             <td>



                                                                               {{$item->product_name}}
                                                                             </td>
                                                                             <td>{{$item->comentario}}</td>
                                                                            
                                                                             <td class="text-right">

                                                                               @if ($item->stock_descubierto === "si")
                                                                               <p style="display: {{$style}};" >
                                                                                
                                                                              <input  style="padding-left:15px;"  type="number" class="boton-editar"  value="{{number_format($item->quantity,0)}}" id="qty{{$item->id}}"
                                                                 							wire:change="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}  ).val() )" min="1" onchange="Update({{$item->id}});" >

                                                                             <p style="color:red;" id="stock_maximo{{$item->id}}" hidden >Stock maximo</p>

                                                                             </p>

                                                                               @else
                                                                               <p style="display: {{$style}};" >

                                                                              <input  style="padding-left:15px;"  type="number" class="boton-editar"  value="{{number_format($item->quantity,0)}}" id="qty{{$item->id}}"
                                                                               wire:change="updateQtyPedido({{$item->id}}, $('#qty' + {{$item->id}}).val() )" min="1" onchange="Update({{$item->id}});" >

                                                                                </p>

                                                                               @endif

                                                                               <input hidden  id="stock_descubierto{{$item->id}}"	value="{{$item->stock_descubierto}}">


                                                                               <input hidden id="stock{{$item->id}}" value="{{$item->stock}}">

                                                                                 <input hidden  id="stock_max{{$item->id}}" value="{{$item->stock+$item->quantity}}">
                                                                                 <p style="display: {{$style2}};"> {{number_format($item->quantity,2)}} </p>


                                                                             </td>
                                                                             <td class="text-right">


                                                                               <p style="display:{{$style}};">


                                                                                  $

                                                                                  <input style="padding-left:15px;" type="text" class="boton-editar"  value="{{$item->price }}" id="price{{$item->id}}"
                                                                 	                wire:change="updatePricePedido({{$item->id}}, $('#price' + {{$item->id}}).val() )" min="1" >


                                                                                  </p>


                                                                            <p style="display: {{$style2}};">

                                                                              @foreach($total_total as $f)

                                                                              @if($f->tipo_comprobante == "A")
                                                                              $

                                                                              {{number_format($item->price,2)}}

                                                                           
                                                                              @else

                                                                            $  {{number_format($item->price*(1+$item->iva),0)}}


                                                                              @endif


                                                                              @endforeach
                                                                            </p>
                                                                            </td>
                                                                           <td class="text-center">
                                                                            <p style="display:{{$style}};">
                                                                           
                                                                            
                                                                            @if($item->price != 0) 
                                                                         
                                                                            <input style="padding-left:15px;" type="text" class="boton-editar"  value="{{ number_format(-1*($item->descuento)/$item->price*100/$item->quantity,2) }}" id="descuento-recargo{{$item->id}}"
                                                                 	         wire:change="UpdateDescuentoRecargo({{$item->id}}, $('#descuento-recargo' + {{$item->id}}).val() )" >
                                                                            
                                                                            @else 
                                                                         
                                                                            <input style="padding-left:15px;" type="text" class="boton-editar"  value="{{ number_format(0,2) }}" id="descuento-recargo{{$item->id}}"
                                                                 	         wire:change="UpdateDescuentoRecargo({{$item->id}}, $('#descuento-recargo' + {{$item->id}}).val() )" >
                                                                            
                                                                            @endif
                                                                         
                                                                            %

                                                                            </p>
                                                                                  
                                                                            <p style="display:{{$style2}};">
                                                                               @if($item->price != 0)  
                                                                             {{ number_format(-1*($item->descuento)/$item->price*100/$item->quantity,2) }} %
                                                                                @else
                                                                                {{ number_format(0,2) }} %
                                                                                @endif
                                                                            </p>
                                                                            
                                                                            </td>
                                                                            
                                                                             @foreach($total_total as $f)

                                                                               @if($f->tipo_comprobante == "A")
                                                                                <td style="display:{{$style2}};" class="text-center">
                                                                                {{$item->iva*100}} %
                                                                                </td>

                                                                               @endif


                                                                               @endforeach



                                                                            <td class="text-center" style="display:{{$style}};">
                                                                              <div class="btn-group mb-4 mr-2">
                                                                									<button style="font-size: 15px;
                                                                									padding: 8px 10px;
                                                                									letter-spacing: 1px;
                                                                									width: 90px !important;
                                                                									padding: 0.5rem 0.5rem;  border: 1px solid #bfc9d4 !important;
                                                                    							color: #3b3f5c !important; background-color: #fff; " class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                											{{$item->iva*100}} % <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                                									</button>
                                                                									<div class="dropdown-menu">


                                                                											<button  id="iva{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#iva' + {{$item->id}}).val() )"  value="0" class="dropdown-item">Sin IVA</button>
                                                                											<button id="ivaprimero{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#ivaprimero' + {{$item->id}}).val() )"  value="0.105" class="dropdown-item">10,5%</button>
                                                                											<button id="ivasegundo{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#ivasegundo' + {{$item->id}}).val() )"  value="0.21" class="dropdown-item">21%</button>
                                                                											<button id="ivatercero{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#ivatercero' + {{$item->id}}).val() )"  value="0.27" class="dropdown-item">27%</button>
                                                                									</div>
                                                                							</div>
                                                                            </td>
                                                                             <td class="text-right">

                                                                               @foreach($total_total as $f)

                                                                               @if($f->tipo_comprobante == "A")
                                                                              $ {{number_format( (( ($item->price*$item->quantity) - $item->descuento ) *(1+$item->iva)) ,2)}}

                                                                               @else
                                                                               $ {{number_format( (( ($item->price*$item->quantity) - $item->descuento) *(1+$item->iva)) ,2)}}



                                                                               @endif


                                                                               @endforeach




                                                                             </td>
                                                                              <td class="text-right">

                                                                                   <a style="display:{{$style}};" href="javascript:void(0)" onclick="Confirm('{{$item->id}}')" >
                                                                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                                                    </a>

                                                                              </td>
                                                                         </tr>
                                                                         @endforeach
                                                                     </tbody>
                                                                 </table>
                                                             </div>
                                                         </div>
                                                     </div>
                                                    
                                                                                                        
                                                     @if((new \Jenssegers\Agent\Agent())->isMobile())
                                                         <div style="margin-top: 40px; padding: 15px 0px; border-top: solid 1px #e0e6ed !important; border-bottom: solid 1px #e0e6ed !important;">
                                                         <b style="font-size:16px; color: #3b3f5c !important;">Datos generales:</b>    
                                                         </div>
                                                    @endif
                                                    
                                                     <div class="row mt-4">
                                                         <div class="col-sm-5 col-12 order-sm-0 order-1">
                                                             <div class="inv--payment-info">
                                                                 <div class="row">
                                                               
                                                                     
                                                                     <div class="col-sm-12 col-12 mt-3">
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
                                                                     
                                                                     
                                                                           
                                                                      <!---- Si es un cliente de con envio a domicilio ---->
                                                                      <br><br><br><br>
                                                                     @if($ecommerce_envio_form != null)
                                                                     <div style="border: solid 1px #e0e6ed; padding:5px; width:100%;">
                                                                       <div class="col-sm-12 col-12">
                                                                     <h6 class=" inv-title">
                                                                        Detalles de Entrega:
                                                                        </h6>
                                                                     </div>
                                                                      <div class="col-sm-12 col-12">
                                                                         <p class=" inv-subtitle">Tipo de entrega : @if($ecommerce_envio_form->metodo_entrega == 1)  Retira por el local @else Entrega a Domicilio    @endif </p>
                                                                     </div>
                                                                   
                                                                     <div class="col-sm-12 col-12">
                                                                         <p class=" inv-subtitle">Destinatario: {{$ecommerce_envio_form->nombre_destinatario}}  </p>
                                                                     </div>
                                                                     
                                                                     @if($ecommerce_envio_form->metodo_entrega == 2)
                                                                     <div class="col-sm-12 col-12">
                                                                         <p class=" inv-subtitle">Direccion: {{$ecommerce_envio_form->direccion}},{{$ecommerce_envio_form->ciudad}}. {{$ecommerce_envio_form->nombre_provincia}} </p>
                                                                     </div>
                                                                     
                                                                     @endif
                                                                       <div class="col-sm-12 col-12">
                                                                         <p class=" inv-subtitle">Telefono: {{$ecommerce_envio_form->telefono}} </p>
                                                                     </div>
                                                                     
                                                                     </div>
                                                                    
                                                                     <br><br><br><br>
                                                                      @endif
                                                                     <!------------------------------------------------>
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
                                                                         <p class="">

                                                                           @foreach($total_total as $f)

                                                                           @if($f->tipo_comprobante != "B")
                                                                           $ {{$t->subtotal - $t->descuento }}

                                                                           @else
                                                                           $ {{ ($t->total - $t->descuento) }}



                                                                           @endif


                                                                           @endforeach


                                                                           </p>

                                                                     </div>
                                                                    @if($t->tipo_comprobante != "B")
                                                                     <div class="col-sm-8 col-7">
                                                                         <p class="">Recargo: </p>
                                                                     </div>
                                                                     <div class="col-sm-4 col-5">
                                                                         <p class=""> $ 
                                                                         @if($rec > 0)
                                                                         {{$rec}}
                                                                         @else 
                                                                         0
                                                                         @endif
                                                                         
                                                                        
                                                                         </p>

                                                                     </div>
                                                               
                                                                     @endif
                                                                     
                                                                     @if($t->tipo_comprobante == "A")
                                                                     <div class="col-sm-8 col-7">
                                                                         <p class="">IVA: </p>
                                                                     </div>
                                                                     <div class="col-sm-4 col-5">
                                                                         <p class=""> $ {{$t->iva}}</p>

                                                                     </div>


                                                                     <div class="col-sm-8 col-7 grand-total-title">
                                                                         <h4 class="">Total : </h4>
                                                                     </div>
                                                                     <div class="col-sm-4 col-5 grand-total-amount">
                                                                         <h4 class=""> $ {{$t->total }}</h4>
                                                                         
                                                                     </div>


                                                                     @endif

                                                                     @if($t->tipo_comprobante == "B" || $t->tipo_comprobante == "C" || $t->tipo_comprobante == "CF" || $t->tipo_comprobante == "" )

                                                                     <div class="col-sm-8 col-7 grand-total-title">
                                                                         <h4 class="">Total : </h4>
                                                                     </div>
                                                                     <div class="col-sm-4 col-5 grand-total-amount">
                                                                         <h4 class=""> $ {{$t->subtotal + $t->iva + $rec - $t->descuento }}</h4>
                                                                     </div>


                                                                     @endif

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
@foreach($total_total as $cae)

@if($cae->cae != null)


     <div style="border: solid 1px #c8c8c8;" class="modal-footer">
       <div style="width:100%;"  class="d-flex">
          <div class="p-2">
           <div class="title m-b-md">
                 {!! QrCode::size(70)->generate($codigo_qr); !!}
                    </div>
            </div>
              <div style="margin-left:15px;" class="mr-auto p-2">
              <img src="assets/img/afip.png" style="width:150px;"><br>
              <b style="font-size:12px;">Comprobante autorizado</b>
              </div>

          <div class="p-2">
              <br>
              <b>CAE: </b>{{$cae->cae}}</div>
          <div class="p-2">
              <br>
              <b>VENCIMIENTO CAE: </b>{{\Carbon\Carbon::parse($cae->vto_cae)->format('d/m/Y')}}</div>

        </div>

       </div>

@endif

@endforeach

     </div>
   </div>
 </div>
</div>
</div>

<script type="text/javascript">


function ConfirmAnularFactura(id) {

  swal({
    title: 'CONFIRMAR',
    text: '¿CONFIRMAS REALIZAR NOTA DE CREDITO DE LA FACTURA?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('AnularFactura', id)
      swal.close()
    }

  })
}

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
function MostrarProduccion() {
  var MostrarProduccion1 = document.querySelector('.contenido_produccion1');
  if (MostrarProduccion1.style.display === "none") {
    MostrarProduccion1.style.display = "block";
  } else {
    MostrarProduccion1.style.display = "none";
  }


  var MostrarProduccion2 = document.getElementById("contenido_produccion2");
  if (MostrarProduccion2.style.display === "none") {
    MostrarProduccion2.style.display = "block";
  } else {
    MostrarProduccion2.style.display = "none";
  }



}
</script>
<script>
function EditarPedido() {
  var MostrarEditarPedido1 = document.getElementById("contenido_editar1");
  if (MostrarEditarPedido1.style.display === "none") {
    MostrarEditarPedido1.style.display = "block";
  } else {
    MostrarEditarPedido1.style.display = "none";
  }

  var MostrarEditarPedido2 = document.getElementById("contenido_editar2");
  if (MostrarEditarPedido2.style.display === "none") {
    MostrarEditarPedido2.style.display = "block";
  } else {
    MostrarEditarPedido2.style.display = "none";
  }

  var MostrarEditarPedido3 = document.getElementById("contenido_editar3");
  if (MostrarEditarPedido3.style.display === "none") {
    MostrarEditarPedido3.style.display = "block";
  } else {
    MostrarEditarPedido3.style.display = "none";
  }

  var MostrarEditarPedido4 = document.getElementById("contenido_editar4");
  if (MostrarEditarPedido4.style.display === "none") {
    MostrarEditarPedido4.style.display = "block";
  } else {
    MostrarEditarPedido4.style.display = "none";
  }
  var MostrarEditarPedido5 = document.getElementById("contenido_editar5");
  if (MostrarEditarPedido5.style.display === "none") {
    MostrarEditarPedido5.style.display = "block";
  } else {
    MostrarEditarPedido5.style.display = "none";
  }
  var MostrarEditarPedido6 = document.getElementById("contenido_editar6");
  if (MostrarEditarPedido6.style.display === "none") {
    MostrarEditarPedido6.style.display = "block";
  } else {
    MostrarEditarPedido6.style.display = "none";
  }
  var MostrarEditarPedido7 = document.getElementById("contenido_editar7");
  if (MostrarEditarPedido7.style.display === "none") {
    MostrarEditarPedido7.style.display = "block";
  } else {
    MostrarEditarPedido7.style.display = "none";
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

    <style media="screen">
	.boton-editar {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}

</style>
