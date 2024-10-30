<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Configuracion de impresion</h4>
							<h6></h6>
						</div>
						<div class="page-btn">               											    
						</div>
					</div>
					
                    
					<!-- /product list -->
                     @include('common.tab-configuracion')
					<div class="card">
                    <div style="margin: 25px 25px 0px 25px; !important;" class="row">
            				<a class="btn btn-light" style="width: 110px !important;  border: none;  background: transparent;  border-bottom: solid 1px #eee;  margin: 10px; {{$configuracion_ver == 'codigos' ? 'border-bottom: 3px solid #333;' : '' }} " href="javascript:void(0)" wire:click="CambiarConfiguracionVer('codigos')" > Codigos  </a>
            				<a hidden class="btn btn-light" style="width: 110px !important; border: none;  background: transparent; border-bottom: solid 1px #eee;  margin: 10px; {{$configuracion_ver == 'precios' ? 'border-bottom: 3px solid #333;' : '' }} " href="javascript:void(0)" wire:click="CambiarConfiguracionVer('precios')" > Precios </a>
            				<a class="btn btn-light" style="width: 110px !important;  border: none;  background: transparent;  border-bottom: solid 1px #eee; margin: 10px; {{$configuracion_ver == 'stock' ? 'border-bottom: 3px solid #333;' : '' }} " href="javascript:void(0)" wire:click="CambiarConfiguracionVer('stock')" > Stock </a>
            	    </div> 
            	
            	
						<div class="card-body">

                        @include('livewire.products.form-configuracion')    

						</div>
					</div>
</div>

<script>
    	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('msg', msg => {
			noty(msg)
		});
		
		window.livewire.on('product-added', msg => {
			noty(msg)
		});
		

	});

</script>
