<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Historico de Actualizaciones de precios</h4>
							<h6>Muestra cambios en los precios a lo largo del tiempo</h6>
						</div>
					
					</div>
					
					<!-- /product list -->
					<div class="card">
					
						<div class="card-body">
							<div class="row mb-2">
                			<div class="col-lg-3 col-md-4 col-sm-12">
                								        
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
                				</div>
                				@if(!empty($query_product))
                					<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
                						<div style="position:absolute; z-index: 999 !important; height: 250px; width: 300px; overflow: auto;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                							@if(!empty($products_s))
                								@foreach($products_s as $i => $product)
                									<div class="btn-group" role="group" aria-label="Basic example">
                										<button value="{{$product['id']}}" 
                											id="code{{$product['id']}}"  
                											wire:click.prevent="$emit('buscar', $('#code{{$product['id']}}').val())" 
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
    			            </div>  
							
							<div class="col-lg-3 col-md-4 col-sm-12">
							<label style="font-size: 11px !important;"><b>Fecha</b></label><br>
							<input class="form-control" style="height: 35px; width:100%;" type="text" id="date-range-picker" name="date_range" />
							
							</div>
							<div class="col-lg-3 col-md-4 col-sm-12">
							<label style="font-size: 11px !important;"><b>Filtros</b></label><br>
							<button class="btn btn-light" wire:click="LimpiarFiltros">Limpiar filtros</button>
							
							</div>
	                        </div>
							
							
					
							<text>Detalle para  @if($cod_producto == null) todos los productos @else el producto Cod. {{$cod_producto}} - {{$nombre_producto}} @endif </b> desde {{$from_formateado}} hasta {{$to_formateado}}  <b> </b></text>
							
							<div class="table-responsive">
								<table class="table">
									<thead style=" background: white !important;  border: 2.2px solid #eee !important;">
										<tr>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;" class="text-center">FECHA</th>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;" class="text-center">CODIGO</th>
											<th style=" background: white !important;  border: 2.2px solid #eee !important;" class="text-center">PRODUCTO</th>
											<th style="text-align:center !important; background: white !important;  border: 2.2px solid #eee !important;" class="text-center">PRECIO ANTERIOR</th>
											<th style="text-align:center !important; background: white !important;  border: 2.2px solid #eee !important;" class="text-center">PRECIO NUEVO</th>
										
											
										</tr>
									</thead>
									<tbody style="border: solid 2px #eee!important;">

									    @foreach($movimientos as $historico)
										<tr>
            						    <td  class="text-center">
                                                {{ \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y') }}
                                        </td>
                                        </td>
                                        <td  class="text-center">
                                          {{$historico->barcode }}
                                        </td>
                                        <td  class="text-center">
                                          {{$historico->nombre_producto }}
                                        </td>
                                        <td  class="text-center">
                                           $     {{ round($historico->precio_viejo,2) }}
                                        </td>
                                        <td class="text-center">
                                           $    {{ round($historico->precio_nuevo,2) }}
                                        </td>
                               			</tr>
										@endforeach
									</tbody>
								</table>
							</div>

						</div>
					</div>
				
					<!-- /product list -->
			        @include('livewire.historico-precios.variaciones')
					</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){

		window.livewire.on('abrir-modal-variaciones', msg => {
			$('#Variaciones').modal('show')
		});
		
		window.livewire.on('cerrar-modal-variaciones', msg => {
			$('#Variaciones').modal('hide')
		});
		
    });
</script>
    