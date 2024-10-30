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

						<div class="card-body">
						    <div class="row mb-3">
						        <div class="col-4">
						            <label>Tamano de impresora termica (ticket)</label>
						            <select class="form-control" wire:model.defer="size">
						                <option value="80"> 80 mm</option>
						                <option value="58"> 58 mm</option>
						            </select>
						        </div>
						        
						        <div class="col-8">
						        </div>
						        <br><br>
						        <div class="col-4 mt-4">
						            <input type="checkbox" wire:model.defer="muestra_cta_cte"> Muestra resumen de cuenta corriente en ticket
						        </div>
						        <div class="col-8">
						        </div>
                                <br><br>
                            <div class="col-4 mt-4">
                                <button class="btn btn-submit" wire:click="CreateOrUpdate">GUARDAR</button>
                            </div>
						    </div>
						</div>
					</div>
</div>

<script>
    	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('msg', msg => {
			noty(msg)
		});

	});

</script>
