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


<div>
@if($itemsQuantity > 0)

<div class="d-flex justify-content-between">
 <div><p>Lista de precios: {{$nombre_lista_precios}}</p></div>
 <div>
               
     <a style="color:black !important;" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">IVA  {{$iva_elegido * 100}} %  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg> </a>
   
								<div class="dropdown-menu">
										<button  wire:click="UpdateIvaGral(0)"   class="dropdown-item">Sin IVA</button>
										<button  wire:click="UpdateIvaGral(0.105)"  class="dropdown-item">10,5%</button>
										<button  wire:click="UpdateIvaGral(0.21)"   class="dropdown-item">21%</button>
										<button  wire:click="UpdateIvaGral(0.27)"   class="dropdown-item">27%</button>
								</div>
</div>
</div>
 
 
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
                   

							<input hidden type="number" id="q{{$item->id}}" value="{{$item->quantity}}">
							
							

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
<div class="card-body" style="background-color: white;">
No hay productos agregados
</div>
@endif
    
</div>

@endif


@if((new \Jenssegers\Agent\Agent())->isTablet())

@include('livewire.pos_nuevo.partials.detail-tablet')   

@endif

@if((new \Jenssegers\Agent\Agent())->isDesktop())

@include('livewire.pos_nuevo.partials.detail-desktop')   
        
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
