					           
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
            								placeholder="Buscar proveedor"
            								wire:model="query"
            								wire:keydown.escape="resetCliente"
            								wire:keydown.tab="resetCliente"
            								
            						/>
            						<div class="input-group-prepend"></div>
            					</div>
                    			@if(!empty($query))
                    			<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
                    			<div style="position:absolute; z-index: 999 !important; max-height: 250px; width: 400px; overflow: auto;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                    			    	@if(!empty($contacts))
                    								@foreach($contacts as $i => $contact)
                    									<a  href="javascript:void(0)"
                    										wire:click="selectProveedor({{$contact['id']}})"
                    										class="btn btn-light" 
                    										title="Seleccione un cliente">{{ $contact['nombre'] }}
                    										</a>
                    								@endforeach												
                    									<a hidden href="javascript:void(0)"  class="btn btn-light" wire:click="BuscarClienteAFIP" >
                    
                    									<i class="fas fa-search"></i> VER MAS </a>
                    									<a hidden href="javascript:void(0)"   class="btn btn-dark" data-toggle="modal" data-target="#ModalCliente" >Ver mas</a>
                    								@else
                    
                    								@endif
                    							</div>
                    				
                    			@endif
                    			
					            </div>