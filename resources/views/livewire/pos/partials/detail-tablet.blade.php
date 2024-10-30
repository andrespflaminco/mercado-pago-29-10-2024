
<div class="connect-sorting">
<div class="d-flex justify-content-between">
 <div><p>Lista de precios: {{$nombre_lista_precios}}</p></div>
 <div>
     <a style="color:black !important;" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">IVA {{$iva_elegido * 100}} %  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg> </a>
   
								<div class="dropdown-menu">
										<button  wire:click="UpdateIvaGral(0)"   class="dropdown-item">Sin IVA</button>
										<button  wire:click="UpdateIvaGral(0.105)"  class="dropdown-item">10,5%</button>
										<button  wire:click="UpdateIvaGral(0.21)"   class="dropdown-item">21%</button>
										<button  wire:click="UpdateIvaGral(0.27)"   class="dropdown-item">27%</button>
								</div>
</div>
</div>
 

<div class="connect-sorting-content">
	<div class="card simple-title-task ui-sortable-handle">
		<div class="card-body" style="padding:0px;">

		@if($itemsQuantity > 0)
		<div class="table-responsive">
			<table class="table">
			    <thead>
					<tr>
						<th  style="width:17%;">CODIGO</th>
						<th style="width:17%;">NOMBRE</th>
						<th>PRECIO</th>
						<th style="width:15%;">CANT</th>
						<th hidden style="width:15%;">DESC</th>
						<th hidden style="width:20%;">IVA</th>
						<th>TOTAL</th>
						<th style="width:30%;">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					@foreach($cart as $item)
					<tr>
						<td>{{$item->attributes['barcode']}}</td>
						<td>{{$item->name}}</td>
						<td>
							<div style="display:flex;">
								<h6 style="margin-bottom: 0; margin-top:2px; ">$</h6>

							@if($item->attributes['relacion_precio_iva'] == 1)

								<input class="boton-precio" type="number" id="p{{$item->id}}"
								wire:change="updatePrice('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#p' + '{{$item->id}}').val() )"
								value="{{$item->price }}">

								@else

								<input class="boton-precio" type="number" id="p{{$item->id}}"
									wire:change="updatePrice('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#p' + '{{$item->id}}').val() )"
								value="{{$item->price  * (1+$item->attributes['iva'] )  }}">

								@endif
							</div>

						</td>
						<td>
                            <div class="row">
							<input type="number" id="r{{$item->id}}"
							wire:change="updateQty('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#r' + '{{$item->id}}').val() )"
							style="font-size: 1rem!important"
							class="botones1"
							value="{{$item->quantity}}"
							>
							<br>
							
							<div>
							    <button  wire:click.prevent="decreaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button  wire:click.prevent="increaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
							</div>
							
							
							</div>
							
							Stock: {{$item->attributes['stock']}} un.
						
							<input hidden id="q{{$item->id}}" value="{{$item->quantity}}">
							
							
						</td>
						<td hidden class="table-th text-center">

							<div style="width:100px;" class="input-group mb-0">
							  <input type="text" id="desc{{$item->id}}" value="{{$item->attributes['descuento']}}"
								wire:keydown.enter="updateDescuento('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#desc' + '{{$item->id}}').val() )"
								wire:change="updateDescuento('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#desc' + '{{$item->id}}').val() )" class="form-control" >
							  <div class="input-group-append">
							    <span class="input-group-text">%</span>
							  </div>
							</div>

						</td>
						<td hidden class="table-th text-center">

							@if($item->attributes['relacion_precio_iva'] == 1)


						<div class="btn-group mb-4 mr-2">
								<button class="btn btn-outline-dark btn-md iva_boton dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										{{$item->attributes['iva']*100}} % <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
								</button>
								<div class="dropdown-menu">
										<button  id="iva{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#iva' + '{{$item->id}}').val() )"  value="0" class="dropdown-item">Sin IVA</button>
										<button id="ivaprimero{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#ivaprimero' + '{{$item->id}}').val() )"  value="0.105" class="dropdown-item">10,5%</button>
										<button id="ivasegundo{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#ivasegundo' + '{{$item->id}}').val() )"  value="0.21" class="dropdown-item">21%</button>
										<button id="ivatercero{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#ivatercero' + '{{$item->id}}').val() )"  value="0.27" class="dropdown-item">27%</button>
								</div>
						</div>

								@endif
								@if($item->attributes['relacion_precio_iva'] == 2)

								IVA Incluido <br>
								({{number_format($item->attributes['iva']*100,1)}} %)



								@endif
                                
                                @if($item->attributes['relacion_precio_iva'] == 0)
								-
								@endif






						</td>

						<td hidden class="text-center">
							<h6>
								${{number_format(($item->price * (1 - ($item->attributes['descuento']/100) ) *  $item->quantity) + ($item->attributes['iva']*$item->price*$item->quantity * (1 - ($item->attributes['descuento']/100) ) ),2)}}
							</h6>
						</td>


						<td>
 

							<button hidden wire:click.prevent="decreaseQty({{$item->id}}, {{$item->attributes['product_id']}} ,{{$item->attributes['sucursal_id']}} )" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button hidden wire:click.prevent="increaseQty({{$item->id}}, {{$item->attributes['product_id']}} ,{{$item->attributes['sucursal_id']}} )" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
							<button wire:click.prevent="comentario('{{$item->id}}')" class="btn btn-dark btn-sm">
								<i class="fas fa-list"></i>
							</button>
							<button onclick="Confirm('{{$item->id}}', 'removeItem', '多CONFIRMAS ELIMINAR EL REGISTRO?')" class="btn btn-dark btn-sm">
								<i class="fas fa-trash-alt"></i>
							</button>

						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@else
		<h5 class="text-center text-muted">Agrega productos a la venta</h5>
		@endif
<!--
		<div wire:loading.inline wire:target="saveSale">
			<h4 class="text-danger text-center">Guardando Venta...</h4>
		</div>
	-->



		</div>
		</div>
		</div>
		</div>
		