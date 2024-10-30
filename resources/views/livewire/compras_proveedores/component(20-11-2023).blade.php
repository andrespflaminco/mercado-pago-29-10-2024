@inject('cart', 'App\Services\Cart')


	<div class="row layout-top-spacing">
		<div class="col-sm-12 col-md-12">

    <div class="page-header">
		<div class="page-title">
			<h4>NUEVA COMPRA</h4>
			<h6>Agregue productos a su carrito</h6>
		</div>
		<div class="page-btn">
			<a hidden class="btn btn-success" href="{{ url('import-compra') }}">
			Importar compra
			</a>
			<a  href="{{ url('compras-resumen') }}" class="btn btn-added">
			Volver al resumen
			</a>
		</div>
	</div>
	</div>
		<div class="col-sm-12 col-md-12">
			<div class="row">


				<div class="col-lg-2 col-md-4  col-sm-12">

					<div class="input-group mb-0">
					
						<input type="text"  id="code" wire:keydown.enter.prevent="BuscarCode($('#code').val())" wire:model="codigo" placeholder="Cod. del producto" class="form-control"
						>
						<div class="input-group-prepend">
								<span wire:click="BuscarCode($('#code').val())" class="input-group-text input-gp">
									<i style="padding: 0.30rem !important; font-size:16px !important;" class="fas fa-search"></i>
								</span> 
							</div>
					</div>
			</div>

			<div class="col-lg-4 col-md-0 col-sm-12">
			<div class="input-group mb-0">
				<div class="input-group-prepend">
					<span class="input-group-text input-gp">
						<i style="padding: 0.30rem !important; font-size:16px !important;" class="fas fa-clipboard-list"></i>
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
									<a href="javascript:void(0)" hidden  class="btn btn-dark" data-toggle="modal" data-target="#ModalProductos" >Agregar otro producto</a>

							</div>
					@endif

			</div>
                
                
                
                @if($proveedor_id != 2)
				<div class="col-lg-3 col-md-4 col-sm-12">
				
				@include('livewire.compras_proveedores.busqueda-proveedor')

				</div>
				@endif




<div class="col-lg-3 col-md-4 col-sm-12">

	<div class="input-group mb-0">
		<div class="input-group-prepend">
			<span class="input-group-text input-gp">
				IVA GRAL
			</span>
		</div>

		<select wire:model="iva_general" wire:change="UpdateIvaGral()" class="form-control" >
			<option hidden value="Elegir" selected>Elegir</option>
			<option value="0">Sin IVA</option>
			<option value="0.105">10,5%</option>
			<option value="0.21">21%</option>
			<option value="0.27">27%</option>

		</select>
	</div>


		</div>

		<div class="col-lg-2 col-md-4 col-sm-12">


				</div>


		</div>

		</div>

		<div class="col-sm-12 col-md-8">
			<!-- DETALLES -->


			@include('livewire.compras_proveedores.partials.detail')


		</div>

		<div class="col-sm-12 col-md-4">
			<!-- TOTAL -->
			@include('livewire.compras_proveedores.partials.total')
			



		</div>


@include('livewire.compras_proveedores.form')
@include('livewire.compras_proveedores.variaciones')
@include('livewire.compras_proveedores.form2')
@include('livewire.gastos.estado-pedido-pos')
@include('livewire.gastos.form-abrir')
	
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

                        	window.livewire.on('msg', Msg => {
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
									window.livewire.on('hide-modal2', Msg =>{
											$('#theModal2').modal('hide')
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


									window.livewire.on('variacion-elegir', Msg => {
												$('#Variaciones').modal('show')
									})

									window.livewire.on('variacion-elegir-hide', Msg => {
												$('#Variaciones').modal('hide')
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

                            		window.livewire.on('abrir-caja', msg => {
                            			$('#AbrirCaja').modal('show')
                            		});
                            		window.livewire.on('abrir-caja-hide', msg => {
                            			$('#AbrirCaja').modal('hide')
                            		});

	    });
	</script>

	@include('livewire.pos.scripts.shortcuts')
	@include('livewire.pos.scripts.events')
	@include('livewire.pos.scripts.general')
