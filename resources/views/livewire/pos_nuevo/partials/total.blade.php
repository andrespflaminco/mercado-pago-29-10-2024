<div class="row" >

	<div class="col-sm-12">
		<div>
			<div class="connect-sorting">
			<!---	<h5 class="text-center mb-3">RESUMEN DE VENTA </h5> -->
				<div class="connect-sorting-content">
				    
				    @include('livewire.pos_nuevo.partials.puntos_venta')
				    
					<div class="card simple-title-task ui-sortable-handle">
					    
					     @if( $cliente != null)
                            @if( $cliente->id != 1)
                            @if($cliente->observaciones != null)
                            <div style="border: solid 1px;   border-color: #eee; margin: 10px;" class="card-body">
                            <div  class="task-header">
                            Observaciones del cliente:
                            {{$cliente->observaciones}}
                            </div>
                            </div>
                            @endif
                            @endif
                            @endif
                            
						<div class="card-body">
						    
						<div>  
						
						<!------------- CAJA --------------------------------->
						
						  <div style="width:100%;" class="btn-group  mb-4 mr-2">
                                    
                                    
                                    <button style="font-size:14px !important;" class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                     <b style="color:green;"> Caja seleccionada: # {{$nro_caja_elegida}} </b>
                                     </button>
                                     <div class="dropdown-menu">
                                                    
                                     <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Ultimas cajas</p>
                                                      
                                     <div class="dropdown-divider"></div>
                                        @foreach($ultimas_cajas as $uc)
                                        <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ElegirCaja({{$uc->id}})">Caja # {{$uc->nro_caja}} ( {{\Carbon\Carbon::parse($uc->created_at)->format('d/m/Y')}} )</a>
                                        @endforeach
                                       
                                        <div class="dropdown-divider"></div>
                                                   
                                        <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja por fecha</p>
                                        <div class="dropdown-divider"></div>
                                        <input type="date" wire:change="CambioCaja()" wire:model="fecha_ap"  class="form-control " >
                                                   
                                        </div>
                                     </div>
                                                   
						<!-------------- ESTADO DE LA VENTA --------------------->
                            
						<h6 style="border-bottom: solid 1px #eee;"><b>Estado del pedido</b></h6>
						<div>
                        	@if($estado_pedido == '')
                        	<button wire:click="selectEstado()" type="button" style=" width: 100%; margin-top: 10px; margin-bottom: 10px; !important; margin-right: 15px;" class="btn btn-dark" > Estado </button>
                            @endif
                            					
                        	@if($estado_pedido == 'Pendiente')
                        	<button wire:click="selectEstado()" type="button" style=" width: 100%; margin-top: 10px; margin-bottom: 10px; !important; margin-right: 15px;" class="btn btn-warning" > Pendiente </button>
                        	@endif
                            
                        	@if($estado_pedido == 'En proceso')
                        	<button wire:click="selectEstado()" type="button" style=" width: 100%; margin-top: 10px; margin-bottom: 10px; !important; margin-right: 15px;" class="btn btn-secondary" > En proceso </button>
                        	@endif
                            
                        	@if($estado_pedido == 'Entregado')
                        	<button type="button" wire:click="selectEstado()" style=" width: 100%; margin-top: 10px; margin-bottom: 10px; !important; margin-right: 15px;" class="btn btn-success" > Entregado </button>
                        	@endif
                            			
                        </div>
                        
                        <!-------------- /ESTADO DE LA VENTA --------------------->	

                        <!-------------- CANAL DE VENTAS --------------------->    
                        
						<h6 style="border-bottom: solid 1px #eee;"><b>Canal de venta</b></h6>
						<div wire:ignore>
    			    	<select style="font-size: 14px !important;" wire:model.lazy="canal_venta" class="form-control mb-2">
    						<option value="Mostrador">Mostrador</option>
    						<option value="E-commerce">E-commerce</option>
    						<option value="Instragram">Instragram</option>
    						<option value="Mercado libre">Mercado libre</option>
    					</select>
					    </div> 
					    
					    <!-------------- / CANAL DE VENTAS --------------------->    
					    
					     <!-------------- DESCUENTO NUEVO --------------------->
	                    
						<h6 style="border-bottom: solid 1px #eee;"><b>Descuento</b></h6>
						<div class="input-group input-group-md mb-0" style="width:100%;">
						<input type="number" id="descuento" wire:model="descuento_gral_mostrar"
						wire:keydown.enter="updateDescuentoGral($('#descuento').val() )"
						wire:change="updateDescuentoGral($('#descuento').val() )"
						class="form-control text-center" min="0" value="${{floatval($descuento_gral_mostrar)}}"
						>
						<div class="input-group-append">
						<span class="input-group-text" style="background-color: #e9ecef; color: #212529; border: 1px solid #ced4da;">
						 %
						</span>
						</div>
						</div>
						
						
						
						<!-------------- / DESCUENTO NUEVO --------------------->

						</div>
						
                        <!----------------- RESUMEN TOTALES ------------->
                        <h6 style="border-bottom: solid 1px #eee;"><b>Resumen de venta</b></h6>
                        <br>
                        
                        @if($relacion_precio_iva == 2)

                        
                        <div class="row">
                            <div class="col-7" style="font-size:12px !important;" >SUBTOTAL</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($sum_subtotal_con_iva,2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">- Descuento promociones</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;"> ${{number_format($sum_descuento_promo_con_iva,2)}}</div>

                            <!---- Aca arranca con un IVA unificado ------>
                            
                            <div class="col-7" style="font-size:12px !important;">- Descuento Gral</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;"> ${{number_format($sum_descuento * (1 + $iva_elegido),2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">+ Recargo ( {{number_format($recargo*100,2)}}%  )</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;"> ${{number_format($recargo_total * (1 + $iva_elegido),2)}} </div>
         
                            <div class="col-7" style="font-size:12px !important;">+ IVA <br> (Incluido en el precio)</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($sum_iva,2)}}</div>
                            <br>

                            <!----- si es pago parcial ------>
                            @if($pago_parcial == 0)
                            <div class="col-4"><h6 style="font-size:17px !important;"><b>TOTAL</b></h6></div>
                            <div class="col-8 text-right" style="text-align: right; font-size:17px !important;"><b>${{number_format($total,2)}}</b></div>
                            @endif
                            
                            <!----- si es pago parcial ------>
                            @if($pago_parcial == 1)
                               <div class="col-6"><h6 style="font-size:17px !important;"><b>TOTAL</b></h6></div>
                            <div class="col-6 text-right" style="text-align: right; font-size:17px !important;"><b>${{number_format($subtotal+$sum_iva+$recargo_total-$sum_descuento-$sum_descuento_promo,2)}}</b></div>
                          
                            @endif
                        </div>
                        @endif
                        
                        @if($relacion_precio_iva == 1)
                        <div class="row">
                            <div class="col-7" style="font-size:12px !important;" >SUBTOTAL</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format(($subtotal),2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">- Descuento promociones</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;"> ${{number_format($sum_descuento_promo,2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">- Descuento Gral ( {{$descuento*100}}%  )</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;"> ${{number_format($sum_descuento,2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">= SUBTOTAL C/DESC</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($subtotal-$sum_descuento_promo-$sum_descuento,2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">+ Recargo ( {{number_format($recargo*100,2)}}%  )</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($recargo_total,2)}} </div>
                            <div class="col-7" style="font-size:12px !important;">= BASE IMPONIBLE</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($subtotal-$sum_descuento_promo-$sum_descuento+$recargo_total,2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">+ IVA</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($sum_iva,2)}}</div>
                            <br>
                            <!----- si es pago parcial ------>
                            @if($pago_parcial == 0)
                            <div class="col-4"><h6 style="font-size:17px !important;"><b>TOTAL</b></h6></div>
                            <div class="col-8 text-right" style="text-align: right; font-size:17px !important;"><b>${{number_format($total,2)}}</b></div>
                            @endif
                            
                            <!----- si es pago parcial ------>
                            @if($pago_parcial == 1)
                               <div class="col-6"><h6 style="font-size:17px !important;"><b>TOTAL</b></h6></div>
                            <div class="col-6 text-right" style="text-align: right; font-size:17px !important;"><b>${{number_format($subtotal+$sum_iva+$recargo_total-$sum_descuento-$sum_descuento_promo,2)}}</b></div>
                          
                            @endif
                        </div>
                        @endif
                        
                        @if($relacion_precio_iva == 0)
                        <div class="row">
                            <div class="col-7" style="font-size:12px !important;" >SUBTOTAL</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format(($subtotal),2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">- Descuento promociones</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;"> ${{number_format($sum_descuento_promo,2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">- Descuento Gral ( {{$descuento*100}}%  )</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;"> ${{number_format($sum_descuento,2)}}</div>
                            <div class="col-7" style="font-size:12px !important;">+ Recargo ( {{$recargo*100}}%  )</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($recargo_total,2)}} </div>
                            <div class="col-7" style="font-size:12px !important;">+ IVA</div>
                            <div class="col-5 text-right" style="font-size:12px !important; text-align: right;">${{number_format($sum_iva,2)}}</div>
                            <br>
                            <!----- si es pago parcial ------>
                            @if($pago_parcial == 0)
                            <div class="col-4"><h6 style="font-size:17px !important;"><b>TOTAL</b></h6></div>
                            <div class="col-8 text-right" style="text-align: right; font-size:17px !important;"><b>${{number_format($total,2)}}</b></div>
                            @endif
                            
                            <!----- si es pago parcial ------>
                            @if($pago_parcial == 1)
                               <div class="col-6"><h6 style="font-size:17px !important;"><b>TOTAL</b></h6></div>
                            <div class="col-6 text-right" style="text-align: right; font-size:17px !important;"><b>${{number_format($subtotal+$sum_iva+$recargo_total-$sum_descuento-$sum_descuento_promo,2)}}</b></div>
                          
                            @endif
                        </div>
                        @endif
                        
				
						@if($efectivo < $total && $total > 0  && $pago_parcial == 1)
						<h4 hidden class="text-muted">Deuda: ${{number_format($change,2)}}</h4>
						@endif
						@if($efectivo >= $total && $total > 0  && $pago_parcial == 1)
						<h4 hidden class="text-muted">Cambio: ${{number_format(-1*$change,2)}}</h4>
						@endif

						@if ($efectivo>= $total && $total > 0 && $pago_parcial == 0)
						<h4 hidden class="text-muted">Cambio: ${{number_format(-1*$change,2)}}</h4>
						@endif
						
						<!---------------------- BOTONES ---------------------------------->
						
						
						@if($nro_paso == 1)
						<!------- Botones Paso 1 -------->
						
						<div class="row justify-content-between mt-5">
						<div class="col-sm-12 col-md-12 col-lg-6">
		
						<button  onclick="Confirm('','clearCart','¿ESTA SEGURO DE ELIMINAR EL CARRITO?')"
						class="btn btn-dark mtmobile" style="font-size: 14px;">
						CANCELAR
						</button>
						</div>
            			<div class="col-sm-12 col-md-12 col-lg-6">
						    
						<button class="btn btn-dark btn-md btn-block" wire:click="IrPaso2" style="font-size: 14px;"> SIGUIENTE</button>
						</div>
						
						<!------- /Botones Paso 1 -------->
                        @endif
                        
						<div class="row justify-content-between mt-5">

            			@if($nro_paso == 2)
            			<!------- Botones Paso 2 -------->
            			<div class="col-sm-12 col-md-12 col-lg-6">
					    <button style="font-size: 14px;" wire:click="IrPaso1" class="btn btn-dark mtmobile">
						ANTERIOR
						</button>
					    </div>
            			
            			
            			<div class="col-sm-12 col-md-12 col-lg-6">
						
						@if($caja_elegida != $caja_abierta)
						<button style="font-size: 14px;" onclick="ConfirmCaja('')"  wire:loading.attr="disabled" {{0 < $itemsQuantity? '' : 'disabled' }} class="btn btn-dark btn-md btn-block">
						<span wire:loading.remove>GUARDAR</span>
						<span wire:loading>GUARDANDO...</span>	
						</button>
						
						@else
						
						
						@if($es_pago_dividido == 1)
						<!--button wire:click.prevent="saveSale" wire:loading.attr="disabled" class="btn btn-dark btn-md btn-block">GUARDAR F6</button-->
						<button style="font-size: 14px;" wire:click.prevent="saveSale" wire:loading.attr="disabled" {{0 < $itemsQuantity? '' : 'disabled' }} class="btn btn-dark btn-md btn-block">
						<span wire:loading.remove>GUARDAR</span>
						<span wire:loading>GUARDANDO...</span>													
						</button>
						@endif
						
						@if($es_pago_dividido == 0)
                         
                        <!--- si el pago es mayor al total ---->
						@if(  $metodo_pago != '3' && ( ( 0 <= ($efectivo+0.01) - $total) )  )
						<button style="font-size: 14px;" wire:click.prevent="saveSale" wire:loading.attr="disabled" {{0 < $itemsQuantity? '' : 'disabled' }} class="btn btn-dark btn-md btn-block">
						<span wire:loading.remove>GUARDAR</span>
						<span wire:loading>GUARDANDO...</span>	
						</button>
						@else
						<!--- si el pago es menor al total ---->
						<button style="font-size: 14px;" wire:click.prevent="ErrorFaltaMonto" wire:loading.attr="disabled" {{0 < $itemsQuantity? '' : 'disabled' }} class="btn btn-dark btn-md btn-block">
						<span>GUARDAR</span>	
						</button>
						@endif
						
						@endif
						
						@endif
						
						</div>
						
						
						@endif
						</div>
						<!------- /Botones Paso 2 -------->
						
						
						<!---------------------- /BOTONES ---------------------------------->
						
						
							

						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
