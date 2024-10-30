<br>
<div class="row">

	<div class="col-sm-12">
		<div>
			<div class="connect-sorting">

				<h5 class="text-center mb-3">RESUMEN DE VENTA <select  wire:model='comprobante' wire:change='MetodoComprobante($event.target.value)' class="form-control">
									<option value="1">Ticket</option>
									<option value="2">Detalle de ventas</option>
								</select></h5>

				<div class="connect-sorting-content">
					<div class="card simple-title-task ui-sortable-handle">
						<div class="card-body">

							<div class="task-header">

								<div>
									<h2>TOTAL: ${{number_format($total,2)}}</h2>
									<input type="hidden" id="hiddenTotal" value="{{$total}}">
								</div>
								<div>
									<h4 class="mt-3">Articulos: {{$itemsQuantity}}</h4>
								</div>


							</div>
							<div class="form-group">

							 <label>Canal de ventas</label>

							 <select  wire:model.lazy='canal_venta' 	class="form-control">
								 <option value="Mostrador">Mostrador</option>
								 <option value="E-commerce">E-commerce</option>
								 <option value="Instragram">Instragram</option>
								 <option value="Mercado libre">Mercado libre</option>

								 </select>

						 </div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
