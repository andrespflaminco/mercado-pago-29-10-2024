@inject('cart', 'App\Services\CartEtiquetas')

	<div>
	
	              <div class="page-header">
					<div class="page-title">
							<h4>Etiquetas</h4>
							<h6>Imprimir hoja de etiquetas</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
				 			<a  href="{{ url('descargas') }}" class="btn btn-added">
                			Ver etiquetas a descargar
                			</a>
						</div>
					</div>
					
		
        
        @if(session('status'))
       
       <div class="card" style="padding:20px !important;">
			<h2>Tu accion se ha completado con éxito.</h2>
            <p>{{ session('status') }} </p>
            <div style="margin-top: 15px;">
                <a href="{{ url('etiquetas') }}" class="btn btn-outline-success">
                    Seguir generando etiquetas
                </a>
                <a href="{{ url('descargas') }}" class="btn btn-success">
                    Ver descargas
                </a>
            </div>
		</div>

        @else

        <div class="col-sm-12 col-md-12">
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
        <button type="button" class="btn btn-dark" wire:click="AbrirFiltrosAccionEnLote"> + Agregar en lote</button>

		</div>

		<div class="col-lg-2 col-md-4 col-sm-12">


				</div>


		</div>

		</div>

        <div class="row">
            
		<div class="col-sm-12 col-md-8">
			<!-- DETALLES -->


			@include('livewire.etiquetas.partials.detail')


		</div>

		<div class="col-sm-12 col-md-4">
			<!-- TOTAL -->
			@include('livewire.etiquetas.partials.total')



		</div>
        </div>
        
        @endif

@include('livewire.etiquetas.variaciones')
@include('livewire.etiquetas.form')
@include('livewire.etiquetas.form-products')
@include('livewire.etiquetas.form2')
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


									window.livewire.on('no-stock', Msg => {
										noty(Msg, 2)
									})

									window.livewire.on('show-modal2', Msg =>{
											$('#theModal2').modal('show')
									})


									window.livewire.on('modal-show', msg => {
										$('#theModal1').modal('show')
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

									//events
									window.livewire.on('product-added', Msg => {
										$('#theModal').modal('hide')
										noty(Msg)
									})
									
									window.livewire.on('agregar-en-lote', Msg => {
										$('#ModalProductos').modal('show')
									})
									window.livewire.on('agregar-en-lote-hide', Msg => {
										$('#ModalProductos').modal('hide')
										noty(Msg)
									})


									var total = $('#suma_totales').val();
									$('#ver_totales').html('Ventas: '+total);


	    });
	</script>

	@include('livewire.pos.scripts.shortcuts')
	@include('livewire.pos.scripts.events')
	@include('livewire.pos.scripts.general')
