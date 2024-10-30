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
		<div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
			<table class="table table-bordered table-striped mt-1">
				<thead class="text-white" style="background: #3B3F5C">
					<tr>
						<th style="width:17%;" class="table-th text-left text-white">DESCRIPCIÓN</th>
						<th class="table-th text-center text-white">PRECIO</th>
						<th width="18%" class="table-th text-center text-white">CANT</th>
						<th class="table-th text-center text-white">ALTO</th>
						<th class="table-th text-center text-white">ANCHO</th>
						<th class="table-th text-center text-white">IMPORTE</th>
						<th style="width:60%;" class="table-th text-center text-white">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					@foreach($cart as $item)
					<tr>
						<td><h6>{{$item->name}}</h6> </td>
						<td class="text-center">
							<div style="display:flex;">
								<h6 style="margin-bottom: 0; margin-top:2px; ">$</h6>
									<input class="boton-precio" type="number" id="p{{$item->id}}"
									wire:change="updatePrice({{$item->id}}, $('#p' + {{$item->id}}).val() )"
									value="{{$recargo*$item->price}}">
							</div>

						</td>
						<td>
							<input type="number" id="r{{$item->id}}"
							wire:change="updateQty({{$item->id}}, $('#r' + {{$item->id}}).val() )"
							onchange="Update({{$item->id}});"
							style="font-size: 1rem!important"
							class="boton-precio"
							value="{{$item->quantity}}"
							>
							<input hidden id="q{{$item->id}}" value="{{$item->attributes['stock']}}">
						</td>
						<td class="text-center">
							<input type="number"
							style="font-size: 1rem!important"
							class="boton-precio"
							value="{{$item->quantity}}"
							>
						</td>
						<td class="text-center">
							<input type="number"
							style="font-size: 1rem!important"
							class="boton-precio"
							value="{{$item->quantity}}"
							>
						</td>
						<td class="text-center">
							<h6>
								${{number_format($item->price * $recargo * $item->quantity,2)}}
							</h6>
						</td>


						<td class="text-center">

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
