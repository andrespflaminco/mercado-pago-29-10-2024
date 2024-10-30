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

									<input type="hidden" id="hiddenTotal" value="{{$total}}">
								</div>
								<div>
								<!---	<h4 class="mt-3">Articulos: {{$itemsQuantity}}</h4> --->
								</div>
								<label>Tipo de pago</label>

								<select  wire:model='tipo_pago' wire:change='TipoPago($event.target.value)'  class="form-control">
										<option value="1">Efectivo</option>
										<option hidden value="3">Cheque</option>
										<option value="2">Pago dividido</option>
										@foreach($tipos_pago as $tipos)
										<option value="{{$tipos->id}}">{{$tipos->nombre}}</option>
										@endforeach
										<option value="OTRO" class="btn btn-dark">Agregar otro banco/plataforma</option>

									</select>

									@if($tipo_pago != 1 && $tipo_pago !=2 && $tipo_pago !=3)

								<label>Forma de pago</label>

									<select  wire:model='metodo_pago' 	wire:change='MetodoPago($event.target.value)' class="form-control">
										<option disabled value="Elegir">Elegir</option>
											@foreach($metodos as $metodo_pago)
											<option value="{{$metodo_pago->id}}">{{$metodo_pago->nombre}}</option>
											@endforeach
											<option hidden  value="1">Efectivo</option>
									    	<option hidden value="2">Pago dividido</option>
												<option value="OTRO" class="btn btn-dark" >Agregar otro medio de pago</option>
										</select>
										@else

										@endif



							<input style="margin:2.5%;" type="checkbox" wire:click="CheckPagoParcial({{$pago_parcial}})" {{$check}}>    Acepta pago parcial


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
								class="form-control text-center"
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

															<div class="input-group input-group-md mb-3" style="width:60%;">
																<div class="input-group-prepend">
																	<span class="input-group-text input-gp hideonsm" style="background: #3B3F5C; color:#3B3F5C; font-size: 12px; padding:5px;"> Descuento
																	</span>
																</div>
																<input type="number" id="descuento" wire:model="descuento_gral_mostrar"
																wire:keydown.enter="updateDescuentoGral($('#descuento').val() )"
																wire:change="updateDescuentoGral($('#descuento').val() )"
																class="form-control text-center" min="0" value="${{floatval($descuento_gral_mostrar)}}"
																>
																<div class="input-group-append">
																	<span class="input-group-text" style="background: #3B3F5C; color:white">
																	 %
																	</span>
																</div>
															</div>

								<p>SUBTOTAL: ${{number_format(($total-$sum_iva+$sum_descuento),2)}}</p>
						

								<p>- Descuento: ${{number_format($sum_descuento,2)}}</p>
								<p>+ IVA: ${{number_format($sum_iva,2)}}</p>
								<p>+ Recargo: ${{number_format($recargo_total,2)}} ( {{$recargo*100}}%  )</p>
								<p hidden>Descuento: ${{number_format($descuento_total,2)}}</p>

								<h3 >A COBRAR: ${{number_format($efectivo+$recargo_total-$descuento_total,2)}}</h3>

								@if($efectivo < $total && $total > 0  && $pago_parcial == 1)
								<h4 class="text-muted">Deuda: ${{number_format($change,2)}}</h4>
								@endif
								@if($efectivo >= $total && $total > 0  && $pago_parcial == 1)
								<h4 class="text-muted">Cambio: ${{number_format(-1*$change,2)}}</h4>
								@endif


								@if ($efectivo>= $total && $total > 0 && $pago_parcial == 0)
								<h4 class="text-muted">Cambio: ${{number_format(-1*$change,2)}}</h4>
								@endif
								
								<!---- ACA ENVIO ------>
                             <input type="checkbox" id="myCheck1" wire:click="EnviosCliente()" wire:model="check_envio_cliente" {{$checked_envio}}> Envio a domicilio del cliente <br>
							 <input type="checkbox" id="myCheck2" wire:click="Envios()" wire:model="check_envio" {{$checked_envio}}> Envio a otro domicilio 
							  
							 
							 <div style="display:{{$envio_visible}}">
							
                            <label>Nombre de quien recibe</label>
                            <input class="form-control" wire:model.lazy="nombre_envio">
                            <label>Telefono</label>
                            <input class="form-control" wire:model.lazy="telefono_envio">
                            <label>Direccion</label>
                            <input class="form-control" wire:model.lazy="direccion_envio">
                            <label>Ciudad</label>
                            <input class="form-control" wire:model.lazy="ciudad_envio">
                            <label>Provincia</label>
                            <select class="form-control" wire:model.lazy="provincia_envio">
                                <option value="Elegir">Elegir</option>
                                @foreach($provincias as $prov)
                                <option value="{{$prov->id}}">{{$prov->provincia}}</option>
                                @endforeach
                            </select>


                            </div>
								
								

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
								@if($efectivo > 0 && $metodo_pago == '3')
								<button wire:click.prevent="Cheques" wire:loading.attr="disabled" class="btn btn-dark btn-md btn-block">
									GUARDAR
								</button>
								@endif
								@if($pago_parcial == 1 && $metodo_pago != '3')
								<!--button wire:click.prevent="saveSale" wire:loading.attr="disabled" class="btn btn-dark btn-md btn-block">GUARDAR F6</button-->
								<button wire:click.prevent="saveSale" wire:loading.attr="disabled" class="btn btn-dark btn-md btn-block">
									<span wire:loading.remove>GUARDAR F6</span>
    								<span wire:loading>GUARDANDO...</span>													
								</button>
								@endif
								@if (  $metodo_pago != '3' && ($efectivo+0.01)>= ($total - $sum_descuento)	&& ($total - $sum_descuento)  > 0	&& $pago_parcial == 0)
								<button wire:click.prevent="saveSale" wire:loading.attr="disabled" class="btn btn-dark btn-md btn-block">
									<span wire:loading.remove>GUARDAR F6</span>
    								<span wire:loading>GUARDANDO...</span>	
								</button>
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
