
<div class="connect-sorting">
<div class="d-flex justify-content-between">
 <div>Lista de precios: 

@can('modificar lista de precios en la venta')
    
    <a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> {{$nombre_lista_precios}}  </a>
 	<div class="dropdown-menu">
 	    <button  wire:click="ActualizarListaPrecios(2,0)"   class="dropdown-item">Precio base</button>
    @foreach($lista_precios as $lp)
		<button  wire:click="ActualizarListaPrecios(2,{{$lp->id}})"   class="dropdown-item">{{$lp->nombre}}</button>
	@endforeach
	</div>
	
@else
{{$nombre_lista_precios}} 
@endif

</div>
 
 
 <div>
    
    @if($df != null)
    
    @can('modificar relacion precio iva')
    <a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Relacion precio - IVA </a>
 	<div class="dropdown-menu">
		<button  wire:click="UpdateRelacionPrecioIva(0)"   class="dropdown-item">Sin IVA @if($relacion_precio_iva == 0) <svg style="margin-left:10px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg> @endif </button>
		<button  wire:click="UpdateRelacionPrecioIva(1)"  class="dropdown-item">Precio + IVA @if($relacion_precio_iva == 1) <svg style="margin-left:10px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg> @endif </button>
		<button  wire:click="UpdateRelacionPrecioIva(2)"  class="dropdown-item">IVA incluido @if($relacion_precio_iva == 2) <svg style="margin-left:10px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg> @endif </button>
	</div>
	@endcan
	
	@can('modificar iva')
	<a style="color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">IVA </a>
 	<div class="dropdown-menu">
		<button  wire:click="UpdateIvaGral(0)"   class="dropdown-item">Sin IVA</button>
		<button  wire:click="UpdateIvaGral(0.105)"  class="dropdown-item">10,5%</button>
		<button  wire:click="UpdateIvaGral(0.21)"   class="dropdown-item">21%</button>
		<button  wire:click="UpdateIvaGral(0.27)"   class="dropdown-item">27%</button>
	</div>
	@endcan
	
	@can('ver facturacion')
	<a {{ $relacion_precio_iva == 0 && $datos_punto_venta_elegido->iva_defecto > 0 ? '' : 'hidden' }} href="{{ url('puntos-venta') }}" target="_blank" style="font-size:11px; margin:4px; color: red; background: white; border: solid 1px red; border-radius: 50%; padding: 0px 7px;" class="example-popover" type="button" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Hay una incompatibilidad entre 'IVA', 'Relacion Precio Iva'y 'Tipo de comprobante' elegido.">!</a>
    @else
    <a {{ $relacion_precio_iva == 0 && $datos_punto_venta_elegido->iva_defecto > 0 ? '' : 'hidden' }} href="javascript:void(0)" style="font-size:11px; margin:4px; color: red; background: white; border: solid 1px red; border-radius: 50%; padding: 0px 7px;" class="example-popover" type="button" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Hay una incompatibilidad entre 'IVA', 'Relacion Precio Iva'y 'Tipo de comprobante' elegido.">!</a>
    @endcan
    
    
	@else
	<p style="cursor:pointer;" wire:click="ErrorRelacionPrecioIva">IVA 0 %</p>
	@endif
	
</div>
</div>
 

<div class="connect-sorting-content">
	<div class="card simple-title-task ui-sortable-handle">
		<div class="card-body"  style="padding:0px;">

		
			<div class="table-responsive">
			<table class="table">
				<thead style="background: #F3F4F5;">
					<tr>
						<th  style="width:4%;"></th>
						<th  style="width:13%;">CODIGO</th>
						<th style="width:17%;">NOMBRE</th>
						<th>PRECIO</th>
						<th style="width:15%;">CANT</th>
						<th hidden style="width:15%;">DESC</th>
						<th style="width:20%;">IVA</th>
						<th>TOTAL</th>
						<th style="width:30%;">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
				    @if($itemsQuantity > 0)
					@foreach($cart as $item)
					<tr>
					    <td>
					    <a style="color:black !important; background: #FAFBFE !important; padding: 1px 8px; border-radius: 8px; border: 1px solid #E9ECEF;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> </a>
                     	<div class="dropdown-menu">
                    		<button  wire:click="DetallesProductoVer({{$item->attributes['product_id']}},{{$item->attributes['referencia_variacion']}})"   class="dropdown-item">VER DETALLE DEL PRODUCTO</button>
                    	</div>
					
					    </td>					    
						<td>{{$item->attributes['barcode']}} </td>
						<td>
						{{$item->name}} @if($item->attributes['pesable'] == 1) x {{$item->quantity}} Kg @endif
						
						@if(0 < $item->attributes['cantidad_promo'] != null)
						
						@if($item->attributes['relacion_precio_iva'] != 2)
						<br><text style="color:red !important;">PROMO: {{$item->attributes['nombre_promo']}}  ({{$item->attributes['cantidad_promo']}} x -${{ number_format($item->attributes['descuento_promo'],2) }}) <a href="javascript:void(0)" onclick="QuitarPromo({{$item}})" title="Quitar promo"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></text>
                        @else
                        <br><text style="color:red !important;">PROMO: {{$item->attributes['nombre_promo']}}  ({{$item->attributes['cantidad_promo']}} x -${{ number_format($item->attributes['descuento_promo'] * (1 + $item->attributes['iva']) , 2) }}) <a href="javascript:void(0)" onclick="QuitarPromo({{$item}})" title="Quitar promo"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a></text>
                        @endif
                        @endif
						</td>
						<td>
							<div style="display:flex;">
								$
							
							@if($item->attributes['relacion_precio_iva'] == 1 || $item->attributes['relacion_precio_iva'] == 0)
                            
                            @can('cambiar precios en la venta')
								<input class="boton-precio" type="number" id="p{{$item->id}}"
								wire:change="updatePrice('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#p' + '{{$item->id}}').val() )"
								value="{{$item->price }}">
							@else
							{{$item->price  * (1+$item->attributes['iva'] )  }}
							@endcan

							@endif
							@if($item->attributes['relacion_precio_iva'] == 2)
							
							 @can('cambiar precios en la venta')
    							<input class="boton-precio" type="number" id="p{{$item->id}}"
    							wire:change="updatePrice('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#p' + '{{$item->id}}').val() )"
    							value="{{$item->price  * (1+$item->attributes['iva'] )  }}">

                            @else
                            {{$item->price  * (1+$item->attributes['iva'] )  }}
                            @endcan 
                            
                            
							@endif
							</div>
                        
                        <p hidden style="font-size: 6px;">Costo: {{$item->attributes['cost']}} </p>
						</td>
						<td>
                            
                            <div class="input-group mb-0">
							<input type="number" id="r{{$item->id}}"
							wire:change="updateQty('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#r' + '{{$item->id}}').val() )"
							wire:keydown.enter="updateQty('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}' , $('#r' + '{{$item->id}}').val() )"
							style="font-size: 1rem!important; border-radius: 5px !important;"
							class="botones1"
							value="{{$item->quantity}}"
							>
        					<div class="input-group-prepend">
                                <span style="color: #637381; height: 100% !important; background: white; margin-left: 1px !important; border:none;" wire:click="$emit('scan-code', $('#code').val())" class="input-group-text input-gp">
                                    @if($item->attributes['pesable'] == 0) un. @else kg. @endif
                                </span> 
                            </div>
                            </div>
							
							Stock: @if($item->attributes['pesable'] == 0) {{ number_format($item->attributes['stock'],0,",",".") }} @else {{ number_format($item->attributes['stock'],3,",",".") }} @endif  @if($item->attributes['pesable'] == 0) un. @else kg. @endif
							 
							<input hidden id="q{{$item->id}}" value="{{$item->quantity}}">
						</td>
						<td hidden>

			

						</td>
						<td>

						       <a style="color: #637381; font-weight: 500 !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						        @if($item->attributes['relacion_precio_iva'] == 2) IVA Incluido <br>  @endif   {{$item->attributes['iva']*100}} % 
						           
						      </a>
                             	<div class="dropdown-menu">
                            		<button   id="iva{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#iva' + '{{$item->id}}').val() , '{{$item->attributes['relacion_precio_iva']}}' )"  value="0"   class="dropdown-item">Sin IVA</button>
                            		<button  id="ivaprimero{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#ivaprimero' + '{{$item->id}}').val(), '{{$item->attributes['relacion_precio_iva']}}'  )"  value="0.105" class="dropdown-item">10,5%</button>
                            		<button  id="ivasegundo{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#ivasegundo' + '{{$item->id}}').val(), '{{$item->attributes['relacion_precio_iva']}}'  )"  value="0.21"  class="dropdown-item">21%</button>
                            		<button  id="ivatercero{{$item->id}}" wire:click="UpdateIva('{{$item->id}}', '{{$item->attributes['product_id']}}' , '{{$item->attributes['referencia_variacion']}}' , '{{$item->attributes['sucursal_id']}}', $('#ivatercero' + '{{$item->id}}').val(), '{{$item->attributes['relacion_precio_iva']}}'  )"  value="0.27"  class="dropdown-item">27%</button>
                            	</div>
						
						</td>

						<td>
						   @php
						   $subtotal_item = (( ($item->price * $item->quantity) - ($item->attributes['descuento_promo'] * $item->attributes['cantidad_promo']) ) );
						   if($item->attributes['relacion_precio_iva'] != 0){
						   $iva_item = $subtotal_item * $item->attributes['iva'];
						   } else {
						   $iva_item = 0;
						   }
						   
						   
						   @endphp
						  $ {{number_format($subtotal_item + $iva_item, 2, ",",".") }}
						   -

						</td>


						<td>


							<button hidden wire:click.prevent="decreaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-minus"></i>
							</button>
							<button hidden wire:click.prevent="increaseQty('{{$item->id}}', {{$item->attributes['product_id']}} ,  {{$item->attributes['referencia_variacion']}} , {{$item->attributes['sucursal_id']}})" class="btn btn-dark btn-sm">
								<i class="fas fa-plus"></i>
							</button>
							@if($lista_precios_elegida != 1)
							<button wire:click.prevent="ModalAgregarDescuento('{{$item->id}}')" class="btn btn-dark btn-sm">
							    DESCUENTO 
							</button>
							@endif
							<button wire:click.prevent="comentario('{{$item->id}}')" class="btn btn-dark btn-sm">
								<i class="fas fa-list"></i>
							</button>

							<button onclick="Confirm('{{$item->id}}', 'removeItem', 'CONFIRMAS ELIMINAR EL REGISTRO?')" class="btn btn-dark btn-sm">
								<i class="fas fa-trash-alt"></i>
							</button>

						</td>
					</tr>
					@endforeach
					@else 
					<tr style="height: 50px;">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					@endif
				</tbody>
			</table>
		</div>
	
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
	<div hidden class="row">
		<div class="col-5">
			<span>Recordatorio para recontacto en:</span>
			<div class="input-group">
						
		<!----- Muestra a todos los planes mayores a 1 ----->
				
		@if(2 < Auth::user()->plan)
		<input type="text" class="form-control" wire:model="recordatorio">
		@else
		<input type="text" onclick="MejorarPlan()" style="cursor:pointer !important; background: white !important;"	readonly class="form-control" wire:model="recordatorio">
		@endif
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


            <!----- Muestra a todos los planes mayores a 1 ----->
            <span>Nota interna</span>
			<textarea wire:model.lazy="nota_interna" class="form-control" rows="3" cols="30"></textarea>
            
		    <span>Observaciones</span>
			<textarea wire:model.lazy="observaciones" class="form-control" rows="3" cols="30"></textarea>

<br>
Codigo de venta: {{$idVenta}}
</div>


</div>
</footer>