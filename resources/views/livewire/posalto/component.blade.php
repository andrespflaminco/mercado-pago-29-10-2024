
<div>

	<div class="row layout-top-spacing">
		<div class="col-sm-12 col-md-12">
			<div id="connect-sorting" class="connect-sorting">

				<div style="padding-right:5px !important; padding-left:5px !important;" class="col-lg-1 col-md-4 col-sm-12">

						<input id="code" type="text"
							wire:keydown.enter.prevent="$emit('scan-code', $('#code').val())"
							 class="form-control search-form-control  ml-lg-auto"
			placeholder="Cod." style="place">

				 </div>

				<div class="col-lg-3 col-md-4 col-sm-12">
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

										@endif
								</div>
						@endif

				</div>


				<div class="col-lg-3 col-md-4 col-sm-12">
				<div style="margin-bottom: 0 !important;" class="input-group mb-4">
					<div class="input-group-prepend">
						<span class="input-group-text input-gp">
							<i class="fas fa-users"></i>
						</span>
					</div>
						<input
								style="font-size:14px !important;"
								type="text"
								class="form-control"
								placeholder="Seleccione un cliente"
								wire:model="query"
								wire:keydown.escape="resetCliente"
								wire:keydown.tab="resetCliente"
								wire:keydown.enter="selectContact"
						/>
						</div>



						@if(!empty($query))
								<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>

								<div style="position:absolute; z-index: 999 !important;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
										@if(!empty($contacts))
												@foreach($contacts as $i => $contact)
												<a href="javascript:void(0)"
												wire:click="selectContact({{$contact['id']}})"
												class="btn btn-light" title="Edit">{{ $contact['nombre'] }}
										</a>


												@endforeach
												<a href="javascript:void(0)"   class="btn btn-dark" wire:click.prevent="AgregarClienteModal()" >Agregar otro cliente</a>

										@else

										@endif
								</div>
						@endif

				</div>




<div class="col-lg-3 col-md-4 col-sm-12">
	<div style="margin-bottom: 0 !important;" class="input-group mb-4">
		<div class="input-group-prepend">
			<span class="input-group-text input-gp">
				<i class="fas fa-user"></i>
			</span>
		</div>

				<select  style="font-size:14px !important;" wire:model.lazy='usuario_activo' class="form-control">

						@foreach($user as $usuario)
						<option style="font-size:14px !important;" value="{{$usuario->id}}">{{$usuario->name}}</option>
						@endforeach
					</select>

				</div>

		</div>

		<div class="col-lg-2 col-md-4 col-sm-12">
			@if($estado_pedido == '')
								<button wire:click="selectEstado(1)" type="button" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-dark" > Estado </button>

									@endif

									@if($estado_pedido == 'Pendiente')
									<button wire:click="selectEstado(1)" type="button" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-warning" > Pendiente </button>

									@endif


										@if($estado_pedido == 'En proceso')
									<button wire:click="selectEstado(1)" type="button" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-secondary" > En proceso </button>

											@endif


											@if($estado_pedido == 'Entregado')

											<button type="button" wire:click="selectEstado(1)" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-success" > Entregado </button>
												@endif




				</div>


		</div>
		</div>

		<div class="col-sm-12 col-md-8">
			<!-- DETALLES -->
			@include('livewire.posalto.partials.sales-detail')
			@include('livewire.posalto.partials.form')

			@include('livewire.posalto.partials.detail')



		</div>

		<div class="col-sm-12 col-md-4">
			<!-- TOTAL -->
			@include('livewire.posalto.partials.total')


			<!-- DENOMINATIONS -->
			@include('livewire.posalto.partials.coins')


		</div>
		@include('livewire.reports.sales-detail2')
		@include('livewire.reports.sales-detail3')
		@include('livewire.posalto.form')
		@include('livewire.posalto.partials.form-alto')
		@include('livewire.posalto.estado-pedido-pos')

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
						window.livewire.on('cliente-agregado', Msg => {
								$('#theModal-cliente').modal('hide')
								noty(Msg)
						})

								window.livewire.on('agregar-cliente', Msg => {
										$('#theModal-cliente').modal('show')
								})

								window.livewire.on('show-modal', Msg =>{
										$('#modalDetails').modal('show')
								})

								window.livewire.on('show-modal-alto', Msg =>{
										$('#modalDetailsAlto').modal('show')
								})

								window.livewire.on('show-modal2', Msg =>{
										$('#modalDetails2').modal('show')
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
