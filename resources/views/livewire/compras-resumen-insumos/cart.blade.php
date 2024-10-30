@inject('cart', 'App\Services\Cart')
@extends('layouts.theme.app')

@section('content')
<br>
<div class="connect-sorting">


<div class="connect-sorting-content">
	<div class="card simple-title-task ui-sortable-handle">
		<div class="card-body">
				<a class="btn btn-secondary" href="{{url('ventas-fabrica')}}"><i class="fas fa-arrow-left"></i>Volver</a>
		@if ($cart->getContent()->count() > 0)
		<div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
			<table class="table table-bordered table-striped mt-1">
				<thead class="text-white" style="background: #3B3F5C">
					<tr>
						<th class="table-th text-left text-white">DESCRIPCIÓN</th>
						<th class="table-th text-center text-white">PRECIO</th>
						<th width="18%" class="table-th text-center text-white">CANT</th>
						<th class="table-th text-center text-white">IMPORTE</th>
						<th style="width:30%;" class="table-th text-center text-white">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					  @foreach ($cart->getContent() as $product)
					<tr>

						<td><h6>{{ $product['name'] }}</h6></td>
						<td class="text-center"><h6>$ {{number_format($product['price'],2)}} </h6></td>
						<td>
							<div class="btn-group" role="group" aria-label="Basic example">
							<a href="{{ route('decrecer', $product['id']) }}" style="padding-top: 12px;" class="btn btn-dark btn-sm"><i class="fas fa-minus"></i></a>
							<input readonly type="number" id="r{{$product['id']}}" wire:change="updateQty( {{$product['id']}}, $('#r'+ {{$product['id']}}).val())"
							style="width: 80px !important; margin:0 auto !important; color:#3b3f5c;"		class="form-control text-center"
							value="{{$product['qty']}}">
								<a href="{{ route('incrementar', $product['id']) }}"  style="padding-top: 12px;" class="btn btn-dark btn-sm"><i class="fas fa-plus"></i></a>
							</div>
						</td>
						<td class="text-center">
							<h6>
								$ {{ number_format($product['price']*$product['qty'],2)}}
							</h6>
						</td>
						<td class="text-center">
							<a href="{{ route('remove_product_from_cart', $product['id']) }}"
											class="btn btn-danger btn-sm">Eliminar</a>

						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@else
		<h5 class="text-center text-muted">Agrega productos a la venta</h5>
		@endif
		<input type="text" value="{{ $cart->totalCantidad() }}" wire:model.lazy="itemsQuantity" class="form-control" >
		<input type="text" value="{{ $cart->totalAmount() }}" wire:model.lazy="total" class="form-control" >

		@if($cart->hasProducts())

		<div class="row">
				<div class="col-3 my-2 mx-right">
					<button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">GUARDAR</button>
				</div>
		</div>
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
<script>
    document.addEventListener('DOMContentLoaded', function(){
            livewire.on('scan-code', action => {
                $('#code').val('')
            })
    })
</script>
@endsection
