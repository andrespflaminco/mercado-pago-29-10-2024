@inject('cart', 'App\Services\CartMovimiento')

	<div>
	
	              <div class="page-header">
					<div class="page-title">
							<h4>Nuevo movimiento de stock</h4>
							<h6>Agregue un nuevo movimiento de stock entre sucursales</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
						 
					        <a class="btn btn-added" href="{{ url('movimiento-stock-resumen') }}">Volver al resumen de movimientos</a>
						</div>
					</div>
					
		<div class="col-sm-12 col-md-12">

			<div class="card">
				
			<div class="card-body">


				<div class="col-4" style="margin-left:20px;">
					@if($sucursal_origen != 0 )
					Sucursal de origen: <button type="button" style="margin-left:10px;" class="btn btn-light" wire:click="ElegirSucursalOrigen"   name="button">{{$nombre_sucursal_origen}}</button>
					@else
					Sucursal de origen: <button type="button" style="margin-left:10px;" class="btn btn-dark" wire:click="ElegirSucursalOrigen" name="button"> Elegir </button>
					@endif
				</div>

				<div class="col-4" style="margin-left:20px;">
					@if($sucursal_destino != 0 )
					Sucursal de destino: <button type="button" style="margin-left:10px;" class="btn btn-light" wire:click="ElegirSucursalDestino"   name="button">{{$nombre_sucursal_destino}}</button>
					@else
					Sucursal de destino: <button type="button" style="margin-left:10px;" class="btn btn-dark" wire:click="ElegirSucursalDestino" name="button"> Elegir </button>
					@endif
				</div>

		</div>
		</div>

		@if($sucursal_origen != 0)


		<div class="row">


		<div class="col-lg-2 col-md-4  col-sm-12">

					<div class="input-group mb-0">
						<div class="input-group-prepend">
							<span style="height:100%;"  class="input-group-text input-gp">
								<i class="fas fa-search"></i>
							</span>
						</div>
						<input type="text"  id="code" wire:keydown.enter.prevent="BuscarCode($('#code').val())" wire:model="codigo" placeholder="Cod. del producto" class="form-control"
						>
					</div>
			</div>

		<div class="col-lg-4 col-md-0 col-sm-12">
			<div class="input-group mb-0">
				<div class="input-group-prepend">
					<span style="height:100%;" class="input-group-text input-gp">
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
											<button value="{{$product['barcode']}}" id="code{{$product['barcode']}}"  wire:click="AgregarCodigoDesdeBuscador('{{$product['barcode']}}')" wire:click.lazy="selectProduct"
											class="btn btn-light" title="Click en el producto">{{ $product['barcode'] }} - {{ $product['name'] }}
									</button>


											@endforeach


									@else
									<div style="  padding: 10px;  text-align: center;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
									No hay resultados
									</div>
									@endif

							</div>
					@endif

			</div>


        <div class="col-lg-3 col-md-4 col-sm-12">


		</div>

		<div class="col-lg-2 col-md-4 col-sm-12">


				</div>


		</div>

		</div>

        <div class="row">
            
		<div class="col-sm-12 col-md-8">
			<!-- DETALLES -->


			@include('livewire.movimiento-stock.partials.detail')


		</div>

		<div class="col-sm-12 col-md-4">
			<!-- TOTAL -->
			@include('livewire.movimiento-stock.partials.total')



		</div>
        </div>


@endif


@include('livewire.movimiento-stock.variaciones')
@include('livewire.movimiento-stock.form')
@include('livewire.movimiento-stock.form2')
@include('livewire.movimiento-stock.agregar-origen')
@include('livewire.movimiento-stock.agregar-destino')

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

									window.livewire.on('agregar-destino', Msg => {
											$('#AgregarDestino').modal('show')
									})

									window.livewire.on('agregar-destino-hide', Msg => {
											$('#AgregarDestino').modal('hide')
									})

									window.livewire.on('agregar-origen', Msg => {
											$('#AgregarOrigen').modal('show')
									})

									window.livewire.on('agregar-origen-hide', Msg => {
											$('#AgregarOrigen').modal('hide')
									})

									window.livewire.on('no-stock', Msg => {
										noty(Msg, 2)
									})

									window.livewire.on('volver-stock', id => {
									var stock = $("#q"+id).val();
									$("#qty"+id).val(stock);
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

									window.livewire.on('misma-sucursal', Msg => {
										noty(Msg, 2)
									})

									var total = $('#suma_totales').val();
									$('#ver_totales').html('Ventas: '+total);


	    });
	</script>

	@include('livewire.pos.scripts.shortcuts')
	@include('livewire.pos.scripts.events')
	@include('livewire.pos.scripts.general')
