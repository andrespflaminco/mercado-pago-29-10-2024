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

								<div class="row">
									<div class="col-5">
										<label>
											<b>FACTURAR</b>
										</label>
										<input type="checkbox" wire:model="facturar">
								</div>
									@if($facturar == true)
								<div class="col-7" wire:ignore>
										<select  wire:model.lazy='tipo_comprobante'  style="height: calc(1.2em + 1.4rem + 2px) !important;" class="form-control" >
											<option value="" disabled> Tipo de comprobante</option>
											<option value="A">Fact. A</option>
											<option value="B">Fact. B</option>
											<option value="C">Fact. C</option>

										</select>

							</div>
								@endif
								</div>

								 <br>

								<label>Tipo de IVA</label>

								<select  wire:model='relacion_precio_iva' wire:change='RelacionPrecioIva($event.target.value)'  class="form-control">

										<option value="1">Incluido en el precio</option>
										<option value="2">Precio + IVA</option>

								</select>

									<label>IVA</label>

									<select  wire:model='iva_general' wire:change='UpdateIvaGral($event.target.value)'  class="form-control">
											<option value="0">Sin IVA</option>
											<option value="0.105">10,5%</option>
											<option value="0.21">21%</option>
											<option value="0.27">27%</option>

										</select>

								<label>Tipo de pago</label>

								<select  wire:model='tipo_pago' wire:change='TipoPago($event.target.value)'  class="form-control">
										<option value="1">Efectivo</option>
										@foreach($tipos_pago as $tipos)
										<option value="{{$tipos->id}}">{{$tipos->nombre}}</option>
										@endforeach
										<option hidden value="OTRO" class="btn btn-dark">Agregar otro banco/plataforma</option>

									</select>

									@if($tipo_pago != 1 && $tipo_pago !=2)

								<label>Forma de pago</label>

									<select  wire:model='metodo_pago' 	wire:change='MetodoPago($event.target.value)' class="form-control">
										<option disabled value="Elegir">Elegir</option>
											@foreach($metodos as $metodo_pago)
											<option value="{{$metodo_pago->id}}">{{$metodo_pago->nombre}}</option>
											@endforeach
											<option hidden value="1">Efectivo</option>
										</select>
										@else

										@endif
										<br><br>

								<p>SUBTOTAL: $ {{number_format( $cart->subtotalAmount()  , 2)}}</p>
								<p>IVA: ${{number_format( $cart->totalIva() , 2 )}} </p>
								<h3 >TOTAL: ${{number_format( $cart->totalAmount() , 2)}}</h3>


							<div class="row justify-content-between mt-5">
								<div class="col-sm-12 col-md-12 col-lg-6">
									@if($cart->hasProducts())
									<button  onclick="Confirm(0)"
									class="btn btn-dark mtmobile">
									CANCELAR F4
								</button>
								@endif
							</div>

							<div class="col-sm-12 col-md-12 col-lg-6">
								@if($cart->hasProducts())

								<button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">GUARDAR</button>

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
