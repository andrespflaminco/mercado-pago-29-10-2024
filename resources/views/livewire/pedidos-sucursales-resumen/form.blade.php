<head>


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
        	<b>COMPRA # {{$ventaId}} </b>
        </h5>


        <div style="justify-content: flex-end !important;" class="invoice-header-section">

          <div style="float:right;">
              <a href="{{ url('imprimir-compra/pdf' . '/' . $ventaId ) }}" class="btn btn-light" style="color: #515365 !important; margin-left: 10px !important;" type="button" wire:click="EditarPedido('{{$style}}')">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer action-print" data-toggle="tooltip" data-placement="top" data-original-title="Imprimir"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
              IMPRIMIR 
              </a>
              <a class="btn btn-light" style="color: #515365 !important; margin-left: 10px !important;" type="button" wire:click="EditarPedido('{{$style}}')">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              EDITAR 
              </a>
            <button type="button" name="button" class="btn btn-dark" type="button" onclick="MostrarPagos()">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                <line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                PAGOS
            </button>

            <button type="button" style="background:transparent; border-radius: 50%; margin-left:20px;" data-dismiss="modal">
              <i class="fa fa-times" aria-hidden="true"></i>
            </button>
          </div>


        </div>

      </div>
      <div style="padding:2rem;" class="modal-body">
          
          
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
                 </div>
            </div>


            @if(!empty($query_product))
                <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>

                <div style="position:absolute;" class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                    @if(!empty($products_s))
                        @foreach($products_s as $i => $product)
                        <a style="z-index: 9999;" href="javascript:void(0)"
                        wire:click="selectProduct({{$product['id']}})"
                        class="btn btn-light" title="Edit">{{ $product['barcode'] }} - {{ $product['name'] }}
                        </a>

                        @endforeach

                    @else

                    @endif
                </div>
            @endif

        <div style="{{$estado}}" id="contenido" >
        <div class="invoice-header-section">
          <div class="row">

            <div class="col-sm-12 col-md-8">
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
                                  <th class="text-center">Total</th>
                                  <th class="text-center"></th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($pagos2 as $p2)
                              @if ($p2->count() > 0)
                              <tr>
                                <td class="text-center">
                                    @if($p2->nro_caja != null)
                                    Caja # {{$p2->nro_caja}}
                                    @else
                                    No asociado a caja
                                    @endif
                                    </td>
                                  <td class="text-center">{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
                                  <td class="text-center">{{$p2->metodo_pago}}</td>
                                  <td class="text-center">$
                                     {{number_format($p2->monto_compra,2) }} </td>

                                      <td class="text-center">

                                    <a href="javascript:void(0)" wire:click="EditPago({{$p2->id}})" >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>


                                    <a href="javascript:void(0)" onclick="ConfirmPago({{$p2->id}})" >
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                     </a>




                                  </td>


                              </tr>
                              @else
                              No hay pagos relacionados con esta compra
                              
                              @endif
                              
                              @endforeach





                          </tbody>
                          <tfoot>
                              <tr>
                                  <th class="text-center">Total </th>
                                  <th class="text-center"> </th>
                                  <th class="text-center"> </th>
                                  <th class="text-center">$ {{number_format($suma_monto,2)}}</th>
                                  <th class="text-center"> </th>
                              </tr>
                          </tfoot>
                      </table>
                  </div>



               </div>

              </div>


              <strong>Deuda: $
                @foreach ($total as $t)
                {{($t->total) - ($suma_monto) }}

                 @endforeach</strong>
              <br><br>

               <div class="form-group">
               <a href="javascript:void(0);" wire:click.prevent="AgregarPago({{$ventaId}})">Agregar pago </a>
               </div>

            </div>
          </div>
        </div>
        </div>




        <div style="min-height: 800px !important;" id="seleccion" class="invoice-00001">
            <div class="content-section  animated animatedFadeInUp fadeInUp">

                <div style="width:100% !important;" class="row inv--head-section">
                  <div class="col-sm-5 col-12 align-self-center text-sm-left">
                      <div class="company-info">

                        @foreach($detalle_proveedor as $dp)
                        <h5 class="inv-brand-name">{{$dp->nombre}}</h5>
                        @endforeach



                      </div>

                  </div>

                    <div class="col-sm-2 col-12 text-center">


                      <div style="border:solid 1px; margin:0 auto; width:  78px;">

                        <h1 >

                          <b>

                            X

                          </b>
                        </h1>


                      </div>



                    </div>
                    <div style="text-align:right;" class="col-sm-5 col-12">
                        <h3 class="in-heading">DETALLE DE COMPRA</h3>
                    </div>

                    <br>


                </div>

                  @foreach($detalle_proveedor as $dp)
                <div class="row inv--detail-section">

                    <div class="col-sm-6 align-self-center">

                      <p class="inv-customer-name"> Mail: {{$dp->mail}} </p>
                      <p class="inv-customer-name"> Telefono: {{$dp->telefono}} </p>
                      <p class="inv-customer-name">Direccion: {{$dp->direccion}}

                        @if($dp->localidad != null)
                         , {{$dp->localidad}}
                         @endif
                         @if($dp->localidad != null)
                          - {{$dp->provincia}}
                          @endif  </p>


                    </div>
                    <div  class="col-sm-1 align-self-center  text-sm-right order-sm-0 order-1">

                     </div>
                    <div  class="col-sm-5 align-self-center  text-sm-right order-sm-0 order-1" style="display:flex;">
                      <br>
                      <div style="display:block; padding-left:17%; padding-right:0;" class="col-sm-8 align-self-left  text-sm-left order-sm-0 order-1">


                          <p class="inv-customer-name"> <b>COMPRA # {{$ventaId}}</b></p>
                          <p class="inv-street-addr"> <b>FECHA: {{$dp->created_at}}</b></p>

                       </div>

                      <div style="display:block; padding:0;"  class="col-sm-4 align-self-center  text-sm-right order-sm-0 order-1">


                          <p class="inv-customer-name"> </p>
                          <p class="inv-street-addr"></p>
                          <p class="inv-street-addr"></p>
                          <p class="inv-email-address"></p>


                       </div>

                    </div>

                </div>
                @endforeach


                <div class="row inv--product-table-section">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="">
                                    <tr>
                                      <th scope="col">Fila</th>
                                      <th scope="col">Codigo</th>
                                      <th scope="col">Producto</th>
                                      <th class="text-right" scope="col">Cantidad</th>
                                      <th class="text-right" scope="col">Precio</th>
                                      
                                      
                                      @foreach($total as $f)

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
                                  @foreach($dci as $dci)
                                    <tr>
                                        <td><?php echo $i++; ?> </td>
                                        <td> {{$dci->barcode}}  </td>
                                        <td> {{$dci->nombre}}  </td>

                                       
                                        <td class="text-right">
                                            <div style="display: {{$style2}};">
                                            {{number_format($dci->cantidad,2)}}    
                                            </div>
                                            
                                        
                                        <p style="display: {{$style}};" >
                                          <input  style="padding-left:15px; max-width: 150px;
    float: right;"  type="number" class="form-control text-right"  value="{{number_format($dci->cantidad,0)}}" id="qty{{$dci->id}}"
                                       wire:change="updateQty('{{$dci->id}}|{{$dci->referencia_variacion}}', $('#qty' + {{$dci->id}}).val() )" min="1" onchange="Update({{$dci->id}});" >

                                        </p>


                                        </td>
                                        
                                        <td class="text-right">
                                        <div style="display: {{$style2}};">
                                        $ {{number_format($dci->precio,2)}}
                                        </div>  
                                        <div style="display:{{$style}};">
                                        $ <input style="padding-left:15px;" type="text" class="boton-editar"  value="{{$dci->precio }}" id="price{{$dci->id}}"
                                        wire:change="updatePrice({{$dci->id}}, $('#price' + {{$dci->id}}).val() )" min="1" >
                                        </div>

                                        
                                        </td>
                                        
                                            
                                            @foreach($total as $f)

                                            @if($f->tipo_comprobante == "A")
                                            <td style="display:{{$style2}};" class="text-center">
                                            {{$dci->alicuota_iva*100}} %
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
                                             {{$dci->alicuota_iva*100}} % <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                             </button>
                                             <div class="dropdown-menu">


                                             <button  id="iva{{$dci->id}}" wire:click="UpdateIva({{$dci->id}}, $('#iva' + {{$dci->id}}).val() )"  value="0" class="dropdown-item">Sin IVA</button>
                                             <button id="ivaprimero{{$dci->id}}" wire:click="UpdateIva({{$dci->id}}, $('#ivaprimero' + {{$dci->id}}).val() )"  value="0.105" class="dropdown-item">10,5%</button>
                                             <button id="ivasegundo{{$dci->id}}" wire:click="UpdateIva({{$dci->id}}, $('#ivasegundo' + {{$dci->id}}).val() )"  value="0.21" class="dropdown-item">21%</button>
                                             <button id="ivatercero{{$dci->id}}" wire:click="UpdateIva({{$dci->id}}, $('#ivatercero' + {{$dci->id}}).val() )"  value="0.27" class="dropdown-item">27%</button>
                                                									</div>
                                                                							</div>
                                                                            </td>
                                       
                                        <td class="text-right">
                                        $ {{number_format($dci->precio*$dci->cantidad*(1+$dci->alicuota_iva),2)}}
                                        </td>    
                            

                                        
                                         <td class="text-right">

                                       <a style="display:{{$style}};" href="javascript:void(0)" onclick="ConfirmDelete({{$dci->id}})" >
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

                <div class="row mt-4">
                    <div class="col-sm-5 col-12 order-sm-0 order-1">
                        <div class="inv--payment-info">
                            <div class="row">
                                <div class="col-sm-12 col-12">
                                    <h6 class=" inv-title">Informacion adicional:</h6>
                                </div>
                                @foreach ($total as $t)

                                <div class="col-sm-4 col-12">
                                    <p class=" inv-subtitle">Observaciones: </p>
                                </div>
                                <div class="col-sm-8 col-12">
                                    <p class="">{{$t->observaciones}}</p>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7 col-12 order-sm-1 order-0">
                        <div class="inv--total-amounts text-sm-right">
                            <div class="row">



                                <div class="col-sm-8 col-7 grand-total-title">
                                    <h6 class="">Subtotal : </h6>
                                </div>
                                <div class="col-sm-4 col-5 grand-total-amount">
                                    <h6 class=""> $ {{number_format($t->subtotal,2)}}</h6>
                                </div>
                                @if($t->iva)
                                <div class="col-sm-8 col-7 grand-total-title">
                                    <h6 class="">IVA : </h6>
                                </div>
                                <div class="col-sm-4 col-5 grand-total-amount">
                                    <h6 class=""> $ {{number_format($t->iva,2)}}</h6>
                                </div>
                                @endif
                                <div class="col-sm-8 col-7 grand-total-title">
                                    <h4 class="">Total : </h4>
                                </div>
                                <div class="col-sm-4 col-5 grand-total-amount">
                                    <h4 class=""> $ {{number_format($t->total,2)}}</h4>
                                </div>
                                <div class="col-sm-8 col-7 grand-total-title">
                                    <h4 class="">Deuda : </h4>
                                </div>
                                <div class="col-sm-4 col-5 grand-total-amount">
                                    <h4 class=""> $ {{number_format($t->deuda,2)}}</h4>
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

<script type="text/javascript">


 function ConfirmDelete(id) {
                                
swal({
title: 'CONFIRMAR',
text: 'QUIERE ELIMINAR EL PRODUCTO DE LA COMPRA?',
type: 'warning',
showCancelButton: true,
cancelButtonText: 'Cancelar',
cancelButtonColor: '#fff',
confirmButtonColor: '#3B3F5C',
confirmButtonText: 'Aceptar'
}).then(function(result) {
if (result.value) {
    window.livewire.emit('EliminarProducto', id)
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

<script type="text/javascript">
function Update(index){
	var stock_descubierto = $("#stock_descubierto"+index).val();
	if(stock_descubierto === "si") {
	var cantidad = $("#qty"+index).val();
	var stock_max = $("#stock_max"+index).val();

	if(cantidad === stock_max) {
    $("#stock_max"+index).css("display","block");
	} else {
    $("#stock_max"+index).css("display","none");
  }
  }
}

</script>
