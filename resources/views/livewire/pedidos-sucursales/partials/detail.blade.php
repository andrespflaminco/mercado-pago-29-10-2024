<style media="screen">
	.boton-precio:hover {
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
	.botones1 {
		height: auto;
		border: 1px solid #bfc9d4;
		color: #3b3f5c;
		font-size: 15px;
		text-align: center;
		letter-spacing: 1px;
		max-width: 90px;
		padding: 0.5rem 0.5rem;
		border-radius: 6px;
	}

	.botones2 {
		height: auto;
		border: 1px solid #bfc9d4;
		color: #3b3f5c;
		font-size: 15px;
		padding: 8px 10px;
		letter-spacing: 1px;
		max-width: 105px;
		padding: 0.5rem 0.5rem;
		border-radius: 6px;
	}
	.boton-precio:focus {
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
	.boton-precio {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border: none;
		text-align: center;
	}
</style>
<br>
<div class="connect-sorting">


<div class="connect-sorting-content">
	<div class="card simple-title-task ui-sortable-handle">
		<div class="card-body">

		@if ($cart->getContent()->count() > 0)
		<div class="table-responsive tblscroll" style="max-height: none; overflow: auto;;">
			<table class="table table-bordered table-striped mt-1">
				<thead class="text-white" style="background: #3B3F5C">
					<tr>
						<th class="table-th text-left text-white">NOMBRE</th>
						<th class="table-th text-center text-white">PRECIO</th>
						<th class="table-th text-center text-white">CANT</th>
						<th class="table-th text-center text-white">IVA</th>
						<th class="table-th text-center text-white">TOTAL</th>
						<th class="table-th text-center text-white">ACCIONES</th>
					</tr>
				</thead>
				<tbody>

				
					@foreach ($cart->getContent()->sortByDesc('orderby_id') as $product)
										
					<tr>

						<td><h6> {{ $product['name'] }}</h6></td>
						<td class="text-center">
							<div style="display:flex;">
								<h6 style="margin-bottom: 0; margin-top:2px; ">$</h6>
									<input class="boton-precio" type="number" id="price{{$product['id']}}" wire:change.lazy="UpdatePrice( '{{$product['id']}}', $('#price' + '{{$product['id']}}').val() )"
									value="{{$product['cost']}}">							
							</div>
				
							

						</td>
						<td class="text-center">
							<div class="btn-group" role="group" aria-label="Basic example">

								<input type="number" id="qty{{$product['id']}}"
								style="width: 90px !important; height: 40px; color:#3b3f5c;"		class="form-control text-center"
								value="{{$product['qty']}}"
								wire:change="updateQty('{{$product['id']}}' , $('#qty' + '{{$product['id']}}').val() )" >


							</div>
						</td>
						<td class="text-center">
							<div class="btn-group mb-4 mr-2">
									<button style="margin-top: 20%; font-size: 15px;
									padding: 8px 10px;
									letter-spacing: 1px;
									width: 90px !important;
									padding: 0.5rem 0.5rem;  border: 1px solid #bfc9d4 !important;
    							color: #3b3f5c !important; background-color: #fff; " class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											{{$product['iva']*100}} % <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
									</button>
									<div class="dropdown-menu">
											<button  id="iva{{$product['id']}}" wire:click="UpdateIva( '{{$product['id']}}' , $('#iva' + '{{$product['id']}}').val() )"  value="0" class="dropdown-item">Sin IVA</button>
											<button id="ivaprimero{{$product['id']}}" wire:click="UpdateIva( '{{$product['id']}}' , $('#ivaprimero' + '{{$product['id']}}').val() )"  value="0.105" class="dropdown-item">10,5%</button>
											<button id="ivasegundo{{$product['id']}}" wire:click="UpdateIva( '{{$product['id']}}' , $('#ivasegundo' + '{{$product['id']}}').val() )"  value="0.21" class="dropdown-item">21%</button>
											<button id="ivatercero{{$product['id']}}" wire:click="UpdateIva( '{{$product['id']}}' , $('#ivatercero' + '{{$product['id']}}').val() )"  value="0.27" class="dropdown-item">27%</button>
									</div>
							</div>
						</td>
						<td class="text-center">
							<h6>
								$ {{ $product['cost']*$product['qty'] + ( $product['iva']*$product['cost']*$product['qty'] )  }}
							</h6>
						</td>
						<td class="text-center">
							<button wire:click="removeProductFromCart('{{$product['id']}}')" class="btn btn-dark mbmobile">
								<i class="fas fa-trash-alt"></i>
							</button>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@else
		<h5 class="text-center text-muted">Agrega productos a la compra</h5>
		@endif
<!--
		<div wire:loading.inline wire:target="saveSale">
			<h4 class="text-danger text-center">Guardando Venta...</h4>
		</div>
	-->



		</div>
		</div>
		</div>
		</div>
		<br>
		<footer>
<div class="connect-sorting">
<div class="connect-sorting-content">



		<span>Observaciones</span>
			<textarea wire:model.lazy="observaciones" class="form-control" rows="3" cols="30"></textarea>


</div>


</div>
</footer>
<script type="text/javascript">

function Update(index){
	var stock_descubierto = $("#stock_descubierto"+index).val();
	if(stock_descubierto === "si") {
	var cantidad = $("#r"+index).val();
	var stock = $("#q"+index).val();

	if(cantidad > stock) {
	noty('STOCK INSUFICIENTE. DISPONIBLES: '+stock, 'alert'); // default
		setTimeout(function(){ 	$("#r"+index).val(stock); }	, 200);
	}
  }
}

</script>
<script type="text/javascript">
$('.basic').select2({
								tags: true
						});
</script>
