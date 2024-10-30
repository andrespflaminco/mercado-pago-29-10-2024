<head>
    
    
<link href="{{ asset('plugins/pricing-table/css/component.css') }}" rel="stylesheet" type="text/css" />

	<style>

	body {
	    background-image: url(../assets/img/regist_2.jpg);
	    background-size: cover;
	    background-repeat: no-repeat;
			background-position: top center;
	}
	
	
#paymentForm {
	padding-left: 8%;
	padding-right: 8%;
	padding-bottom: 20px;
}

.componente {
  position: absolute;
  margin-top: 1.35%;
    width: 67.5%;
    margin-left: 26.5%;
}

@media only screen and (max-width: 820px) {
    
    .recommended-badge {
        display:none;
    }
    
    .mt-sm-3 {
        margin-top: 25px !important;
    }
    
     .mb-sm-3 {
        margin-top: 15px !important;
    }

	.logo{
    display: none;
}

	.componente {
        width: 90%;
    margin-left: 5%;
}

.contenedor.card-contenedor.front.card-number-box {
    padding: 5px 0;
    font-size: 22px;
    color: #fff;
}


}


.contenedor {
    min-height: 38vh;
    background: #eee;
    display: flex;
    align-items: center;
    /* justify-content: center; */
    flex-flow: column;
    /* padding-bottom: 60px; */
    padding: 15px;
}


	.contenedor form{
	    background: #fff;
	    border-radius: 5px;
	    box-shadow: 0 10px 15px rgba(0,0,0,.1);
	    padding: 20px;
	    width: 600px;
	    padding-top: 160px;
	}

	.contenedor form .inputBox{
	    margin-top: 20px;
	}

	.contenedor form .inputBox span{
	    display: block;
	    color:#999;
	    padding-bottom: 5px;
	}

	.contenedor form .inputBox input,
	.contenedor form .inputBox select{
	    width: 100%;
	    padding: 10px;
	    border-radius: 10px;
	    border:1px solid rgba(0,0,0,.3);
	    color:#444;
	}

	.contenedor form .flexbox{
	    display: flex;
	    gap:15px;
	}

	.contenedor form .flexbox .inputBox{
	    flex:1 1 150px;
	}

	.contenedor form .submit-btn{
	    width: 100%;
	    background:linear-gradient(45deg, #4d4d4d, #898888);
	    margin-top: 20px;
	    padding: 10px;
	    font-size: 20px;
	    color:#fff;
	    border-radius: 10px;
	    cursor: pointer;
	    transition: .2s linear;
	}

	.contenedor form .submit-btn:hover{
	    letter-spacing: 2px;
	    opacity: .8;
	}

	.contenedor .card-contenedor {
	    margin-bottom: -150px;
	    position: relative;
	    height: 200px;
	    width: 320px;
	}


	.contenedor .card-contenedor .front{
	    position: absolute;
	    height: 100%;
	    width: 100%;
	    top: 0; left: 0;
	    background:linear-gradient(45deg, #4d4d4d, #898888);
	    border-radius: 5px;
	    backface-visibility: hidden;
	    box-shadow: 0 15px 25px rgba(0,0,0,.2);
	    padding:20px;
	    transform:perspective(1000px) rotateY(0deg);
	    transition:transform .4s ease-out;
	}


	.contenedor .card-contenedor .front .image{
	    display: flex;
	    align-items:center;
	    justify-content: space-between;
	    padding-top: 10px;
	}

	.contenedor .card-contenedor .front .image img{
	    height: 40px;
	}

	.contenedor .card-contenedor .front .card-number-box {
	    padding: 15px 0;
	    font-size: 15px;
	    color: #fff;
	}

	.contenedor .card-contenedor .front .flexbox{
	    display: flex;
	}

	.contenedor .card-contenedor .front .flexbox .box:nth-child(1){
	    margin-right: auto;
	}

	.contenedor .card-contenedor .front .flexbox .box{
	    font-size: 15px;
	    color:#fff;
	}

	.contenedor .card-contenedor .back{
	    position: absolute;
	    top:0; left: 0;
	    height: 100%;
	    width: 100%;
	    background:linear-gradient(45deg, #4d4d4d, #898888);
	    border-radius: 5px;
	    padding: 20px 0;
	    text-align: right;
	    backface-visibility: hidden;
	    box-shadow: 0 15px 25px rgba(0,0,0,.2);
	    transform:perspective(1000px) rotateY(180deg);
	    transition:transform .4s ease-out;
	}

	.contenedor .card-contenedor .back .stripe{
	    background: #000;
	    width: 100%;
	    margin: 10px 0;
	    height: 50px;
	}

	.contenedor .card-contenedor .back .box{
	    padding: 0 20px;
	}

	.contenedor .card-contenedor .back .box span{
	    color:#fff;
	    font-size: 15px;
	}

	.contenedor .card-contenedor .back .box .cvv-box{
	    height: 50px;
	    padding: 10px;
	    margin-top: 5px;
	    color:#333;
	    background: #fff;
	    border-radius: 5px;
	    width: 100%;
	}

	.contenedor .card-contenedor .back .box img{
	    margin-top: 30px;
	    height: 30px;
	}






		.layout-top-spacing {
    width: 100% !important;
    margin-top: 100px;
}

.modal-dialog {
    max-width: 750px !important;
    margin: 1.75rem auto;
}

.faq .faq-layouting .fq-comman-question-wrapper {
    padding: 52px 52px;
    -webkit-box-shadow: 0 10px 30px 0 rgb(31 45 61 / 10%);
    box-shadow: 0 10px 30px 0 rgb(31 45 61 / 10%);
    border-radius: 15px;
    background: #fff;
    margin-top: 0;
    margin-bottom: 70px;
}
	#MP-Checkout-dialog {
    z-index: 999999 !important;
}
.navbar {
	display: none !important;

}
.row {
		position: absolute;
		width: 100%;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: 0;
    margin-left: 0;
}
.layout-px-spacing {
	width: 100% !important;
	padding: 0 !important;

}
	</style>
	<style>
	.logo img {

width: 100%;
height: auto;

}

#boton {
		position: absolute;
		margin-top: 34%;
		font-size: 26px;
		font-weight: bold;
		margin-left: 42%;
		background: #1e324b !important;
		border-color: #1e324b !important;

}

#ayuda {
		position: absolute;
		margin-top: 38.5%;
		font-size: 15px;
		margin-left: 43%;
		cursor:pointer;
		color: #1e324b;

}


	@media only screen and (max-width: 600px) {
		#connect-sorting {
			display: block !important;
		}
	}

	.card {
			width: 100%;

	}

	@media only screen and (min-width: 600px) {
		#connect-sorting {
			display: flex !important;
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
</head>

<body width="100%">

	<!-- Modal -->
	<div class="modal fade register-modal" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">

				<div class="modal-header" id="registerModalLabel">
					<h4 class="modal-title">Registro</h4>
					<img style="margin-left: 7%; width: 120px;" src="{{ asset('assets/img/mp4.webp') }}" alt="">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
				</div>
				<div class="modal-body" style="padding: 0 !important;">

					<div class="contenedor">


						<div class="card-contenedor">

								<div id="front" class="front">
										<div class="image">

												<img src="{{ asset('assets/img/chip.png') }}" alt="">
												<img id="frontimg" src="{{ asset('assets/img/visa.png') }}" alt="">
										</div>
										<div class="card-number-box">################</div>
										<div class="flexbox">
												<div class="box">
														<span>TITULAR</span>
														<div class="card-holder-name">NOMBRE EN LA TARJETA</div>
												</div>
												<div class="box">
														<span>Expira</span>
														<div class="expiration">
																<span class="exp-month">MM</span> /	<span class="exp-year">AA</span>
														</div>
												</div>
										</div>
								</div>

								<div id="back" class="back">
										<div class="stripe"></div>
										<div class="box">
												<span>cvv</span>
												<div class="cvv-box"></div>
												<img id="backimg" src="{{ asset('assets/img/visa.png') }}" alt="">
										</div>
								</div>

						</div>
					</div>


					<form action="{{ route('pay') }}" method="POST" id="paymentForm">
							@csrf

							<div hidden class="row">
									<div class="col-auto">
										<input type="number" id="id_plan" name="plan">
											<label>Valor del plan</label>
											<input
													id="valor"
													type="number"
													min="5"
													step="0.01"
													class="form-control"
													name="value"
													required
											>
											<small hidden class="form-text text-muted">
													Use values with up to two decimal positions, using dot "."
											</small>
									</div>
									<div class="col-auto">
											<label>Moneda</label>
											<select class="custom-select" name="currency" required>
													@foreach ($currencies as $currency)
															<option value="ARS">ARS</option>
													@endforeach
											</select>
									</div>

							</div>


									@foreach ($paymentPlatforms as $paymentPlatform)

													<input
															type="hidden"
															name="payment_platform"
															value="{{ $paymentPlatform->id }}"
															required
													>

									@endforeach
							<br>

					<label class="mt-3">Detalles de la tarjeta:</label>


					<div class="form-group form-row">
					    <div class="col-lg-8 col-sm-12">
					        <input class="form-control" type="text" id="cardNumber" onkeyup="setCardNetwork();" data-checkout="cardNumber" placeholder="Numero de tarjeta">
									<small>Ingrese los numeros sin espacios.</small>
					    </div>

 						<div class="col-lg-2 col-sm-6">
					        <input class="form-control" id="expimes" type="text" data-checkout="cardExpirationMonth" placeholder="MM">
					    </div>

					    <div class="col-lg-2 col-sm-6">
					        <input class="form-control" id="expiano" type="text" data-checkout="cardExpirationYear" placeholder="AA">
					    </div>
					</div>



					<div class="form-group form-row">
					    <div class="col-10">
					        <input class="form-control" id="nombre" type="text" data-checkout="cardholderName" placeholder="Nombre que aparece en la tarjeta">
					    </div>

							<div class="col-2">
									<input class="form-control" id="code" type="text" data-checkout="securityCode" placeholder="CVC">
							</div>

					</div>

					<label class="mt-3">Datos del comprador:</label>

					<div class="form-group form-row">

					    <div class="col-2">
					        <select class="custom-select" data-checkout="docType"></select>
					    </div>
					    <div class="col-5">
					        <input class="form-control" type="text" data-checkout="docNumber" placeholder="Documento">
					    </div>
							<div class="col-5">
									<input class="form-control" type="email" data-checkout="cardholderEmail" placeholder="email@ejemplo.com" name="email">
							</div>
					</div>

					<div class="form-group form-row">
					    <div class="col">

					    </div>
					</div>

					<div class="form-group form-row">
					    <div class="col">
					        <small class="form-text text-danger" id="paymentErrors" role="alert"></small>
					    </div>
					</div>

					<input  hidden id="cardNetwork" name="card_network">
					<input  hidden id="cardToken" name="card_token">

					<div class="text-right mt-3">
							<button type="submit" id="payButton" class="btn btn-primary btn-lg">Pagar</button>
					</div>
			</form>

				</div>





	</div>


	</div>

	</div>

<div class="componente">

	                                    <!-- Pricing Plans Container -->
	                                    <div class="pricing-plans-container mt-lg-5 mt-sm-2 d-md-flex d-block">


	                                        <!-- Plan 1 -->


	                                        <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
	                                            <h3 class="text-center">INICIAL</h3>
	                                            <div class="pricing-plan-label billed-monthly-label"><strong>$1990</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            <div class="pricing-plan-features mb-4">
	                                                <strong>Hasta 1 usuarios</strong>
	                                                <ul>
																										<li>
																												<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																											Cajas diarias</li>
																											<li>
																													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																												Stock</li>

																												<li>
																														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																													Lector de codigo de barras</li>

																											<li>
																												<li>
																														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																													Tickets </li>

																												<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>

																												Facturación AFIP</li>
																												<li>

																											<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>

																											Compras y gastos</li>
																												<li>

																											<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>

																											Tienda oline</li>



																											<li>
																											<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>

																											Hoja de ruta </li>

																											<li>
	                                                </ul>
	                                            </div>
	                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#registerModal" onclick="Plan('1')" class="button btn btn-default btn-block margin-top-20">Suscribirse</a>
	                                        </div>



	                                        <!-- Plan 2 -->

	                                        <div class="pricing-plan mb-lg-5 mg-sm-0 mt-lg-md-0 mt-sm-3 mb-sm-3 recommended">
	                                            <div style="background: #1e324b !important; " class="recommended-badge">Recomendado</div>
	                                            <h3 class="text-center">INTERMEDIO</h3>
	                                            <div class="pricing-plan-label billed-monthly-label"><strong style="color: #67a9f6;;">$3500</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$670</strong>/ yearly</div>
	                                            <div class="pricing-plan-features mb-4">
	                                                <strong>Hasta 3 usuarios</strong>
	                                                <ul>
																										<ul>
  																										<li>
  																												<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

  																											Cajas diarias</li>
  																											<li>
  																													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

  																												Stock</li>

  																												<li>
  																														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

  																													Lector de codigo de barras</li>

  																											<li>
  																												<li>
  																														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

  																													Tickets </li>

  																												<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

  																												Facturación AFIP</li>

																													<li>

																											<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																												Compras y gastos</li>


  																												<li>

  																											<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>

  																											Tienda oline</li>


  																											<li>
  																											<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>

  																											Hoja de ruta </li>

  																											<li>
  	                                                </ul>

	                                                </ul>
	                                            </div>
	                                            <a href="javascript:void(0);" style="background-color: #1e324b !important;" data-toggle="modal" data-target="#registerModal" onclick="Plan('2')" class="button btn btn-default btn-block margin-top-20">Suscribirse</a>
																							<br>

																								<a  href="https://wa.me/+5493518681453?text=Necesito%20ayuda%20para%20suscribirme%20" target="_blank" name="button"> <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-headphones"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg> ¿Necesitas ayuda? </a>

	                                        </div>

	                                        <!-- Plan 3 -->


	                                        <div class="pricing-plan mb-lg-5 mb-sm-5 mt-sm-3 mb-sm-3">
	                                            <h3 class="text-center">FULL</h3>
	                                            <div class="pricing-plan-label billed-monthly-label"><strong>$4900</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$1100</strong>/ yearly</div>
	                                            <div class="pricing-plan-features mb-4">
	                                                <strong>Usuarios ilimitados</strong>
	                                                <ul>
																										<ul>
																										 <li>
																												 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																											 Cajas diarias</li>
																											 <li>
																													 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																												 Stock</li>

																												 <li>
																														 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																													 Lector de codigo de barras</li>

																											 <li>
																												 <li>
																														 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																													 Tickets </li>

																												 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																												 Facturación AFIP</li>
																												 <li>

																											 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																											 Compras y gastos</li>

																												 <li>

																											 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																											 Tienda oline</li>


																											 <li>
																											 <svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>

																											 Hoja de ruta </li>

																											 <li>
																									 </ul>
	                                                </ul>
	                                            </div>
	                                             <a href="javascript:void(0);" data-toggle="modal" data-target="#registerModal" onclick="Plan('3')" class="button btn btn-default btn-block margin-top-20">Suscribirse</a>
	                                        </div>


	                                    </div>

</div>



	<a hidden class="btn btn-danger" href="https://wa.me/+5493518681453?text=Quiero%20suscribirme%20a%20Flaminco%20Express%20" id="boton" target="_blank" name="button"> Suscribirse </a>




</body>
</html>
<script src="assets/js/libs/jquery-3.1.1.min.js"></script>
<script src="bootstrap/js/popper.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="assets/js/app.js"></script>

<script type="text/javascript">

	function Plan(plan) {

		if(plan == 1) {
			var valor = "1990";
			var plan = 1;
		}
		if(plan == 2) {
			var valor = "3500";
			var plan = 2;
		}
		if(plan == 3) {
			var valor = "4900";
			var plan = 3;
		}

		$("#valor").val(valor);
		$("#id_plan").val(plan);

	}
</script>


<script>


document.querySelector('#cardNumber').oninput = () =>{
		document.querySelector('.card-number-box').innerText = document.querySelector('#cardNumber').value;
}

document.querySelector('#nombre').oninput = () =>{
		document.querySelector('.card-holder-name').innerText = document.querySelector('#nombre').value;
}

document.querySelector('#expimes').oninput = () =>{
		document.querySelector('.exp-month').innerText = document.querySelector('#expimes').value;
}

document.querySelector('#expiano').oninput = () =>{
		document.querySelector('.exp-year').innerText = document.querySelector('#expiano').value;
}

document.querySelector('#code').onmouseenter = () =>{
		document.querySelector('.front').style.transform = 'perspective(1000px) rotateY(-180deg)';
		document.querySelector('.back').style.transform = 'perspective(1000px) rotateY(0deg)';
}

document.querySelector('#code').onmouseleave = () =>{
		document.querySelector('.front').style.transform = 'perspective(1000px) rotateY(0deg)';
		document.querySelector('.back').style.transform = 'perspective(1000px) rotateY(180deg)';
}

document.querySelector('#code').oninput = () =>{
		document.querySelector('.cvv-box').innerText = document.querySelector('#code').value;
}

</script>



<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
		});

});
		</script>


		<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>


		<script>

		    window.Mercadopago.setPublishableKey('{{ config('services.mercadopago.key') }}');

		    window.Mercadopago.getIdentificationTypes();


		</script>

		<script>
		    function setCardNetwork()
		    {
		        const cardNumber = document.getElementById("cardNumber");

		        window.Mercadopago.getPaymentMethod(
		            { "bin": cardNumber.value.substring(0,6) },
		            function(status, response) {
		                const cardNetwork = document.getElementById("cardNetwork");

		                cardNetwork.value = response[0].id;

										var net =	document.getElementById("cardNetwork").value;


										if(net == "visa") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, red, #007bff)';
											document.getElementById('back').style.background = 'linear-gradient(45deg, red, #007bff)';
											document.getElementById('frontimg').src="{{ asset('assets/img/visa.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/visa.png') }}";
										}
										if(net == "debvisa") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, #602525, rgb(60 74 89))';
											document.getElementById('back').style.background = 'linear-gradient(45deg, #602525, rgb(60 74 89))';
											document.getElementById('frontimg').src="{{ asset('assets/img/visa.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/visa.png') }}";
										}
										if(net == "master") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, rgb(25 21 61), rgb(0 123 255 / 72%))';
											document.getElementById('back').style.background = 'linear-gradient(45deg, rgb(25 21 61), rgb(0 123 255 / 72%))';
											document.getElementById('frontimg').src="{{ asset('assets/img/master.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/master.png') }}";
										}
										if(net == "debmaster") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, rgb(25 21 61), rgb(0 123 255 / 72%))';
											document.getElementById('back').style.background = 'linear-gradient(45deg, rgb(25 21 61), rgb(0 123 255 / 72%))';
											document.getElementById('frontimg').src="{{ asset('assets/img/master.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/master.png') }}";
										}
										if(net == "cordobesa") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, #009688, #ade5b9)';
											document.getElementById('back').style.background = 'linear-gradient(45deg,  #009688, #ade5b9)';
											document.getElementById('frontimg').src="{{ asset('assets/img/master.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/master.png') }}";
										}

										if(net == "cabal") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, #848271, #cead18)';
											document.getElementById('back').style.background = 'linear-gradient(45deg,  #848271, #cead18)';
											document.getElementById('frontimg').src="{{ asset('assets/img/cabal.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/cabal.png') }}";
										}

										if(net == "debcabal") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, #848271, #cead18)';
											document.getElementById('back').style.background = 'linear-gradient(45deg,  #848271, #cead18)';
											document.getElementById('frontimg').src="{{ asset('assets/img/cabal.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/cabal.png') }}";
										}

										if(net == "naranja") {
											document.getElementById('front').style.background = 'linear-gradient(45deg, #b8966a, rgb(211 206 199))';
											document.getElementById('back').style.background = 'linear-gradient(45deg, #b8966a, rgb(211 206 199))';
											document.getElementById('frontimg').src="{{ asset('assets/img/naranja.png') }}";
											document.getElementById('backimg').src="{{ asset('assets/img/naranja.png') }}";
										}
		            }


		        );
		    }
		</script>

		<script>
		    const mercadoPagoForm = document.getElementById("paymentForm");

		    mercadoPagoForm.addEventListener('submit', function(e) {


					const cardNumber = document.getElementById("cardNumber");

					window.Mercadopago.getPaymentMethod(
							{ "bin": cardNumber.value.substring(0,6) },
							function(status, response) {
									const cardNetwork = document.getElementById("cardNetwork");

									cardNetwork.value = response[0].id;
							}
					);


		        if (mercadoPagoForm.elements.payment_platform.value === "{{ $paymentPlatform->id }}") {
		            e.preventDefault();

		            window.Mercadopago.createToken(mercadoPagoForm, function(status, response) {
		                if (status != 200 && status != 201) {
		                    const errors = document.getElementById("paymentErrors");

		                    errors.textContent = response.cause[0].description;
		                } else {
		                    const cardToken = document.getElementById("cardToken");


		                    cardToken.value = response.id;

		                    mercadoPagoForm.submit();
		                }
		            });
		        }
		    });
		</script>
