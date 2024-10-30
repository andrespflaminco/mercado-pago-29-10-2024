
        <!--------- INGRESOS VS EGRESOS ------------->
		
        <div class="row">
        		
				@include('livewire.dashboard.contador-stock')
        </div>       
        
        <!--------- / INGRESOS VS EGRESOS ------------->
		
 <div class="row">
            <div class="col-12">
                <div class="card mb-3">
						<div class="card-body">
							<h4 class="card-title">Stock valuado</h4>
							
				            <input type="text" wire:model="search_tabla_stock" placeholder="Buscar por nombre o código de producto..." class="form-control mb-3" />

							<div style="height: 350px;" class="table-responsive dataview mb-3">
								<table class="table">
									<thead>
										<tr>
										    <th>Codigo</th>
										    <th>Producto</th>
										    <th>Cantidad</th>
										    <th>Unidad medida</th>
										    <th>Costo Total Stockeado</th>
										    <th>Valor Total Stockeado</th>
										</tr>
									</thead>
									<tbody>
									        @foreach($tabla_stock as $item)
                                            <tr>
                                                <td>{{ $item->barcode }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>@if($item->unidad_medida == 1) {{ $item->STOCK }} @endif  @if($item->unidad_medida == 9) {{ number_format($item->STOCK,0) }} @endif</td>
                                                <td>@if($item->unidad_medida == 1) KG @endif  @if($item->unidad_medida == 9) UNIDAD @endif</td>
                                                <td>$ {{ number_format($item->STOCK * $item->COSTO,2,",",".") }}</td>
                                                <td>$ {{ number_format($item->STOCK * $item->PRECIO,2,",",".") }}</td>
                                            </tr>
                                            @endforeach    
									</tbody>
									<tfoot></tfoot>
								</table>
							</div>
							{{ $tabla_stock->links() }} <!-- Esto generará los enlaces de paginación -->
						</div>
					</div>
					
            </div>
        </div>  
        
        
        <div hidden class="row mt-3">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
							<h4 class="card-title">Productos con stock por debajo del stock minimo</h4>
							
							<input type="text" wire:model="search_tabla_stock_minimo" placeholder="Buscar por nombre o código de producto..." class="form-control mb-3" />
							<div style="height: 350px;" class="table-responsive dataview mb-3">
								<table class="table">
									<thead>
										<tr>
										    <th>Codigo</th>
										    <th>Producto</th>
										    <th>Unidades en stock</th>
										    <th>Minimo</th>
										</tr>
									</thead>
									<tbody>
									        @foreach($tabla_stock_minimo as $item)
                                            <tr>
                                                <td>{{ $item->barcode }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->STOCK }}</td>
                                                <td>{{ $item->alerts }}</td>
                                            </tr>
                                            @endforeach    
									</tbody>
									<tfoot></tfoot>
								</table>
							</div>
						</div>
					</div>
            </div>
        </div>   
        
        
        