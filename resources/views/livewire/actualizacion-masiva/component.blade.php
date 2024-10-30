<div >	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Actualizacion masiva</h4>
							<h6>Elija los productos a actualizar</h6>
						</div>
						<div class="page-btn">               											    
                		</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					<div class="card-body">
                    
                    <!-- Wizard -->
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                 
                                </div>
                                <div class="card-body">

                                    <div id="progrss-wizard" class="twitter-bs-wizard">
                                        <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
                                            <li class="nav-item">
                                                <a class="nav-link">
                                                    <div class="step-icon" style="{{$logo_paso1}}" data-bs-toggle="tooltip" data-bs-placement="top" title="User Details">
                                                        Filtro
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link">
                                                    <div class="step-icon" style="{{$logo_paso2}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Address Detail">
                                                        Seleccion
                                                    </div>
                                                </a>
                                            </li>
                                            
                                            <li class="nav-item">
                                                <a class="nav-link">
                                                    <div class="step-icon" style="{{$logo_paso3}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Payment Details">
                                                        Actualizacion
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- wizard-nav -->

                                        <div class="mt-4">
                                            @include('livewire.actualizacion-masiva.paso1')
                                            @include('livewire.actualizacion-masiva.paso2')
                                            @include('livewire.actualizacion-masiva.paso3')
                                            @include('livewire.actualizacion-masiva.paso4')
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
						<!-- /Wizard -->

	
					</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					
					
					@endif 
					
					
					</div>
					
<script>
					    
	function ConfirmarActualizar(nro) {

		swal({
			title: 'CONFIRMAR',
			text: 'CONFIRMAS ACTUALIZAR LOS PRODUCTOS SELECCIONADOS?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('Actualizar', nro)
				swal.close()
			}

		})
	}
</script>


<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('progressUpdated', (current, total) => {
            let progress = (current / total) * 100;
            document.querySelector('.progress-bar').style.width = `${progress}%`;
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', progress);
            document.querySelector('.progress-bar').textContent = `${Math.round(progress)}%`;
        });
    });
</script>

<script>
    	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('show-modal', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('category-added', msg => {
			noty(msg)
		});
		window.livewire.on('category-updated', msg => {
			noty(msg)
		});
		
		window.livewire.on('msg', msg => {
		swal({
			title: 'EXITO',
			text: 'SE HAN ACTUALIZADO CON EXITO LOS REGISTROS',
			type: 'success'
		}).then(function(result) {
				swal.close()
		})
		});


	});

</script>