<head>
	<style media="screen">
		.layout-top-spacing {
    width: 100% !important;
    margin-top: 100px;
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
.row {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: 0;
    margin-left: 0;
}
.layout-px-spacing {
	width: 100% !important;
	padding: 0 10px 0 30px !important;

}
	</style>
</head>

<!-- Modal -->
<div class="modal fade register-modal" id="theModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header" id="registerModalLabel">
				<h4 class="modal-title">Registro</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
			</div>
			<div class="modal-body">

				<form action="{{ route('pay') }}" method="POST" id="paymentForm">
						@csrf

						<div  class="row">
								<div class="col-auto">
										<label>Valor del plan</label>
										<input
												id="plan"
												readonly
												type="number"
												min="5"
												step="0.01"
												class="form-control"
												name="value"
												value="6900"
												required
										>
										<small hidden class="form-text text-muted">
												Use values with up to two decimal positions, using dot "."
										</small>
								</div>
								<div class="col-auto">
										<label>Currency</label>
										<select class="custom-select" name="currency" required>
												@foreach ($currencies as $currency)
														<option value="ARS">ARS</option>
												@endforeach
										</select>
								</div>

						</div>

						<div class="btn-group btn-group-toggle" data-toggle="buttons">
								@foreach ($paymentPlatforms as $paymentPlatform)
										<label
												class="btn btn-outline-secondary rounded m-2 p-1"
												data-target="#{{ $paymentPlatform->name }}Collapse"
												data-toggle="collapse"
										>
												<input
														type="radio"
														name="payment_platform"
														value="{{ $paymentPlatform->id }}"
														required
												>
												<img class="img-thumbnail" src="{{ asset('assets/img/' . $paymentPlatform->image) }}">
										</label>
								@endforeach
						</div>
						<br>

				<label class="mt-3">Detalles de la tarjeta de credito:</label>


				<div class="form-group form-row">
				    <div class="col-5">
				        <input class="form-control" type="text" id="cardNumber" data-checkout="cardNumber" placeholder="Numero de tarjeta">
				    </div>

				    <div class="col-2">
				        <input class="form-control" type="text" data-checkout="securityCode" placeholder="CVC">
				    </div>

				    <div class="col-1"></div>

				    <div class="col-1">
				        <input class="form-control" type="text" data-checkout="cardExpirationMonth" placeholder="MM">
				    </div>

				    <div class="col-1">
				        <input class="form-control" type="text" data-checkout="cardExpirationYear" placeholder="YY">
				    </div>
				</div>



				<div class="form-group form-row">
				    <div class="col-5">
				        <input class="form-control" type="text" data-checkout="cardholderName" placeholder="Nomnre Name">
				    </div>
				    <div class="col-5">
				        <input class="form-control" type="email" data-checkout="cardholderEmail" placeholder="email@example.com" name="email">
				    </div>
				</div>


				<div class="form-group form-row">
				    <div class="col-2">
				        <select class="custom-select" data-checkout="docType"></select>
				    </div>
				    <div class="col-3">
				        <input class="form-control" type="text" data-checkout="docNumber" placeholder="Document">
				    </div>
				</div>

				<div class="form-group form-row">
				    <div class="col">
				        <small class="form-text text-mute"  role="alert" >Your payment will be converted to {{ strtoupper(config('services.mercadopago.base_currency')) }}</small>
				    </div>
				</div>

				<div class="form-group form-row">
				    <div class="col">
				        <small class="form-text text-danger" id="paymentErrors" role="alert"></small>
				    </div>
				</div>

				<input hidden id="cardNetwork" name="card_network">
				<input  hidden id="cardToken" name="card_token">

				<div class="text-center mt-3">
						<button type="submit" id="payButton" class="btn btn-primary btn-lg">Pagar</button>
				</div>
		</form>

			</div>


</div>


</div>

</div>
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
