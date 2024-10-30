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
                                F{{$tipo_comprobante}}
								</div>
								<div>
								<!---	<h4 class="mt-3">Articulos: {{$itemsQuantity}}</h4> --->
								</div>
								<br>

								<p>SUBTOTAL: $ {{number_format( $cart->subtotalAmount()  , 2)}}</p>
								<p>IVA: ${{number_format( $cart->totalIva() , 2 )}} </p>

								<h6 >CANT. ITEMS: {{number_format( $cart->totalCantidad() )}}</h6>
								<h3 >TOTAL: ${{number_format( $cart->totalAmount() , 2)}}</h3>
								<br><br>
								<i>
	                @if($deuda != null)
	                <b>Deuda: $ {{$deuda}}</b>
	                @endif
	              </i>

                        <div class="col-lg-12">
                            @if($cart->hasProducts())
							<a class="btn btn-cancel"  onclick="Confirm('','clearCart','¿SEGURO DE ELIMINAR EL CARRITO?')">Cancelar</a>
							@endif
							@if($cart->hasProducts())

							
							<button wire:click.prevent="saveSale"  wire:loading.attr="disabled" class="btn btn-submit me-2">Guardar</button>
							
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
