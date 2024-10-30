            	<div class="row">				
                <!-------------- CLIENTES ----------------------------->
                <div class="col-lg-8 col-md-8 col-sm-11 d-none d-sm-block  ">
                <label style="font-size: 11px !important;"><b>Cliente</b> <button hidden wire:click="CalcularTotales">.</button></label>
					           
			    <div class="mb-3">
					                
                        		<div style="margin-bottom: 0 !important;" class="input-group mb-4">
            						<div class="input-group-prepend">
            							<span style="height: 100% !important;" class="input-group-text input-gp">
            								<i class="fas fa-users"></i>
            							</span>
            						</div>
            						<input
            								style="font-size:14px !important;"
            								type="text"
            								class="form-control"
            								placeholder="Buscar cliente o cuit"
            								wire:model="query"
            								wire:keydown.escape="resetCliente"
            								wire:keydown.tab="resetCliente"
            								wire:keydown.enter="selectContact"
            								
            								
            								
            								
            						/>
            						<div class="input-group-prepend"></div>
            					</div>
                    			@if(!empty($query))
                    			<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
                    			<div style="position:absolute; z-index: 999 !important; max-height: 250px; width: 400px; overflow: auto;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                    			    	@if(!empty($contacts))
                    								@foreach($contacts as $i => $contact)
                    								
                    								
                    								@if(Auth::user()->sucursal == 0)
                    									<a  href="javascript:void(0)"
                    										wire:click="selectContact({{$contact['id']}})"
                    										class="btn btn-light" 
                    										title="Seleccione un cliente">{{ $contact['nombre'] }}
                    									</a>
                    								@else
                    								
                    								@if($contact['sucursal_id'] == 0)
                    									<a  href="javascript:void(0)"
                    										wire:click="selectContact({{$contact['id']}})"
                    										class="btn btn-light" 
                    										title="Seleccione un cliente">{{ $contact['nombre'] }}
                    									</a>
                    								
                    								@endif
                    								@endif
                    								@endforeach												
                    									<a href="javascript:void(0)"  class="btn btn-light" wire:click="BuscarClienteAFIP" >
                    
                    									<i class="fas fa-search"></i> Buscar CUIT en Afip</a>
                    									<a hidden href="javascript:void(0)"   class="btn btn-dark" data-toggle="modal" data-target="#ModalCliente" >+ Agregar otro cliente</a>
                    								@else
                    
                    								@endif
                    							</div>
                    				
                    			@endif
                    			
					            </div>
                </div>        
               <!-------------- / CLIENTES --------------------------->
               
               <!-------------- AGREGAR CLIENTES ----------------------------->
               <div class="col-lg-1 col-md-1 col-sm-1 d-none d-sm-block  ">
               <button class="btn btn-dark" style="margin-top: 19px;" wire:click="ModalAgregarCliente">+</button>
               </div>
               <!-------------- / AGREGAR CLIENTES ----------------------------->
               
               
               <div class="col-lg-3 col-md-3 col-sm-12 d-none d-sm-block  ">
                   <label style="font-size: 11px !important;"><b>Vendedor</b></label>
    			<div style="margin-bottom: 0 !important;" class="input-group mb-4">
    				<div class="input-group-prepend">
    					<span style="height: 100%;" class="input-group-text input-gp">
    						<i class="fas fa-user"></i>
    					</span>
    				</div>
    				<select  style="font-size:14px !important;" wire:model.lazy='usuario_activo' class="form-control">
    					@foreach($user as $usuario)
    						<option style="font-size:14px !important;" value="{{$usuario->id}}">{{$usuario->name}}</option>
    					@endforeach
    				</select>
    			</div>
    		</div>
    		</div>