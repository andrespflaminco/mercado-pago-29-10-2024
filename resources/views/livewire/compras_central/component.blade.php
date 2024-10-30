@inject('cart', 'App\Services\CartCompraCentral')
    
    <div>
	@if($muestra_carrito == 0)
    
    <div class="page-header">
		<div class="page-title">
			<h4>NUEVA COMPRA</h4>
			<h6>Agregue productos a su carrito</h6>
		</div>
		<div class="page-btn">
			<a href="{{ url('compras-resumen') }}" class="btn btn-added">
			Volver al resumen
			</a>
		</div>
	</div>
	
	<div class="row">
	    
	<div class="col-md-4 col-sm-12">
	    <label>Buscar un producto</label>
		<input
		style="font-size:14px !important;"
		type="text"
		class="form-control"
		placeholder="Buscar un producto"
		wire:model="search"
		/>
	</div>
	
	<div class="col-md-4 col-sm-12">
	<label>Buscar por categoria</label>
	<select class="form-control" wire:model="categoria_search">
	    <option value="0">Todas las categorias</option>
	    <option value="1">Sin categoria</option>
	   @foreach($categorias as $c)
	   <option value="{{$c->id}}">{{$c->name}}</option>
	   @endforeach
	</select>
	
	</div>
	</div>					

    <div style="margin-top:2%;" class="row">
                 @foreach($prod as $product)
                    <div class="col-md-2 col-sm-6 mb-3">
                    <button value="{{$product->barcode}}" id="code{{$product->barcode}}"  wire:click="$emit('scan-code', $('#code{{$product->barcode}}').val())" wire:click.lazy="selectProduct"
		            class="btn btn-light" title="Click en el producto">
                        <div id="product-item" class="product-item">
                            <div id="product-item-image" class="product-item-image">
                                
                                @if($product->image)
                                  <img style="width:100% !important;" src="{{ asset('storage/products/' . $product->image ) }}" alt="{{$product->name}}"
                                        class="img-fluid">
                                @else
                                 <img style="width:100% !important;" src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" class="img-fluid">
                                @endif
                                <div id="cart-icon" class="cart-icon">

                                </div>
                                @if($product->stock < 1)
                                <div hidden class="agotado">
                                  Agotado
                                </div>
                                @endif
                            </div>
                            <div class="product-item-info">
                                <br>
                                <h6>Cod: {{$product->barcode}}</h6>
                                 <h5>{{$product->name}}</h5>
                                <div class="descripcion">
                                  {{$product->category}}
                                </div>
                                <br>
                                
                                 <!---- si el producto es variable ---->
                                @if($product->producto_tipo == "v")
                                
                                Seleccione una variacion
                                @endif
                                
                                @if($product->producto_tipo == "s")
                                
                                $ {{$product->precio_interno}}
                                
                                 @endif
                                
                                </span> <del hidden>$999</del>
                            </div>
                        </div>
                  
                </button>
                    </div>
                 @endforeach



                </div>

    <footer class="footer">
    <a href="javascript:void(0);" wire:click="MuestraCarrito" style="
    position: fixed;
    bottom: 4%;
    right: 2%;
    cursor: pointer;
    border-radius: 5PX;
    padding: 15px;
    background-color: #e33747;
    color: #fff;
    font-size: 18px;
    text-align: left;">
       <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="feather feather-shopping-bag"
    >
        <path d="M2 5l2-2h16a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5zm2 0V3m0 0L.01 5M22 5V3m0 0l2 2M6 9a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm12 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm-6 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" />
    </svg>
    
    $   {{$monto_total}}
    </a>
</footer>

    @include('livewire.compras_central.form')
    @include('livewire.compras_central.variaciones')
    @include('livewire.compras_central.form2')
    
    @else
    <div class="row">
    <div class="col-sm-12 col-md-12">
    
    <div class="page-header">
		<div class="page-title">
			<h4>CARRITO DE COMPRAS</h4>
			<h6>Confirme su compra</h6>
		</div>
		<div class="page-btn">
			<a  href="javascript:void(0)" wire:click=OcultaCarrito() class="btn btn-added">
		    Volver al catalogo
			</a>
		</div>
	</div>

			</div>
    <div class="col-sm-12 col-md-8">
	<!-- DETALLES -->
	@include('livewire.compras_central.partials.detail')
	</div>

	<div class="col-sm-12 col-md-4">
	<!-- TOTAL -->
	@include('livewire.compras_central.partials.total')
	</div>    
    </div>
    
    
    @endif
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
									
									window.livewire.on('hide-modal', Msg => {
										$('#theModal').modal('hide')
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
