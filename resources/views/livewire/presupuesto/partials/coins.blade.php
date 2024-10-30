
<div class="row mt-3">
	<div class="col-sm-12">
		<div class="connect-sorting">
				<div class="card simple-title-task ui-sortable-handle">
					<div class="card-body">
						<div class="col-sm-12 col-md-12">
						 <div class="form-group">

							<label>Forma de pago</label>


						</div>
						<input style="margin:2.5%;" type="checkbox" wire:click="CheckPagoParcial({{$pago_parcial}})" {{$check}}>    Acepta pago parcial
						</div>
						<br>



						<button wire:click.prevent="ACash(0)" class="btn btn-dark btn-block den">
							Pago exacto
						</button>
							<br>
							
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
