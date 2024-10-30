@inject('cart', 'App\Services\CartRecetas')

<div class="row sales layout-top-spacing">

	<div class="col-sm-12 ">

		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title"> Editar receta de componentes</h4>

				<a  class="btn btn-dark" href="{{ url('produccion_recetas') }}">

						 Volver al resumen </a>

			</div>

			<div class="row">


					<div hidden class="col-lg-3 col-md-4 col-sm-12">

						<div class="input-group mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text input-gp">
									<i class="fas fa-search"></i>
								</span>
							</div>
							<input type="text"  id="code" wire:keydown.enter.prevent="BuscarCode($('#code').val())" wire:model="codigo" placeholder="Cod. del producto" class="form-control"
							>
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
										<a href="javascript:void(0)"   class="btn btn-dark" data-toggle="modal" data-target="#theModal" >Agregar otro insumo</a>

								</div>
						@endif

				</div>
				
				
				<div class="col-lg-3 col-md-4 col-sm-12">

						<div class="input-group mb-4">
							<span style="background: #eee !important; border: solid 1px #bfc9d4 !important; color: #3b3f5c!important;" class="input-group-text input-gp">
									Rinde
								</span>
							<input type="number"  wire:model="rinde" placeholder="" class="form-control">
							<span style="background: #eee !important; border: solid 1px #bfc9d4 !important; color: #3b3f5c!important;" class="input-group-text input-gp">
									Unidades
								</span>
						</div>
						
				</div>




				</div>
			<div class="widget-content">

									<div class="connect-sorting-content">

											@if ($cart->getContent()->count() > 0)
											<div class="table-responsive tblscroll" style="max-height: 650px; overflow: hidden">
												<table class="table table-bordered table-striped mt-1">
													<thead class="text-white" style="background: #3B3F5C">
														<tr>
														  	<th class="table-th text-left text-white">DESCRIPCIÓN</th>
															<th class="table-th text-center text-white">COSTO</th>
															<th width="18%" class="table-th text-center text-white">UNIDAD MEDIDA</th>
															<th width="18%" class="table-th text-center text-white">CANT</th>
															<th class="table-th text-center text-white">IMPORTE</th>
															<th style="width:10%;" class="table-th text-center text-white">ACCIONES</th>
														</tr>
													</thead>
													<tbody>
															@foreach ($cart->getContent() as $product)
														<tr>
                                                           <td><h6>{{ $product['name'] }}</h6></td>
															<td class="text-center"><h6>$ {{$product['cost']*$product['relacion'] }} </h6></td>
															<td class="text-center">
																<h6>
																	{{ $product['nombre_unidad_medida'] }}
																</h6>
															</td>
															<td class="text-center">
																<h6>{{$product['qty']}}</h6>

															</td>

															<td class="text-center">
																<h6>
																	$ {{ $product['cost']*$product['relacion']*$product['qty'] }}
																</h6>
															</td>
															<td class="text-center" style="min-width:200px;">
																<button wire:click="Edit('{{$product['id']}}','{{$product['qty']}}','{{$product['unidad_medida']}}')" class="btn btn-dark mbmobile">
																	<i class="fas fa-edit"></i>
																</button>
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
																<th class="table-th text-white"></th>
																<th class="table-th text-center text-white"></th>
																<th class="table-th text-center text-white ">$ {{ $cart->totalAmount() }}</th>
																<th class="table-th text-center "></th>

															</tr>
														</tfoot>
													</tbody>
												</table>
											</div>
											@else
											<h5 class="text-center text-muted">Agregue insumos al detalle</h5>
											@endif

									<!--
											<div wire:loading.inline wire:target="saveSale">
												<h4 class="text-danger text-center">Guardando Venta...</h4>
											</div>
										-->

									<br>


									@if($cart->hasProducts())
									 <button class="btn btn-dark close-btn text-light" wire:click.prevent="saveSale()"  title="Guardar">
									GUARDAR
									</button>
									@endif
								</div>
						</div>
				</div>

				</a>
		@include('livewire.componentes_editar.form')
		@include('livewire.componentes_editar.form-editar')
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
								window.livewire.on('show-modal-editar', Msg => {
									$('#theModalEditar').modal('show')
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
									$('#theModalEditar').modal('hide')
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
