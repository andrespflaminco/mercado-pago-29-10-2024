
                    	
            			@if($configuracion_ver == 'codigos')
						<div class="card-body">
						
						<div class="row mb-4">
						    <h5 style="font-style: bold;">CONFIGURACION DE CODIGOS </h5>
							<div class="col-lg-6 col-sm-12 col-12">
								<div class="form-group">
									<label>Tipos de codigos utilizados</label>
									<select class="form-control" wire:model="configuracion_codigos">
									    <option value="0">Solo Codigos no pesables</option>
									    <option value="1">Codigos pesables y no pesables</option>
									</select>
								</div>
							</div>
						</div>
						
						@if($configuracion_codigos == 1)
						
						<div class="row">
						    <h5 style="font-style: bold;">ESTRUCTURA DEL CODIGO </h5>
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
									<label>Digitos del Prefijo de codigo</label>
									<select class="form-control" wire:model="numeros_prefijo">
									    <option value="1">1 digito</option>
									    <option value="2">2 digitos</option>
									</select>
									
								</div>
							</div>
							
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
									<label>Digitos del codigo</label>
									<select class="form-control" wire:model="numeros_codigo">
									    <option value="4">4 digitos</option>
									    <option value="5">5 digitos</option>
									    <option value="6">6 digitos</option>
									</select>
								</div>
							</div>
							
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
									<label>Digitos del Peso</label>
									<select class="form-control" wire:model="numeros_peso">
									    <option value="4">4 digitos</option>
									    <option value="5">5 digitos</option>
									    <option value="6">6 digitos</option>
									</select>
								</div>
							</div>
							
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
									<label>Total digitos</label>
									<h5 style="padding: .375rem .75rem; {{($numeros_prefijo + $numeros_codigo + $numeros_peso) == 12 ? 'color: green;' : 'color: red;'}}" >{{$numeros_prefijo + $numeros_codigo + $numeros_peso}}</h5>
								<text style="font-size:11px;">En total el codigo de barras para productos pesables debe contener 12 digitos.</text>
								</div>
							</div>
						</div>
							

						<div class="row">
						    <h5 style="font-style: bold;">PREFIJO DE CODIGO PESABLES </h5>
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
									<label></label>
									<input class="form-control" type="number" maxleght="{{$numeros_prefijo}}" wire:model="prefijo_pesables">
								</div>
							</div>
							
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
								
								</div>
							</div>
							
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
									
								</div>
							</div>
							
							<div class="col-lg-3 col-sm-12 col-12">
								<div class="form-group">
								</div>
							</div>
							
						</div>
						
						
						<div hidden>
						<br><br>
						COMBINACIONES DE C.BARRA = 2-5-5 / 2-4-6 / 1-5-6/ 2-6-4    
						<br><br>						    
						</div>

						@endif
						
						
						<div class="col-lg-12">
                    		<a class="btn btn-cancel" wire:click.prevent="CerrarModalConfiguracion()"  data-bs-dismiss="modal">Cancelar</a>
					     	<a class="btn btn-submit me-2" wire:click="UpdateConfiguracionCodigos()" >Guardar</a>
						</div>
					</div>
					    @endif
					    @if($configuracion_ver == 'precios')
    					<div class="card-body">
    
    						<div class="row mb-4">
    						    <h5 style="font-style: bold;">CONFIGURACION DE PRECIOS </h5>
    							<div class="col-lg-6 col-sm-12 col-12">
    								<div class="form-group">
    									<label>Caracteristicas del precio interno (precio de venta a las sucursales)</label>
    									<select class="form-control" wire:model="configuracion_precio_interno">
    									    <option value="0">El precio interno es distinto al costo</option>
    									    <option value="1">El precio interno es igual al costo</option>
    									</select>
    								</div>
    							</div>
    						</div>
    						
    						<div class="col-lg-12">
                        		<a class="btn btn-cancel" wire:click.prevent="CerrarModalConfiguracion()"  data-bs-dismiss="modal">Cancelar</a>
    					     	<a class="btn btn-submit me-2" onclick="ConfirmConfiguracion({{$configuracion_precio_interno}})" >Guardar</a>
    						</div>
    					</div>
    					@endif
    					
    					@if($configuracion_ver == 'stock')
    					<div class="card-body">
    
    						<div class="row mb-4">
    						    <h5 style="font-style: bold;">VISIBILIDAD DE STOCKS </h5>
    							<div class="col-lg-6 col-sm-12 col-12">
    								<div class="form-group">
    									<label>En una sucursal, mostrar los stocks de otras sucursales?</label>
    									<select class="form-control" wire:model="muestra_stock_otras_sucursales">
    									    <option value="1">Si</option>
    									    <option value="0">Solo mostrar el stock de esa sucursal</option>
    									</select>
    								</div>
    							</div>
    							<div class="col-lg-6 col-sm-12 col-12">
    							</div>
    						
    						<div class="col-lg-6 col-sm-12 col-12">
    								<div class="form-group">
    									<label>En una sucursal, mostrar el stock que hay en la casa central?</label>
    									<select class="form-control" wire:model="muestra_stock_casa_central">
    									    <option value="1">Si</option>
    									    <option value="0">No</option>
    									</select>
    								</div>
    							</div>
    							<div class="col-lg-6 col-sm-12 col-12">
    							</div>
    							
    							
    							
    						    <h5 style="font-style: bold;">CANTIDAD DE DECIMALES EN LAS CANTIDADES </h5>
    							<div class="col-lg-6 col-sm-12 col-12">
    								<div class="form-group">
    									<label>Decimales  a usar en las cantidades para unidades</label>
    									<select class="form-control" wire:model="digitos_cantidad_unidades">
    									    <option value="0">0</option>
    									    <option value="1">1</option>
    									    <option value="2">2</option>
    									    <option value="3">3</option>
    									</select>
    								</div>
    							</div>
    							<div class="col-lg-6 col-sm-12 col-12">
    							</div>
    							<div class="col-lg-6 col-sm-12 col-12">
    								<div class="form-group">
    									<label>Decimales  a usar en las cantidades para KG</label>
    									<select class="form-control" wire:model="digitos_cantidad_kg">
    									    <option value="0">0</option>
    									    <option value="1">1</option>
    									    <option value="2">2</option>
    									    <option value="3">3</option>
    									</select>
    								</div>
    							</div>
    							
    						</div>
    						
    						<div class="col-lg-12">
                        		<a class="btn btn-cancel" wire:click.prevent="CerrarModalConfiguracion()"  data-bs-dismiss="modal">Cancelar</a>
    					     	<a class="btn btn-submit me-2" wire:click="UpdateConfiguracionDigitosStock()" >Guardar</a>
    						</div>
    					</div>
    					@endif
    					
    					@if($configuracion_ver == 'acciones_masivas')
    					<div class="card-body">
    						<div class="col-lg-12">
                        		<a class="btn btn-danger" wire:click.prevent="EliminarTodos()"  data-bs-dismiss="modal">Eliminar Todos los productos</a>
    						</div>
    					</div>
    
    					@endif
