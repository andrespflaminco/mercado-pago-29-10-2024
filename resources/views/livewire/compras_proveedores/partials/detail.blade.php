<br>
<div class="connect-sorting">


<div class="connect-sorting-content">
	<div class="card simple-title-task ui-sortable-handle">
		<div class="card-body">

		@if ($cart->getContent()->count() > 0)
	<div class="table-responsive">
		<table class="table">
				<thead>
					<tr>
					    <th>CODIGO</th>
						<th>NOMBRE</th>
						<th>PRECIO</th>
						<th>CANT</th>
						<th>IVA</th>
						<th>TOTAL</th>
						<th>ACCIONES</th>
					</tr>
				</thead>
				<tbody>

				
					@foreach ($cart->getContent()->sortByDesc('orderby_id') as $product)
										
					<tr>
                        <td> {{ $product['barcode'] }}</td>
						<td> {{ $product['name'] }}</td>
						<td>
							<div style="display:flex;">
								<p style="margin-bottom: 0; margin-top:2px; ">$</p>
									<input class="boton-precio" type="number" id="price{{$product['id']}}" wire:change.lazy="UpdatePrice( '{{$product['id']}}', $('#price' + '{{$product['id']}}').val() )"
									value="{{$product['cost']}}">							
							</div>
				
							

						</td>
						<td>
							<div class="btn-group" role="group" aria-label="Basic example">

								<input type="number" id="qty{{$product['id']}}"
								style="width: 90px !important; height: 40px; color:#3b3f5c;"		class="form-control text-center"
								value="{{$product['qty']}}"
								wire:change="updateQty('{{$product['id']}}' , $('#qty' + '{{$product['id']}}').val() )" >


							</div>
						</td>
						<td>
							
									<h6>{{$product['iva']*100}} %</h6>		
							
						</td>
						<td>
							<h6>
								$ {{ $product['cost']*$product['qty'] + ( $product['iva']*$product['cost']*$product['qty'] )  }}
							</h6>
						</td>
						<td>
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
