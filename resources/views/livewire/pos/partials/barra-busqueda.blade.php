<div id="connect-sorting" class="connect-sorting">

            @include('livewire.pos.partials.barra-busqueda-cliente')
            <!------------ Barra de abajo -------------------------->
            
            <div class="row">
			<div class="col-lg-3 col-md-4 col-sm-12 d-none d-sm-block">
			    <label style="font-size: 11px !important;"><b>Cod producto</b></label>
            	<div class="input-group mb-0">				
					<input id="code" type="text"
						    wire:keydown.enter.prevent="$emit('scan-code', $('#code').val())"
							class="form-control search-form-control  ml-lg-auto"
							placeholder="Cod." style="place">
			
                    <div class="input-group-prepend">
                        <span style="height: 100% !important;" wire:click="$emit('scan-code', $('#code').val())" class="input-group-text input-gp">
                            <i class="fas fa-search"></i>
                        </span> 
                    </div>

                    
				</div>
			</div>
            
 			<div class="col-lg-6 col-md-4 col-sm-12">
 			    <label style="font-size: 11px !important;"><b>Buscar por nombre</b></label>
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
						placeholder="Seleccione un producto"
						wire:model="query_product"
						wire:keydown.escape="resetProduct"
						wire:keydown.tab="resetProduct"
						wire:keydown.enter="selectProduct"
					 />
                    
                    @can('agregar producto nuevo (modulo venta)')
					<div style="margin-left: 15px !important;" class="input-group-append">
							<button class="btn btn-dark" wire:click="SetBarcodeModal()">+</button>
					</div>
					@endcan
					
					 
					 
				</div>
				@if(!empty($query_product))
					<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
						<div style="position:absolute; z-index: 999 !important; height: 250px; width: 300px; overflow: auto;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
							@if(!empty($products_s))
								@foreach($products_s as $i => $product)
									<div class="btn-group" role="group" aria-label="Basic example">
										<button value="{{$product['barcode']}}" 
											id="code{{$product['barcode']}}"  
											wire:click="BuscarPorBuscador('{{$product['barcode']}}')" 
											wire:click.lazy="selectProduct"
											class="btn btn-light" 
											title="Click en el producto">{{ $product['barcode'] }} - {{ $product['name'] }}
										</button>
									    <button hidden value="{{$product['barcode']}}" id="info{{$product['barcode']}}"  wire:click="$emit('info-producto', $('#info{{$product['barcode']}}').val())" style="max-width:50px;" type="button" class="btn btn-dark">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
										</button>
									</div>
								@endforeach
							@else
								<div style="  padding: 10px;  text-align: center;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
								No hay resultados
								</div>
							@endif

							@can('product_create')
								<a href="javascript:void(0)"  hidden style=" position: fixed;   width: 300px;  margin-top: 250px;" class="btn btn-dark" data-toggle="modal" data-target="#ModalProductos" >Agregar otro producto</a>
							@endcan
						</div>
				@endif

				
				
				@if(session('status'))
                <br>
                <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                @endif
				</div>  
				
			<div class="col-lg-3 col-md-4 col-sm-12"><button  wire:click="VerCatalogo" style="font-size:14px; margin-top:20px; width:100%; background:white; border:solid 1px #c8c8c8;" class="btn text-center">Ver catalogo</button></div>
            </div>	        

	        
				       
            <!-------------------------------------->

</div>