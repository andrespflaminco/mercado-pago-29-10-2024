<div style="display:{{$ver_configuracion == 1? 'block' : 'none';}};">

<div>
<div class="page-header">

						<div class="page-title">
						    
						    
							<h4>CONFIGURACIONES </h4>
							<h6>Setea las configuraciones relacionada a los productos. </h6>
						
						</div>
					</div>
                    <!-- /add -->

            			
                <div class="card">
                <ul class="nav nav-tabs  mb-0">
            				<li class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'codigos' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('codigos')" > CODIGOS  </a>
            				</li>
            				<li hidden class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'precios' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('precios')" > PRECIOS </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'stock' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('stock')" > STOCK </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'acciones_masivas' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('acciones_masivas')" > ACCIONES MASIVAS </a>
            				</li>
            	</ul>                
                @include('livewire.products-nuevo.form-configuracion')    
				</div>
				
				

				
</div>





</div>
