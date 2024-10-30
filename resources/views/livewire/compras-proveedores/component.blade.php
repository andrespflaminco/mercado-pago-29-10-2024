@inject('cart', 'App\Services\Cart')

<div class="row sales layout-top-spacing">

	<div class="col-sm-12 ">

		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title"> Nueva Compra</h4>

				<a  class="btn btn-dark" href="{{ url('compras-resumen') }}">

						 Volver al resumen </a>

			</div>

			<div class="row">


					<div class="col-lg-3 col-md-4 col-sm-12">

						<div class="input-group mb-4">
							<input type="text"  id="code" wire:keydown.enter.prevent="BuscarCode($('#code').val())" wire:model="codigo" placeholder="Cod. del producto" class="form-control"
							>
							<div class="input-group-prepend">
								<span class="input-group-text input-gp">
									<i class="fas fa-search"></i>
								</span>
							</div>
						</div>
				</div>

				<div class="col-lg-4 col-md-4 col-sm-12">
				<div style="margin-bottom: 0 !important;" class="input-group mb-4">
					<div class="input-group-prepend">
						<span class="input-group-text input-gp">
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

								<div style="position:absolute; z-index: 999 !important;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
										@if(!empty($products_s))
												@foreach($products_s as $i => $product)
												<button value="{{$product['barcode']}}" id="code{{$product['barcode']}}"  wire:click="$emit('scan-code', $('#code{{$product['barcode']}}').val())" wire:click.lazy="selectProduct"
												class="btn btn-light" title="Click en el producto">{{ $product['barcode'] }} - {{ $product['name'] }}
										</button>


												@endforeach


										@else
										<div style="  padding: 10px;  text-align: center;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
										No hay resultados
										</div>
										@endif
										<a href="javascript:void(0)"   class="btn btn-dark" data-toggle="modal" data-target="#ModalProductos" >Agregar otro producto</a>

								</div>
						@endif

				</div>




				</div>
			<div class="widget-content">

									<div class="connect-sorting-content">

											@if ($cart->getContent()->count() > 0)
											<div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
												<table class="table table-bordered table-striped mt-1">
													<thead class="text-white" style="background: #3B3F5C">
														<tr>
														    <th class="table-th text-left text-white">CODIGO</th>
															<th class="table-th text-left text-white">DESCRIPCIÓN</th>
															<th class="table-th text-center text-white">COSTO</th>
															<th width="18%" class="table-th text-center text-white">CANT</th>
															<th class="table-th text-center text-white">IMPORTE</th>
															<th style="width:10%;" class="table-th text-center text-white">ACCIONES</th>
														</tr>
													</thead>
													<tbody>
															@foreach ($cart->getContent() as $product)
														<tr>
                                                            <td><h6>{{ $product['referencia_id'] }} - {{ $product['barcode'] }} </h6></td>
															<td><h6>{{ $product['name'] }}</h6></td>
															<td class="text-center"><h6>$ {{$product['cost']}} </h6></td>
															<td class="text-center">
																<div class="btn-group" role="group" aria-label="Basic example">
																<a wire:click="Decrecer({{$product['id']}})"  style="padding-top: 12px;" class="btn btn-dark btn-sm"><i class="fas fa-minus"></i></a>
																<input readonly type="number" id="r{{$product['id']}}" wire:change="updateQty( {{$product['id']}}, $('#r'+ {{$product['id']}}).val())"
																style="width: 80px !important; margin:0 auto !important; color:#3b3f5c;"		class="form-control text-center"
																value="{{$product['qty']}}">
																	<a wire:click="Incrementar({{$product['id']}})"  style="padding-top: 12px;" class="btn btn-dark btn-sm"><i class="fas fa-plus"></i></a>
																</div>
															</td>
															<td class="text-center">
																<h6>
																	$ {{ $product['cost']*$product['qty'] }}
																</h6>
															</td>
															<td class="text-center">
																<button wire:click="removeProductFromCart({{$product['id']}})" class="btn btn-dark mbmobile">
																	<i class="fas fa-trash-alt"></i>
																</button>
															</td>
														</tr>

														@endforeach
														<tfoot class="bg-dark" style="color:black;">
															<tr>

																<th class="table-th text-center text-white">TOTAL</th>
																<th class="table-th text-white"></th>
																<th class="table-th text-center text-white">{{ $cart->totalCantidad() }}</th>
																<th class="table-th text-center text-white ">$ {{ $cart->totalAmount() }}</th>
																<th class="table-th text-center "></th>

															</tr>
														</tfoot>
													</tbody>
												</table>
											</div>
											@else
											<h5 class="text-center text-muted">Agregue productos al carrito</h5>
											@endif

									<!--
											<div wire:loading.inline wire:target="saveSale">
												<h4 class="text-danger text-center">Guardando Venta...</h4>
											</div>
										-->

									<br>
									<span>Observaciones</span>
									<div class="col-lg-6 col-sm-12">
										<textarea wire:model.lazy="observaciones" class="form-control" rows="3" cols="30"></textarea>
										<br><br>
									</div>

									@if($cart->hasProducts())
									 <button class="btn btn-dark close-btn text-light" wire:click.prevent="Edit2({{$cart->totalAmount()}})"  title="Guardar">
									<i class="fa fa-shopping-cart"></i> GUARDAR
									</button>
									@endif
								</div>
						</div>
				</div>

				</a>
		@include('livewire.compras-proveedores.form')
		@include('livewire.compras-proveedores.form2')
	</div>
</div>
<script src="{{ asset('js/keypress.js') }}"></script>
<script src="{{ asset('js/onscan.js') }}"></script>
<script>

try{

    onScan.attachTo(document, {
    suffixKeyCodes: [13],
    onScan: function(barcode) {
        console.log(barcode)
        window.livewire.emit('scan-code', barcode)
    },
    onScanError: function(e){
        //console.log(e)
    }
})

    console.log('Scanner ready!')


} catch(e){
    console.log('Error de lectura: ', e)
}


</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
            livewire.on('scan-code', action => {
                $('#code').val('')
            })
						window.livewire.on('sale-ok', Msg => {
								$('#theModal2').modal('hide')
								noty(Msg)
						})

								window.livewire.on('agregar-cliente', Msg => {
										$('#theModal-cliente').modal('show')
								})

								window.livewire.on('agregar-pago', Msg =>{
										$('#AgregarPago').modal('show')
								})

								window.livewire.on('show-modal2', Msg =>{
										$('#theModal2').modal('show')
								})

								window.livewire.on('agregar-pago-hide', Msg =>{
										$('#AgregarPago').modal('hide')
								})

								window.livewire.on('pago-dividido', Msg =>{
										$('#PagoDividido').modal('show')
								})

								window.livewire.on('pago-dividido-hide', Msg =>{
										$('#PagoDividido').modal('hide')
								})


								window.livewire.on('hide-modal2', Msg =>{
										$('#modalDetails2').modal('hide')
								})

								window.livewire.on('cerrar-factura', Msg =>{
										$('#theModal1').modal('hide')
								})

								window.livewire.on('modal-show', msg => {
									$('#theModal1').modal('show')
								})

								window.livewire.on('abrir-hr-nueva', msg => {
									$('#theModal').modal('show')
								})
								window.livewire.on('show-modal', Msg => {
									$('#theModal').modal('show')
								})
								window.livewire.on('show-modal3', Msg =>{
										$('#modalDetails3').modal('show')
								})

								window.livewire.on('hide-modal3', Msg =>{
										$('#modalDetails3').modal('hide')
								})



								window.livewire.on('modal-hr-hide', Msg =>{
										$('#theModal').modal('hide')
								})

								window.livewire.on('hr-added', Msg => {
									noty(Msg)
								})

								window.livewire.on('modal-estado', Msg =>{
										$('#modalDetails-estado-pedido').modal('show')
								})

								window.livewire.on('modal-estado-hide', Msg =>{
										$('#modalDetails-estado-pedido').modal('hide')
								})

								window.livewire.on('hr-asignada', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-agregado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-actualizado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-eliminado', Msg => {
									noty(Msg)
								})
								//events
								window.livewire.on('product-added', Msg => {
									$('#theModal').modal('hide')
									noty(Msg)
								})

								window.livewire.on('no-stock', Msg => {
									noty(Msg, 2)
								})

								var total = $('#suma_totales').val();
								$('#ver_totales').html('Ventas: '+total);


    });
</script>

@include('livewire.pos.scripts.shortcuts')
@include('livewire.pos.scripts.events')
@include('livewire.pos.scripts.general')
