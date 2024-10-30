<div>
    
    	                <div class="page-header">
					<div class="page-title">
							<h4>Importar clientes</h4>
							<h6>Ingrese el excel para importar sus clientes</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
						    <a class="btn btn-added"   href="{{ url('clientes') }}">Volver al resumen de clientes</a>
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
						 <div class="widget-content">


                  <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"  x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">




                <div class="row mt-5">
                    <div class="col-sm-8 col-md-6">
                        <div class="form-group custom-file">
                            <label class="custom-file-label">Buscar excel CLIENTES</label>
                            <input type="file" class="custom-file-input form-control" wire:model="fileClientes" accept=".xlsx, .xls,">

                            @error('fileClientes') <span class="text-danger er">{{ $message}}</span>@enderror

                            <div  class="d-flex justify-content-between mx-5 mt-3 mb-5">

                            </div>

                            <div x-show="isUploading">
                                <progress max="100" x-bind:value="progress"></progress>
                            </div>

                        </div>

                    </div>
                <div class="col-sm-12 col-md-12 text-left">
                <p>Exportar lista de clientes actuales:
                <button wire:click.prevent="ExportarExcel()" class="btn btn-success">Exportar</button>
                </p>
                </div>
                
               
                
                <br><br>
                @if(count($validacion_errores) < 1)
                <div class="col-sm-12 col-md-12 text-left mt-5">
                    <button style="width: 200px !important;" wire:loading.attr="disabled" wire:click.prevent="ValidateClientes()" {{$fileClientes =='' ? 'disabled' : ''}} class="btn btn-dark">Importar</button>
                </div>
                @else 
                
                <div class="col-sm-12 col-md-12 text-left">
                <h6>Desea importar el excel saltando los registros con errores?</h6>
                    <button wire:loading.attr="disabled" wire:click.prevent="uploadCancelar()" {{$fileClientes =='' ? 'disabled' : ''}} class="btn btn-cancel">Cancelar</button>
                    <button wire:loading.attr="disabled" wire:click.prevent="uploadClientesConErrores()" {{$fileClientes =='' ? 'disabled' : ''}} class="btn btn-submit">Aceptar</button>
                </div>
               @endif
                <br>
                 @if($wc != null)
                 <br><br><br>
                 <div class="col-3 mt-3"><input type="checkbox" wire:model="importar_wocommerce">  Agregar clientes en Wocommerce</div>
                
                @endif
                
                </div>
                <!-- Primary -->
                <div hidden  class="progress br-10">
                    <div x-show="{isUploading:true}" class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>


                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
                <!-- display validation errors-->
                @if(count($errors->getMessages()) > 0)
                <div class="alert alert-danger alert-dismissible" role="alert">

                  <h5><strong>Se han encontrado errores en el archivo a importar:</strong></h5>
                  <br>


                    <ul class="danger">
                        @foreach($errors->getMessages() as $errorMessages)
                        @foreach($errorMessages as $errorMessage)
                        <li>
                          <h6>{{ $errorMessage }}</h6>

                        </li>
                        @endforeach
                        @endforeach
                    </ul>
                </div>
                
                @endif

                                             
                @if(0 < count($validacion_errores))   
                <div class="row mt-5">
                    <div class="col-sm-10 col-md-10">
                    <div class="form-group custom-file">   
                    <h5><strong>Se han encontrado errores en el archivo a importar</strong></h5>
                    </br>
                      <ul class="danger">
                      @foreach($validacion_errores as $errorMessage)
                            <li>
                            <h6>{{ $errorMessage }}</h6>
                            </li>
                      @endforeach
                      </ul>
                    </br>     
                                
                                                     
                 </div>
                </div> 
                </div>
                @endif

                </div>

<br>
<h6 hidden > Ejemplo del excel a importar:</h6>
<br>
							<div hidden class="table-responsive">
								<table class="table">
									<thead>
										<tr>
                						<th>NOMBRE</th>
                                        <th>CUIT</th>
                                        <th>TELEFONO</th>
                                        <th>EMAIL</th>
                                        <th>DIRECCION</th>
                                        <th>BARRIO</th>
                                        <th>LOCALIDAD</th>
                                        <th>PROVINCIA</th>
                                        <th>LISTA PRECIOS</th>
                                        <th>OBSERVACIONES</th>
										</tr>
									</thead>
									<tbody>
        							<tr>
    	                                <td>Juan perez</td>
                                        <td>203219111</td>
                                        <td>3512312321</td>
                                        <td>juanperez@gmail.com</td>
                                        <td>Bolivar 123</td>
                                        <td>Centro</td>
                                  		<td>Cordoba</td>
                                  		<td>Cordoba</td>
                                  		<td>Mayorista</td>
                                  		<td>Observacion sobre el cliente</td>
        							</tr>
        							</tbody>
								</table>
								</div>
            </div>
			</div>
			</div>
					
</div>

<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {

    window.livewire.on('import', msg => {
      swal({
            title: 'IMPORTACION EXITOSA!',
            type: 'success',
            padding: '2em'
          })
		});


    window.livewire.on('msg-no', msg => {
      swal({
            title: 'EL EXCEL ESTA VACIO',
            type: 'warning',
            padding: '2em'
          })
		});





});
</script>