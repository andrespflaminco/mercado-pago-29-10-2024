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


								<label>Forma de pago</label>

	              <select wire:model='metodo_pago_elegido' class="form-control">
	                <option value="Elegir" disabled>Elegir</option>
	                <option value="1">Efectivo</option>
	                @foreach($metodo_pago as $mp)
	                  <option value="{{$mp->id}}" >{{$mp->nombre}}</option>
	                @endforeach
	              </select>
								<br>



							<div class="input-group input-group-md mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text input-gp hideonsm" style="background: #3B3F5C; color:white">Pago F8
									</span>
								</div>
								<input type="text" class="form-control text-center" wire:change="MontoPago()" wire:model="pago">
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

								@if($cart->totalIva() > 0)
									<a href="javascript:void(0)" class="btn btn-dark btn-md btn-block" data-toggle="modal" data-target="#theModal2">GUARDAR</a>

								@else
								<button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">GUARDAR</button>
								@endif

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
