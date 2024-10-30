@inject('cart', 'App\Services\CartRecetas')

<div>
  
    
                <div class="page-header">
					<div class="page-title">
					    @if($accion == 1)
							<h4>Nueva Receta</h4>
							<h6>Agrega insumos a la receta</h6>
						@endif
						@if($accion == 2)
							<h4>Editar Receta</h4>
							<h6>Editar los insumos de la receta</h6>
						@endif
					    @if($accion == 3)
							<h4>Ver Receta</h4>
							<h6>Ver el listado de insumos de la receta</h6>
						@endif
						</div>
						<div class="page-btn">               											    
                			
                			@if(Auth::user()->sucursal != 1)
                			@if(Auth::user()->profile != "Cajero" )
							<a href="{{ url('recetas') }}" class="btn btn-added">Volver al Resumen</a>
						    @endif
						    @endif
						    
						</div>
					</div>
            
            
			<div class="row">
                
                @if($accion != 3)
				<div class="col-lg-4 col-md-4 col-sm-12">
				    <div style="margin-bottom: 0 !important;" class="input-group mb-4">
					<div class="input-group-prepend">
						<span style="height: 100%;" class="input-group-text input-gp">
							<i class="fas fa-clipboard-list"></i>
						</span>
					</div>
						<input
								style="font-size:14px !important;"
								type="text"
								class="form-control"
								placeholder="Seleccione un insumo"
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
										<a hidden href="javascript:void(0)"   class="btn btn-dark" data-toggle="modal" data-target="#theModal" >Agregar otro insumo</a>

								</div>
						@endif

				</div>
				@endif
				<div class="col-lg-3 col-md-4 col-sm-12">

						<div class="input-group mb-4">
							<span style="background: #eee !important; border: solid 1px #bfc9d4 !important; color: #3b3f5c!important;" class="input-group-text input-gp">
									Rinde
								</span>
							<input @if($accion == 3) readonly @endif type="number"  wire:model="rinde" placeholder="" class="form-control">
							<span style="background: #eee !important; border: solid 1px #bfc9d4 !important; color: #3b3f5c!important;" class="input-group-text input-gp">
									Unidades
								</span>
						</div>
						
				</div>




			</div>
							<!-- /product list -->
					<div class="card">
				
						<div class="card-body">


											@if ($cart->getContent()->count() > 0)
											<div class="table-responsive">
												<table class="table">
													<thead>
														<tr>
															<th>DESCRIPCIÓN</th>
															<th>COSTO</th>
															<th>UNIDAD MEDIDA</th>
															<th>CANT</th>
															<th>IMPORTE</th>
															<th>ACCIONES</th>
														</tr>
													</thead>
													<tbody>
															@foreach ($cart->getContent() as $product)
														<tr>

															<td>{{ $product['name'] }}</td>
															<td>$ {{$product['cost']*$product['relacion'] }}</td>
															<td>{{ $product['nombre_unidad_medida'] }}</td>
															<td>{{$product['qty']}}</td>
															<td>$ {{ number_format($product['cost']*$product['relacion']*$product['qty'] , 2,",",".") }}</td>
															<td>
															@if($accion != 3)
			
																<button wire:click="Edit('{{$product['id']}}','{{$product['qty']}}','{{$product['unidad_medida']}}')" class="btn btn-dark mbmobile">
																	<i class="fas fa-edit"></i>
																</button>
																<button wire:click="removeProductFromCart({{$product['id']}})" class="btn btn-dark mbmobile">
																	<i class="fas fa-trash-alt"></i>
																</button>
                                                            @endif
                                                            
															</td>
														</tr>

														@endforeach
														<tfoot>
															<tr>

																<th>TOTAL</th>
																<th></th>
																<th></th>
																<th></th>
																<th>$ {{ number_format($cart->totalAmount() , 2,",",".") }}</th>
																<th></th>

															</tr>
														</tfoot>
													</tbody>
												</table>
											</div>
											@else
											<div style="padding:10%; border: solid 1px #eee; ">
											<h5 class="text-center text-muted">Agregue insumos al detalle</h5>    
											</div>
											@endif


									<br>


									@if($cart->hasProducts())
									
									@if($accion == 1)
									<button class="btn btn-submit" wire:click.prevent="StoreReceta()"  title="Guardar">
									    GUARDAR
									</button>
									@endif
																		
									@if($accion == 2)
									<button class="btn btn-submit" wire:click.prevent="UpdateReceta()"  title="Actualizar">
									    ACTUALIZAR
									</button>
									@endif
									
									
									@endif
					    </div>
				    </div>


		@include('livewire.recetas_detalle.form')
		@include('livewire.recetas_detalle.form-editar')
		@include('livewire.recetas_detalle.form2')
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
