<style media="screen">
	.boton-precio:hover {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.botones1 {
		height: auto;
		border: 1px solid #bfc9d4;
		color: #3b3f5c;
		font-size: 15px;
		text-align: center;
		letter-spacing: 1px;
		max-width: 90px;
		padding: 0.5rem 0.5rem;
		border-radius: 6px;
	}

	.botones2 {
		height: auto;
		border: 1px solid #bfc9d4;
		color: #3b3f5c;
		font-size: 15px;
		padding: 8px 10px;
		letter-spacing: 1px;
		max-width: 105px;
		padding: 0.5rem 0.5rem;
		border-radius: 6px;
	}
	.boton-precio:focus {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-precio {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border: none;
		text-align: center;
	}
</style>
<br>

@if((new \Jenssegers\Agent\Agent())->isMobile())

@if($itemsQuantity > 0)
<div class="table-responsive" style="background-color: white;">
    <table style="border: solid 4px white; border-radius:5px;" class="table table-bordered table-striped mb-0">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart as $item)
            <tr>
                <td>{{$item->name}} </td>
                <td>
                    <div class="row">
                         <input readonly type="number" id="r{{$item->id}}"
							wire:change="updateQty({{$item->id}}, {{$item->attributes['product_id']}} ,{{$item->attributes['sucursal_id']}} , $('#r' + {{$item->id}}).val() )"
							style="font-size: 1rem!important"
							class="botones1"
							value="{{$item->quantity}}">
							<button  wire:click.prevent="decreaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button  wire:click.prevent="increaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
                    </div>
                   

							<input hidden type="number" id="q{{$item->id}}" value="{{$item->attributes['stock']}}">
							
							

                </td>
                <td>
                ${{number_format(($item->price * (1 - ($item->attributes['descuento']/100) ) *  $item->quantity) + ($item->attributes['iva']*$item->price*$item->quantity * (1 - ($item->attributes['descuento']/100) ) ),2)}}
                </td>
                <td class=" text-center">
                	<button hidden wire:click.prevent="decreaseQty({{$item->id}}, {{$item->attributes['product_id']}} ,{{$item->attributes['sucursal_id']}} , $('#r' + {{$item->id}}).val() )" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button hidden wire:click.prevent="increaseQty({{$item->id}}, {{$item->attributes['product_id']}} ,{{$item->attributes['sucursal_id']}} , $('#r' + {{$item->id}}).val() )" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
							<button wire:click.prevent="comentario({{$item->id}})" class="btn btn-dark btn-sm">
								<i class="fas fa-list"></i>
							</button>
							<button onclick="Confirm('{{$item->id}}', 'removeItem', '¿CONFIRMAS ELIMINAR EL REGISTRO?')" class="btn btn-dark btn-sm">
								<i class="fas fa-trash-alt"></i>
							</button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="card-body" style="background-color: white;">
No hay productos agregados
</div>
@endif

@endif


@if((new \Jenssegers\Agent\Agent())->isTablet())

<div class="connect-sorting">


<div class="connect-sorting-content">
	<div class="card simple-title-task ui-sortable-handle">
		<div class="card-body">

		@if($itemsQuantity > 0)
		<div class="table-responsive tblscroll" style="max-height: none; overflow: auto;;">
			<table class="table table-bordered table-striped mt-1">
				<thead class="text-white" style="background: #3B3F5C">
					<tr>
						<th  style="width:17%;" class="table-th barcode text-left text-white">CODIGO</th>
						<th style="width:17%;" class="table-th text-left text-white">NOMBRE</th>
						<th class="table-th text-center text-white">PRECIO</th>
						<th style="width:15%;" class="table-th text-center text-white">CANT</th>
						<th hidden style="width:15%;" class="table-th text-center text-white">DESC</th>
						<th hidden style="width:20%;" class="table-th text-center text-white">IVA</th>
						<th class="table-th text-center text-white">TOTAL</th>
						<th style="width:30%;" class="table-th text-center text-white">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					@foreach($cart as $item)
					<tr>
						<td class="barcode"><h6>{{$item->attributes['barcode']}}</h6> </td>
						<td><h6> {{$item->name}}
						</h6> </td>
						<td class="text-center">
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
							<input reandonly type="number" id="r{{$item->id}}"
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
						
							<input hidden id="q{{$item->id}}" value="{{$item->attributes['stock']}}">
							
							
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


						<td class="text-center">
 

							<button hidden wire:click.prevent="decreaseQty({{$item->id}}, {{$item->attributes['product_id']}} ,{{$item->attributes['sucursal_id']}} )" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button hidden wire:click.prevent="increaseQty({{$item->id}}, {{$item->attributes['product_id']}} ,{{$item->attributes['sucursal_id']}} )" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
							<button wire:click.prevent="comentario('{{$item->id}}')" class="btn btn-dark btn-sm">
								<i class="fas fa-list"></i>
							</button>
							<button onclick="Confirm('{{$item->id}}', 'removeItem', '¿CONFIRMAS ELIMINAR EL REGISTRO?')" class="btn btn-dark btn-sm">
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
		
@endif

@if((new \Jenssegers\Agent\Agent())->isDesktop())

<div class="connect-sorting">


<div class="connect-sorting-content">
	<div class="card simple-title-task ui-sortable-handle">
		<div class="card-body">

		@if($itemsQuantity > 0)
		<div class="table-responsive tblscroll" style="max-height: none; overflow: auto;;">
			<table class="table table-bordered table-striped mt-1">
				<thead class="text-white" style="background: #3B3F5C">
					<tr>
						<th  style="width:17%;" class="table-th barcode text-left text-white">CODIGO</th>
						<th style="width:17%;" class="table-th text-left text-white">NOMBRE</th>
						<th class="table-th text-center text-white">PRECIO</th>
						<th style="width:15%;" class="table-th text-center text-white">CANT</th>
						<th style="width:15%;" class="table-th text-center text-white">DESC</th>
						<th style="width:20%;" class="table-th text-center text-white">IVA</th>
						<th class="table-th text-center text-white">TOTAL</th>
						<th style="width:30%;" class="table-th text-center text-white">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					@foreach($cart as $item)
					<tr>
						<td class="barcode"><h6>{{$item->attributes['barcode']}}</h6> </td>
						<td><h6> {{$item->name}}
						</h6> </td>
						<td class="text-center">
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

							<input type="number" id="r{{$item->id}}"
							wire:change="updateQty('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#r' + '{{$item->id}}').val() )"
							style="font-size: 1rem!important"
							class="botones1"
							value="{{$item->quantity}}"
							>
							<br>
							Stock: {{$item->attributes['stock']}} un.
							 
							<input hidden id="q{{$item->id}}" value="{{$item->attributes['stock']}}">
						</td>
						<td class="table-th text-center">

							<div style="width:100px;" class="input-group mb-0">
							  <input type="text" id="desc{{$item->id}}" value="{{$item->attributes['descuento']}}"
								wire:keydown.enter="updateDescuento('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#desc' + '{{$item->id}}').val() )"
								wire:change="updateDescuento('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#desc' + '{{$item->id}}').val() )" class="form-control" >
							  <div class="input-group-append">
							    <span class="input-group-text">%</span>
							  </div>
							</div>

						</td>
						<td class="table-th text-center">

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

						<td class="text-center">
							<h6>
								${{number_format(($item->price * (1 - ($item->attributes['descuento']/100) ) *  $item->quantity) + ($item->attributes['iva']*$item->price*$item->quantity * (1 - ($item->attributes['descuento']/100) ) ),2)}}
							</h6>
						</td>


						<td class="text-center">


							<button hidden wire:click.prevent="decreaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button hidden wire:click.prevent="increaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
							<button wire:click.prevent="comentario('{{$item->id}}')" class="btn btn-dark btn-sm">
								<i class="fas fa-list"></i>
							</button>
							<button onclick="Confirm('{{$item->id}}', 'removeItem', '¿CONFIRMAS ELIMINAR EL REGISTRO?')" class="btn btn-dark btn-sm">
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
		<br>
		<footer>
<div class="connect-sorting">
<div class="connect-sorting-content">
	<div class="row">
		<div class="col-5">
			<span>Recordatorio para recontacto en:</span>
			<div class="input-group">
		  <input type="text" class="form-control" wire:model="recordatorio">
		  <div class="input-group-append">
		    <span class="input-group-text">Dias</span>
		  </div>
			<div class="input-group-append">
				<div hidden class="note-footer">
						<div class="tags-selector btn-group">
								<a class="nav-link dropdown-toggle d-icon label-group" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
										<div class="tags">
												<div class="g-dot-personal"></div>
												<div class="g-dot-work"></div>
												<div class="g-dot-social"></div>
												<div class="g-dot-important"></div>
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
										</div>
								</a>
								<div class="dropdown-menu dropdown-menu-right d-icon-menu">
										<a class="note-personal label-group-item label-personal dropdown-item position-relative g-dot-personal" href="javascript:void(0);"> Personal</a>
										<a class="note-work label-group-item label-work dropdown-item position-relative g-dot-work" href="javascript:void(0);"> Work</a>
										<a class="note-social label-group-item label-social dropdown-item position-relative g-dot-social" href="javascript:void(0);"> Social</a>
										<a class="note-important label-group-item label-important dropdown-item position-relative g-dot-important" href="javascript:void(0);"> Important</a>
								</div>
						</div>
				</div>
		 </div>
		</div>
		</div>
        <div class="col-3">
           
        </div>
	</div>

<span>Nota interna</span>
			<textarea wire:model.lazy="nota_interna" class="form-control" rows="3" cols="30"></textarea>

		<span>Observaciones</span>
					<textarea wire:model.lazy="observaciones" class="form-control" rows="3" cols="30"></textarea>



</div>


</div>

@endif
<script type="text/javascript">

function Update(index){
	var stock_descubierto = $("#stock_descubierto"+index).val();
	if(stock_descubierto === "si") {
	var cantidad = $("#r"+index).val();
	var stock = $("#q"+index).val();

	if(cantidad > stock) {
	noty('STOCK INSUFICIENTE. DISPONIBLES: '+stock, 'alert'); // default
		setTimeout(function(){ 	$("#r"+index).val(stock); }	, 200);
	}
  }
}

</script>
<script type="text/javascript">
$('.basic').select2({
								tags: true
						});
</script>
