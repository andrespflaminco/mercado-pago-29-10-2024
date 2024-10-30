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


                                <div class="col-sm-12 col-md-12">
                                        
                                <div style="width:100%;" class="btn-group  mb-4 mr-2">
                                    
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if($caja == null)
                                
                                    <b style="color:red;"> Sin caja seleccionada. </b>
                                   
                                    @else
                                    <b style="color:green;"> Caja seleccionada: # {{$caja_seleccionada->nro_caja}} </b>
                                    @endif
                                     </button>
                                                   <div class="dropdown-menu">
                                                    
                                                    @if($caja_abierta == null)
                                                    @if($caja == null)
                                                    <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Abrir caja</p>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ModalAbrirCaja()">+ NUEVA CAJA </a>
                                
                                                    @endif
                                                    @endif
                                                    <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Ultimas cajas</p>
                                                      
                                                     <div class="dropdown-divider"></div>
                                                     @foreach($ultimas_cajas as $uc)
                                                   <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ElegirCaja({{$uc->id}})">Caja # {{$uc->nro_caja}} ( {{\Carbon\Carbon::parse($uc->created_at)->format('d/m/Y')}} )</a>
                                                    @endforeach
                                                     <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="SinCaja()"> SIN CAJA </a>
                                
                                
                                                   <div class="dropdown-divider"></div>
                                                   
                                                    <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja por fecha</p>
                                                   <div class="dropdown-divider"></div>
                                                      <input type="date" wire:change="CambioCaja()" wire:model="fecha_ap"  class="form-control " >
                                                   
                                                   </div>
                                                   </div>
                                
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
								<input type="number" class="form-control text-center" wire:model="pago" wire:change="MontoPago()" >
								<div class="input-group-append">
									<span wire:click="EliminarMoneda(0)" class="input-group-text" style="height: 100% !important; background: #3B3F5C; color:white">
										<i class="fas fa-backspace"></i>
									</span>
								</div>
							</div>



							<button hidden wire:click.prevent="ACash(0)" class="btn btn-dark btn-block den">
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
                            
                            <div class="col-lg-12">
                            @if($cart->hasProducts())
							<a class="btn btn-cancel"  onclick="Confirm('','clearCart','¿SEGURO DE ELIMINAR EL CARRITO?')">Cancelar</a>
							@endif
							@if($cart->hasProducts())
                            
                            <a href="javascript:void(0)" class="btn btn-submit me-2" wire:click="AgregarNroFactura">Guardar</a>
                            
							<!----
								@if($cart->totalIva() > 0)
									<a href="javascript:void(0)" class="btn btn-submit me-2" wire:click="AgregarNroFactura">Guardar</a>

								@else
								<button wire:click.prevent="saveSale" wire:loading.attr="disabled" class="btn btn-submit me-2">Guardar</button>
								@endif
                            ----->
                            
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
