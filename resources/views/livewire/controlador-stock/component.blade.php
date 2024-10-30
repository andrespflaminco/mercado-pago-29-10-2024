<div >	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Recuento fisico de stock</h4>
							<h6>Agregue las cantidades de stock de cada producto</h6>
						</div>
					
					</div>
					
                    
					<!-- /product list -->
					<div class="card">

						<div class="card-body">
							<div class="row mb-2">
						
						    @if($cambio_hecho == false)
						    
							@if($cod_producto == null)
                			<div class="col-lg-5 col-md-4 col-sm-12">
                								        
                 			    <label style="font-size: 11px !important;"><b>Buscar codigo</b></label>
                				<div style="margin-bottom: 0 !important;" class="input-group mb-4">
                					<div class="input-group-prepend">
                						<span style="height: 100% !important;" class="input-group-text input-gp">
                							<i class="fas fa-clipboard-list"></i>
                						</span>
                					</div>
                					<input
                						style="font-size:14px !important;"
                						type="text"
                						class="form-control"
                						placeholder="Ingrese el codigo del producto"
                						wire:model="query_product"
                						wire:keydown.escape="resetProduct"
                						wire:keydown.tab="resetProduct"
                						wire:keydown.enter="Buscar"
                					 />
                			    <button style="margin-left: 15px;" class="btn btn-primary" wire:click="Buscar">Buscar</button>
                				</div>
                		       
                				
                				@if(session('status'))
                                <br>
                                <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                                @endif
                				
                				            </div>  
                			<div class="col-lg-7 col-md-4 col-sm-12"></div>  
                			@endif
                				            
                			@if($cod_producto != null)
							<div class="col-lg-12 col-md-12 col-sm-12 mt-3 mb-0">
							<div class="card mb-0">
							    <div class="card-body">
							     <h6>Codigo: {{$cod_producto}}</h6>
							     <h6>Producto: {{$nombre_producto}} </h6>
							    </div>
							</div>
							</div>
							@endif
							
							@if($cod_producto != null)
							<div class="col-lg-12 col-md-12 col-sm-12 mt-3 mb-0">
							<div class="card mb-0">
							<div class="card-body">
							<h6>Stock: <input type="number" min="0" wire:model="stock" class="form-control"></h6>
							<p hidden>Stock anterior: {{$stock_anterior}}</p>
							</div>
							</div>
							</div>
							
							
							<div class="col-lg-4 col-md-12 col-sm-12 mt-3 mb-0">
							<button class="btn btn-success text-center" wire:click="Guardar">GUARDAR</button>    
							<button class="btn btn-danger text-center" wire:click="LimpiarBusqueda">CANCELAR</button>
							</div>
							
						
							
						    
							@endif
							
							@else
							<div class="col-lg-12 col-md-12 col-sm-12 mt-3 mb-0">
							<br><br>
							
							@if($msg == "text-success")
							
							<h3 class="{{$msg}}" style="background: #efffef; padding: 25px 5px;">Cambios guardados con exito</h3>
							<h1 style="padding:5px;">Stock nuevo: {{$stock_nuevo}}</h1>
							@endif
							
							@if($msg == "text-danger")
							<h1 class="{{$msg}}" style="background: #ffd3d7; padding: 25px 5px;" >NO SE PUDIERON REALIZAR LOS CAMBIOS. INTENTE NUEVAMENTE</h1>
							@endif
							
							<br><br>
							<a style="margin-left:5px;" class="btn btn-primary" href="{{ url('controlador-stock') }}">Volver</a>
							</div>
							
							@endif
							
				
							</div>
							
							
						
						</div>
					</div>
					
					
				
					<!-- /product list -->
			
					</div>
			