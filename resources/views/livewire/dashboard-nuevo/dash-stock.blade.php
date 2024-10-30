
        <!--------- INGRESOS VS EGRESOS ------------->
		
        <div class="row">
        		
				@include('livewire.dashboard-nuevo.contador-stock')
        </div>       
        
        <!--------- / INGRESOS VS EGRESOS ------------->
		
 <div class="row">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
							<h4 class="card-title">Stock valuado</h4>
							<div style="height: 350px;" class="table-responsive dataview">
								<table class="table">
									<thead>
										<tr>
										    <th>Codigo</th>
										    <th>Producto</th>
										    <th>Unidades</th>
										    <th>Costo</th>
										    <th>Valor</th>
										</tr>
									</thead>
									<tbody>
									        @foreach($tabla_stock as $item)
                                            <tr>
                                                <td>{{ $item->barcode }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->STOCK }}</td>
                                                <td>$ {{ number_format($item->STOCK * $item->COSTO,2,",",".") }}</td>
                                                <td>$ {{ number_format($item->STOCK * $item->PRECIO,2,",",".") }}</td>
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
        
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
							<h4 class="card-title">Productos con stock por debajo del stock minimo</h4>
							<div style="height: 350px;" class="table-responsive dataview">
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
        
        
        