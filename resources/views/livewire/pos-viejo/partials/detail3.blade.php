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

		@if($itemsQuantity > 0)
		<div class="table-responsive tblscroll" style="max-height: none; overflow: auto;;">
			<table class="table table-bordered table-striped mt-1">
				<thead class="text-white" style="background: #3B3F5C">
					<tr>
						<th style="width:17%;" class="table-th text-left text-white">CODIGO</th>
						<th style="width:17%;" class="table-th text-left text-white">NOMBRE</th>
						<th class="table-th text-center text-white">PRECIO</th>
						<th style="width:15%;" class="table-th text-center text-white">CANT</th>
						<th style="width:20%;" class="table-th text-center text-white">IVA</th>
						<th class="table-th text-center text-white">TOTAL</th>
						<th style="width:30%;" class="table-th text-center text-white">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					@foreach($cart as $item)
					<tr>
						<td><h6>{{$item->attributes['barcode']}}</h6> </td>
						<td><h6>{{$item->name}}</h6> </td>
						<td class="text-center">
							<div style="display:flex;">
								<h6 style="margin-bottom: 0; margin-top:2px; ">$</h6>
									<input class="boton-precio" type="number" id="p{{$item->id}}"
									wire:change="updatePrice({{$item->id}}, $('#p' + {{$item->id}}).val() )"
									value="{{$item->price}}">
							</div>

						</td>
						<td>
							<input type="number" id="r{{$item->id}}"
							wire:change="updateQty({{$item->id}}, $('#r' + {{$item->id}}).val() )"
							onchange="Update({{$item->id}});"
							style="font-size: 1rem!important"
							class="botones1"
							value="{{$item->quantity}}"
							>
							<input hidden id="q{{$item->id}}" value="{{$item->attributes['stock']}}">
						</td>
						<td class="table-th text-center">
							<div class="btn-group mb-4 mr-2">
									<button style="margin-top: 20%; font-size: 15px;
									padding: 8px 10px;
									letter-spacing: 1px;
									width: 90px !important;
									padding: 0.5rem 0.5rem;  border: 1px solid #bfc9d4 !important;
    							color: #3b3f5c !important; background-color: #fff; " class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											{{$item->attributes['iva']*100}} % <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
									</button>
									<div class="dropdown-menu">
											<button  id="iva{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#iva' + {{$item->id}}).val() )"  value="0" class="dropdown-item">Sin IVA</button>
											<button id="ivaprimero{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#ivaprimero' + {{$item->id}}).val() )"  value="0.105" class="dropdown-item">10,5%</button>
											<button id="ivasegundo{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#ivasegundo' + {{$item->id}}).val() )"  value="0.21" class="dropdown-item">21%</button>
											<button id="ivatercero{{$item->id}}" wire:click="UpdateIva({{$item->id}}, $('#ivatercero' + {{$item->id}}).val() )"  value="0.27" class="dropdown-item">27%</button>
									</div>
							</div>


						</td>

						<td class="text-center">
							<h6>
								${{number_format(($item->price * $item->quantity) + ($item->attributes['iva']*$item->price*$item->quantity),2)}}
							</h6>
						</td>


						<td class="text-center">


							<button wire:click.prevent="decreaseQty({{$item->id}})" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button wire:click.prevent="increaseQty({{$item->id}})" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
							<button wire:click.prevent="comentario({{$item->id}})" class="btn btn-dark btn-sm">
								<i class="fas fa-list"></i>
							</button>
							<button onclick="Confirm('{{$item->id}}', 'removeItem', '¿CONFIRMAS ELIMINAR EL REGISTRO?')" class="btn btn-dark btn-sm">
								<i class="fas fa-trash-alt"></i>
							</button>

						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@else
		<h5 class="text-center text-muted">Agrega productos a la venta</h5>
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

					<span>Nota interna</span>
								<textarea wire:model.lazy="nota_interna" class="form-control" rows="3" cols="30"></textarea>

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
