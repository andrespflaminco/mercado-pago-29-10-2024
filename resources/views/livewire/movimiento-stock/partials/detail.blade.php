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
				<thead>
					<tr>
						<th >CODIGO</th>
						<th >NOMBRE</th>
						<th class="text-center" >CANT</th>
						<th class="text-center">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($cart->getContent() as $product)
					<tr>
						<td>{{ $product['barcode'] }}</td>
						<td>{{ $product['name'] }}</td>

						<td class="text-center">
							<div class="btn-group" role="group" aria-label="Basic example">

							<input type="number" id="qty{{$product['id']}}" wire:change="updateQty( '{{$product['id']}}', $('#qty'+ '{{$product['id']}}' ).val())"
							style="width: 90px !important; height: 40px; color:#3b3f5c;"		class="form-control text-center"
							value="{{$product['qty']}}">
							<input hidden id="q{{$product['id']}}" value="{{$product['stock']}}">
							</div>
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
		<h5 class="text-center text-muted">Agrega productos al movimiento de stock</h5>
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
$('.basic').select2({
								tags: true
						});
</script>
