<br>
<div class="row">

	<div class="col-sm-12">
		<div>
			<div class="connect-sorting">
			<!---	<h5 class="text-center mb-3">RESUMEN DE VENTA </h5> -->
				<div class="connect-sorting-content">
					<div class="card simple-title-task ui-sortable-handle">
						<div class="card-body">

							<div class="task-header">

								<div>

								</div>
								<div>
								<!---	<h4 class="mt-3">Articulos: {{$itemsQuantity}}</h4> --->
								</div>


								<br>

								<h3 >CANT. ITEMS: {{number_format( $cart->totalCantidad() )}}</h3>
								<h3 >COSTO TOTAL: ${{number_format( $cart->totalAmount() , 2)}}</h3>
								<br><br>


							<div class="row justify-content-between mt-5">
								<div class="col-sm-12 col-md-12 col-lg-6">
									@if($cart->hasProducts())
									<button  onclick="Confirm('','clearCart','¿SEGURO DE ELIMINAR EL CARRITO?')"
									class="btn btn-dark mtmobile">
									CANCELAR F4
								</button>
								@endif
							</div>

							<div class="col-sm-12 col-md-12 col-lg-6">
								@if($cart->hasProducts())

								<button wire:click.prevent="saveProduccion" class="btn btn-dark btn-md btn-block">GUARDAR</button>

									@endif
							</div>


						</div>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
