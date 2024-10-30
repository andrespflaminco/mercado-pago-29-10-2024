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

		<div class="table-responsive">
			<table class="table">
				<thead style="background: #F3F4F5;">
					<tr>
					    <th>CODIGO</th>
						<th>NOMBRE</th>
						<th>PRECIO</th>
						<th>CANT</th>
						<th hidden>DESC</th>
						<th>IVA</th>
						<th>TOTAL</th>
						<th>ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($cart->getContent()->sortByDesc('orderby_id') as $product)
					<tr>
                        <td>{{ $product['barcode'] }}</td>
						<td>{{ $product['product_id'] }} - {{ $product['name'] }}</td>
						<td >
						@if($product['relacion_precio_iva'] != 2)    
							<div style="display:flex;">
							$
									<input class="boton-precio" type="number" id="price{{$product['id']}}" wire:change.lazy="UpdatePrice('{{$product['id']}}', $('#price' + '{{$product['id']}}').val() )"
									value="{{$product['price']}}">
							</div>
                        @else 
                        	<div style="display:flex;">
							$
									<input class="boton-precio" type="number" id="price{{$product['id']}}" wire:change.lazy="UpdatePrice('{{$product['id']}}', $('#price' + '{{$product['id']}}').val() )"
									value="{{$product['price']*(1 + $product['iva'])}}">
							</div>
                        @endif

						</td>
						<td>
							<div class="btn-group" role="group" aria-label="Basic example">

							<input type="number" id="qty{{$product['id']}}" wire:change="updateQty( '{{$product['id']}}', $('#qty'+ '{{$product['id']}}').val())"
							style="width: 90px !important; height: 40px; color:#3b3f5c;"		class="form-control text-center"
							value="{{$product['qty']}}">
							</div>
						</td>

						<td hidden>

							<div style="width:100px;" class="input-group mb-0">
							  <input type="text" id="desc{{$product['id']}}" value="{{$product['descuento']}}"
								wire:keydown.enter="updateDescuento('{{$product['id']}}', $('#desc' + '{{$product['id']}}').val() )"
								wire:change="updateDescuento('{{$product['id']}}', $('#desc' + '{{$product['id']}}').val() )" class="form-control" >
							  <div class="input-group-append">
							    <span class="input-group-text">%</span>
							  </div>
							</div>

						</td>


						<td>
						@if($product['relacion_precio_iva'] != 2)    
						        <a style="color: #637381; font-weight: 500 !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{$product['iva']*100}} %</a>
                             	<div class="dropdown-menu">
                            		<button  id="iva{{$product['id']}}" wire:click="UpdateIva('{{$product['id']}}', $('#iva' + '{{$product['id']}}').val() )"  value="0"   class="dropdown-item">Sin IVA</button>
                            		<button  id="ivaprimero{{$product['id']}}" wire:click="UpdateIva('{{$product['id']}}', $('#ivaprimero' + '{{$product['id']}}').val() )"   value="0.105" class="dropdown-item">10,5%</button>
                            		<button  id="ivasegundo{{$product['id']}}" wire:click="UpdateIva('{{$product['id']}}', $('#ivasegundo' + '{{$product['id']}}').val() )"   value="0.21"  class="dropdown-item">21%</button>
                            		<button  id="ivatercero{{$product['id']}}" wire:click="UpdateIva('{{$product['id']}}', $('#ivatercero' + '{{$product['id']}}').val() )"   value="0.27"  class="dropdown-item">27%</button>
                            	</div>
                        @else
                        Iva {{$product['iva']*100}} % <br>
                        (incluido en el precio) 
                        @endif
						</td>
						<td>
							
								$ {{ $product['price']*$product['qty'] + ( $product['iva']*$product['price']*$product['qty'] )  }}
						
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
