@inject('cart', 'App\Services\Cart')
@extends('layouts.theme.app')

@section('content')
<div class="row sales layout-top-spacing">

	<div class="col-sm-12 ">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title"> </h4>

			</div>
			<div class="widget-content">
				<a class="btn btn-secondary" href="{{url('ventas-fabrica')}}"><i class="fas fa-arrow-left"></i>Volver</a>

  @if ($cart->getContent()->count() > 0)

				<div class="table-responsive">
					<table  class="table table-bordered table-striped  mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>

								<th class="table-th text-center text-white">PRODUCTOS</th>
								<th class="table-th text-white">PRECIO</th>
								<th style="max-width:20%;" class="table-th text-center text-white">CANTIDADES</th>
								<th class="table-th text-center text-white">SUBTOTAL</th>
                <th class="table-th text-center text-white"></th>

							</tr>
						</thead>
						<tbody>
                @foreach ($cart->getContent() as $product)
							<tr>
								<td class="text-center">
									<h6>{{ $product['name'] }}</h6>

								</td>
								<td class="text-center"><h6>$ {{number_format($product['price'],2)}}</h6></td>
								<td class="text-center">
									<div class="btn-group" role="group" aria-label="Basic example">
  								<a href="{{ route('decrecer', $product['id']) }}" style="padding-top: 12px;" class="btn btn-dark btn-sm"><i class="fas fa-minus"></i></a>
									<input readonly type="number" id="r{{$product['id']}}" wire:change="updateQty( {{$product['id']}}, $('#r'+ {{$product['id']}}).val())"
									style="width: 80px !important; margin:0 auto !important; color:#3b3f5c;"		class="form-control text-center"
									value="{{$product['qty']}}">
  									<a href="{{ route('incrementar', $product['id']) }}"  style="padding-top: 12px;" class="btn btn-dark btn-sm"><i class="fas fa-plus"></i></a>
									</div>


									 </td>
                <td class="text-center"><h6>
									$ {{ number_format($product['price']*$product['qty'],2)}}</h6>
									 </td>
                <td class="text-center"><a href="{{ route('remove_product_from_cart', $product['id']) }}"
                        class="btn btn-danger btn-sm">Eliminar</a></td>

							</tr>
              	@endforeach
              <tfoot class="bg-dark" style="color:black;">
                <tr>

  								<th class="table-th text-center text-white">TOTAL</th>
  								<th class="table-th text-white"></th>
  								<th class="table-th text-center text-white">{{ $cart->totalCantidad() }}</th>
  								<th class="table-th text-center text-white ">$ {{ number_format($cart->totalAmount(),2) }}</th>
                  <th class="table-th text-center "></th>

  							</tr>
              </tfoot>



						</tbody>
					</table>
					 <input type="text" value="{{ $cart->totalCantidad() }}" wire:model.lazy="itemsQuantity" class="form-control" >
					 <input type="text" value="{{ number_format($cart->totalAmount(),2) }}" wire:model.lazy="total" class="form-control" >
					 <input type="text" value="{{ $cart->totalCantidad() }}" wire:model.lazy="selected_id" class="form-control" >

          @if($cart->hasProducts())
          <div class="row">
              <div class="col-3 my-2 mx-right">
								<button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">GUARDAR</button>
              </div>
          </div>
          @endif
          @else
          <div class="alert alert-danger" role="alert">
              <h4 class="alert-heading">Carrito vacío</h4>
              <p>Pase a productos y seleccione sus productos a comprar.</p>
              <hr>
              <a class="btn btn-success" href="{{url('ventas-fabrica')}}">Productos</a>
          </div>
          @endif
      </div>

				</div>

			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {

		//events
		window.livewire.on('product-added', Msg => {
			$('#theModal').modal('hide')
			noty(Msg)
		})
		window.livewire.on('product-updated', Msg => {
			$('#theModal').modal('hide')
			noty(Msg)
		})
		window.livewire.on('product-deleted', Msg => {
			noty(Msg)
		})
		window.livewire.on('hide-modal', Msg => {
			$('#theModal').modal('hide')
		})
		window.livewire.on('show-modal', Msg => {
			$('#theModal').modal('show')
		})
		$('#theModal').on('hidden.bs.modal', function (e) {
			$('.er').css('display','none')
			console.log('display:none')
		})



	})


	function Confirm(id)
	{
		swal({
			title: 'CONFIRMAR',
			text: '¿DESEAS ELIMINAR EL REGISTRO?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			padding: '2em'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteRow', id)
				swal.close()
			}
		})

	}
</script>
@endsection
