
<div class="row mt-3">
	<div class="col-sm-12">
		<div class="connect-sorting">
				<div class="card simple-title-task ui-sortable-handle">
					<div class="card-body">
						<div class="col-sm-12 col-md-12">
						 <div class="form-group">

							<label>Forma de pago</label>

							<select  wire:model='metodo_pago' 	wire:change='MetodoPago($event.target.value)' class="form-control">
									<option value="1">Efectivo</option>
									<option value="2">Pago dividido</option>
									@foreach($metodos as $metodo_pago)
									<option value="{{$metodo_pago->id}}">{{$metodo_pago->nombre}}</option>
									@endforeach
								</select>

						</div>
						<input style="margin:2.5%;" type="checkbox" wire:click="CheckPagoParcial({{$pago_parcial}})" {{$check}}>    Acepta pago parcial
						</div>
						<br>
						<div class="input-group input-group-md mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text input-gp hideonsm" style="background: #3B3F5C; color:white">Pago F8
								</span>
							</div>
							<input min="0" type="number" id="cash"
							onchange="cambio();"
							onkeyup="cambio();"
							wire:model="efectivo"
							wire:change='cambio($event.target.value)'
							wire:keydown.enter="saveSale"
							class="form-control text-center" value="${{number_format($efectivo,2)}}"
							>
							<div class="input-group-append">
								<span wire:click="EliminarMoneda(0)" class="input-group-text" style="background: #3B3F5C; color:white">
									<i class="fas fa-backspace fa-2x"></i>
								</span>
							</div>
						</div>



						<button wire:click.prevent="ACash(0)" class="btn btn-dark btn-block den">
							Pago exacto
						</button>
							<br>
							<p>Descuento:</p>
							<div style="max-width:160px;" class="input-group input-group-md mb-2">
								<input min="0" type="number" id="descuento"
								onchange="cambiodescuento();"
								onkeyup="cambiodescuento();"
								wire:model="descuento"
								wire:change='descuento($event.target.value)'
								class="form-control text-center" value="${{number_format($descuento,2)}}"
								>
								<div class="input-group-append">
									<span class="input-group-text" style="background: #3B3F5C; color:white">
									 %
									</span>
								</div>
							</div>

							@if($efectivo < $total && $total > 0  && $pago_parcial == 1)
							<h4 class="text-muted">Deuda: ${{number_format(-1*$change,2)}}</h4>

							@else
							@endif
							@if($efectivo >= $total && $total > 0  && $pago_parcial == 1)
							<h4 class="text-muted">Cambio: ${{number_format($change,2)}}</h4>

							@else
							@endif
							@if ($efectivo>= $total && $total > 0 && $pago_parcial == 0)
							<h4 class="text-muted">Cambio: ${{number_format($change,2)}}</h4>

							@else
							@endif

						<div class="row justify-content-between mt-5">
							<div class="col-sm-12 col-md-12 col-lg-6">
								@if($total > 0)
								<button  onclick="Confirm('','clearCart','¿SEGURO DE ELIMINAR EL CARRITO?')"
								class="btn btn-dark mtmobile">
								CANCELAR F4
							</button>
							@endif
						</div>

						<div class="col-sm-12 col-md-12 col-lg-6">
							@if($pago_parcial == 1)
							<button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">GUARDAR F6</button>

							@else
							@endif
							@if (($efectivo+0.01)>= ($total+$recargo_total-$descuento_total) && $total > 0 && $pago_parcial == 0)
							<button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">GUARDAR F6</button>
							@else
							@endif
						</div>


					</div>




				</div>
				<div class="col-sm-12 mt-1 text-center">
				</div>
			</div>
		</div>

	</div>

</div>
</div>
<script type="text/javascript">
function cambiodescuento(){
	var descuento = $("#descuento").val();
		if(descuento == '') {
		$("#descuento").val(0);

	}
}

</script>
