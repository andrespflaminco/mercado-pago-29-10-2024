    <div wire:ignore.self class="layout-px-spacing">
                
                <div class="page-header">
						<div class="page-title">
							<h4>Configuracion del comercio</h4>
							<h6></h6>
						</div>
					</div>
					
					@if(session('status'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>{{ session('status') }}  </strong> 
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
                    @endif
                         
					<!-- /add -->
            		@include('common.tab-configuracion')
                    	
					<div class="card">
            			

        	
						<div class="card-body">
							<div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Informacion general</h6>
                                            <br>
                                            @foreach($data as $d)
                                            <div class="row">
                                                <div class="col-lg-12 mx-auto">
                                                    <div class="row">
                                                        <div class="col-xl-12 col-lg-12 col-md-8 mt-md-0 mt-4">
                                                            <div class="form">
                                                              <div class="row">

                                                              <div class="col-sm-12 col-md-8">
                                                              	<div class="form-group">
                                                              		<label >Nombre del comercio</label>
                                                              		<input type="text" wire:model.lazy="name"
                                                              		class="form-control" placeholder="ej: Luis Fax" >
                                                              		@error('name') <span class="text-danger er">{{ $message}}</span>@enderror
                                                              	</div>
                                                              </div>
                                                              <div class="col-sm-12 col-md-4">
                                                              	<div class="form-group">
                                                              		<label >Teléfono</label>
                                                              		<input type="text" wire:model.lazy="phone"
                                                              		class="form-control" placeholder="ej: 351 115 9550" maxlength="10" >
                                                              		@error('phone') <span class="text-danger er">{{ $message}}</span>@enderror
                                                              	</div>
                                                              </div>
                                                              <div class="col-sm-12 col-md-6">
                                                              	<div class="form-group">
                                                              		<label >Email</label>
                                                              		<input type="text" wire:model.lazy="email"
                                                              		class="form-control" placeholder="ej: luisfaax@gmail.com"  >
                                                              		@error('email') <span class="text-danger er">{{ $message}}</span>@enderror
                                                              	</div>
                                                              </div>
                                                              <div class="col-sm-12 col-md-6">
                                                              	<div class="form-group">
                                                              		<label >Contraseña</label>
                                                              		<input type="password" wire:model.lazy="password"
                                                              		class="form-control"   >
                                                              		@error('password') <span class="text-danger er">{{ $message}}</span>@enderror
                                                              	</div>
                                                              </div>
                                                              <div hidden class="col-sm-12 col-md-6">
                                                                <div class="form-group">
                                                                  <label >Provincia</label>
                                                                  <select type="text" wire:model.lazy="id_provincia"                                                                   class="form-control" >
                                                                  @foreach($provincias as $p)
                                                                  <option value="{{$p->id}}">{{$p->provincia}}</option>
                                                                  @endforeach
                                                                  </select>
                                                                  @error('provincia') <span class="text-danger er">{{ $message}}</span>@enderror
                                                                </div>
                                                              </div>


                                                              <div hidden class="col-sm-12 col-md-6">
                                                                <div class="form-group">
                                                                  <label >Ciudad</label>
                                                                  <input type="text" wire:model.lazy="ciudad"  class="form-control" >
                                                                  @error('ciudad') <span class="text-danger er">{{ $message}}</span>@enderror
                                                                </div>
                                                              </div>
                                                              <div hidden class="col-sm-12 col-md-6">
                                                                <div class="form-group">
                                                                  <label >Domicilio</label>
                                                                  <input type="text" wire:model.lazy="domicilio_comercial"                                                                   class="form-control" placeholder="ej: Cordoba"  >
                                                                  @error('ciudad') <span class="text-danger er">{{ $message}}</span>@enderror
                                                                </div>
                                                              </div>
                                                          
                                                            
                                                            <div class="col-sm-12 col-md-6">
                                                            <div class="upload mt-4 pr-md-4">
                                                                  @if($image != null)
                                                                    <img id="previewImageMiComercio" width="100" class="rounded" src="{{ asset('storage/users/'.$image) }}">
                                                                    <br>
                                                                @else
                                                                    <!-- Si no hay una imagen previa guardada -->
                                                                    <img id="previewImageMiComercio" width="100" class="rounded" style="display: none;">
                                                                    <br>
                                                                @endif
                                                                <label >Logo del comercio</label>
                                                             <input type="file" wire:model.defer="image" accept="image/x-png, image/jpeg, image/gif" class="form-control" id="imageInputMiComercio">
                                                             @error('image') <span class="text-danger er">{{ $message}}</span>@enderror
                                                            </div>
                                                        </div>

                                                              </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                </div>

                                <br>
                                <br>
                                <div hidden class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <div  class="section contact">
                                        <div class="info">
                                            <h5 class="">Datos de facturacion</h5>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12 mx-auto">
                                                    <div class="row">

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Razon social</label>
                                                      		<input type="text" wire:model.lazy="razon_social"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('razon_social') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >CUIT</label>
                                                      		<input type="text" wire:model.lazy="cuit"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('cuit') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Condicion ante el IVA</label>
                                                      		<select wire:model.lazy="condicion_iva" class="form-control">
                                                      			<option value="Elegir" selected>Elegir</option>
                                                      			<option value="IVA Responsable inscripto" >IVA Responsable inscripto</option>
                                                      			<option value="IVA exento" >IVA exento</option>
                                                      			<option value="Monotributo" >Monotributo</option>

                                                      		</select>
                                                      		@error('condicion_iva') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Ingresos brutos</label>
                                                      		<input type="text" wire:model.lazy="iibb"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('iibb') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                        <div class="form-group">
                                                          <label >Punto de venta</label>
                                                          <input type="text" wire:model.lazy="pto_venta"
                                                          class="form-control" placeholder=""  >
                                                          @error('pto_venta') <span class="text-danger er">{{ $message}}</span>@enderror
                                                        </div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                        <div class="form-group">
                                                          <label >Fecha de inicio de actividades</label>
                                                          <input type="date" wire:model.lazy="fecha_inicio_actividades"
                                                          class="form-control" >
                                                          @error('fecha_inicio_actividades') <span class="text-danger er">{{ $message}}</span>@enderror
                                                        </div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >IVA por defecto</label>
                                                      		<select wire:model.lazy="iva_defecto"
                                                      		class="form-control">
                                                              <option value="0">Sin IVA</option>
                                                              <option value="0.105">10,5%</option>
                                                              <option value="0.21">21%</option>
                                                              <option value="0.27">27%</option>
                                                          </select>
                                                      		@error('iva_defecto') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>
                                                      
                                                       <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Relacion Precio -> Iva</label>
                                                      		<select wire:model.lazy="relacion_precio_iva"
                                                      		class="form-control">
                                                      		  <option value="0">Sin IVA</option>
                                                              <option value="1">IVA + Precio</option>
                                                              <option value="2">IVA incluido en el precio</option>
                                                          </select>
                                                      		@error('relacion_precio_iva') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <br>


                            </div>
								<div class="row">
									<div class="col-lg-12">
										<a href="javascript:void(0);" wire:click.prevent="Store()" class="btn btn-submit me-2">Guardar cambios</a>
										<a hidden href="javascript:void(0);" class="btn btn-cancel">Cancel</a>
									</div>
								</div>
							</div>
							
				    </div>
				    </div>
				    </div>

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="assets/js/libs/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/popper.min.js"></script>

    <script src="assets/js/custom.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!--  BEGIN CUSTOM SCRIPTS FILE  -->

    <script src="plugins/dropify/dropify.min.js"></script>
    <script src="plugins/blockui/jquery.blockUI.min.js"></script>
    <!-- <script src="plugins/tagInput/tags-input.js"></script> -->
    <script src="assets/js/users/account-settings.js"></script>
    <!--  END CUSTOM SCRIPTS FILE  -->
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            window.livewire.on('user-added', Msg => {
                $('#theModal').modal('hide')
                noty(Msg)
            })
            window.livewire.on('user-updated', Msg => {
                $('#theModal').modal('hide')
                noty(Msg)
            })
            window.livewire.on('user-deleted', Msg => {
                noty(Msg)
            })
            window.livewire.on('hide-modal', Msg => {
                $('#theModal').modal('hide')
            })
            window.livewire.on('show-modal', Msg => {
                $('#theModal').modal('show')
            })
            window.livewire.on('hide-modal-facturacion', Msg => {
                $('#theModalFacturacion').modal('hide')
            })
            window.livewire.on('show-modal-facturacion', Msg => {
                $('#theModalFacturacion').modal('show')
            })
            window.livewire.on('user-withsales', Msg => {
                noty(Msg)
            })
            
            


        });

        function Confirm(id)
        {

            swal({
                title: 'CONFIRMAR',
                text: '¿CONFIRMAS ELIMINAR EL REGISTRO?',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cerrar',
                cancelButtonColor: '#fff',
                confirmButtonColor: '#3B3F5C',
                confirmButtonText: 'Aceptar'
            }).then(function(result) {
                if(result.value){
                    window.livewire.emit('deleteRow', id)
                    swal.close()
                }

            })
        }
    </script>
    
    <!-- Script para la previsualización -->
<script>
    document.getElementById('imageInputMiComercio').addEventListener('change', function(event) {
        const file = event.target.files[0]; // Obtener el archivo
        const previewImage = document.getElementById('previewImageMiComercio'); // Seleccionar el elemento img

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result; // Asignar el src a la imagen
                previewImage.style.display = 'block'; // Mostrar la imagen
            }

            reader.readAsDataURL(file); // Leer el archivo como DataURL
        }
    });
</script>
