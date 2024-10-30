@inject('cart', 'App\Services\CartProduccion')

	<div class="row layout-top-spacing">
		<div class="col-sm-12 col-md-12">

	        <div class="page-header">
				<div class="page-title">
					<h4>Produccion</h4>
					<h6>Agregue productos a producir</h6>
				</div>
					<div class="page-btn">   
						<a  class="btn btn-dark" href="{{ url('produccion') }}">Volver al resumen </a>
            	    </div>
			</div>
        </div>
		
		<div class="col-sm-12 col-md-12">
			<div class="row">


				<div class="col-lg-2 col-md-4  col-sm-12">

					<div class="input-group mb-0">
						<div class="input-group-prepend">
							<span style="height:100%;" class="input-group-text input-gp">
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
											<button value="{{$product['barcode']}}" id="code{{$product['barcode']}}"  wire:click="$emit('scan-code', $('#code{{$product['barcode']}}').val())" wire:click.lazy="selectProduct"
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

	<div class="input-group mb-0">
		<div class="input-group-prepend">
			<span class="input-group-text input-gp">
				ESTADO:
			</span>
		</div>

		<select wire:model="estado" class="form-control" >
			<option value="Elegir" selected>Elegir</option>
			<option value="1">Pendiente</option>
			<option value="2">En proceso</option>
			<option value="3">Terminado</option>
			<option value="5">Cancelado</option>

		</select>
	</div>


		</div>
						<div class="col-lg-3 col-md-4 col-sm-12">
				
				<div class="input-group mb-0">
        		<div class="input-group-prepend">
        			<span class="input-group-text input-gp">
        				FECHA PROD.:
        			</span>
        		</div>
                <input type="date" class="form-control" wire:model="fecha_produccion">
                </div>
				</div>

		<div class="col-lg-2 col-md-4 col-sm-12">


				</div>


		</div>

		</div>

		<div class="col-sm-12 col-md-8">
			<!-- DETALLES -->


			@include('livewire.produccion-nueva.partials.detail')


		</div>

		<div class="col-sm-12 col-md-4">
			<!-- TOTAL -->
			@include('livewire.produccion-nueva.partials.total')



		</div>


@include('livewire.produccion-nueva.form')
@include('livewire.produccion-nueva.form2')
@include('livewire.produccion-nueva.variaciones')
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
									
									window.livewire.on('variaciones', Msg =>{
											$('#Variaciones').modal('show')
									})
									
									window.livewire.on('variaciones-hide', Msg =>{
											$('#Variaciones').modal('hide')
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
									
		window.livewire.on('mensaje', msg => {

			swal({
				title: 'ATENCION',
				text: msg,
				type: 'warning',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'ACEPTAR'
			}).then(function(result) {
				swal.close()
			})

		});	


	    });
	    

	</script>

	@include('livewire.pos.scripts.shortcuts')
	@include('livewire.pos.scripts.events')
	@include('livewire.pos.scripts.general')
