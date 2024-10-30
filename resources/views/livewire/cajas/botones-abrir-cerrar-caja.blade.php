<div class="page-btn  d-lg-flex d-sm-block">
						   
						    <!---- si es casa central -------->
						    @if(Auth::user()->sucursal != 1 )
                            @include('livewire.cajas.botones-abrir-cerrar-caja-casa-central')
						    
						    @else
						    
						    <!---- si es sucursal -------->
                            @include('livewire.cajas.botones-abrir-cerrar-caja-sucursal')
						    @endif

</div>