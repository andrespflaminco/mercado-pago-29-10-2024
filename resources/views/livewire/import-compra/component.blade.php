
<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>Módulo de Importar Compra</b>
                </h4>

            </div>


            <div class="widget-content">


                  <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"  x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">




                <div class="row mt-5">
                    <div class="col-sm-8 col-md-6">
                        <div style="height: 60px !important;" class="form-group custom-file">
                            <label class="custom-file-label">Buscar excel</label>
                            <input type="file" class="custom-file-input form-control" wire:model="fileCompra" accept=".xlsx, .xls,">

                            @error('fileCompra') <span class="text-danger er">{{ $message}}</span>@enderror

                            <div  class="d-flex justify-content-between mx-5 mt-3 mb-5">

                            </div>

                        </div>

                    </div>



                    <div class="col-sm-4 col-md-1 text-right">
                        <button wire:loading.attr="disabled" wire:click.prevent="uploadCompra()" {{$fileCompra =='' ? 'disabled' : ''}} class="btn btn-dark">Importar</button>
                    </div>
                    

                </div>
                <input type="checkbox" wire:model.defer="actualizar_costos"> Actualizar los costos en el catalogo
                <!-- Primary -->
                <div hidden  class="progress br-10">
                    <div x-show="{isUploading:true}" class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <br>
                <br>
                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
                <!-- display validation errors-->
                @if(count($errors->getMessages()) > 0)
                <div class="alert alert-danger alert-dismissible" role="alert">

                  <h5><strong>Se han encontrado errores en el archivo a importar:</strong></h5>
                  <br>


                    <ul>
                        @foreach($errors->getMessages() as $errorMessages)
                        @foreach($errorMessages as $errorMessage)
                        <li>
                          <h6>{{ $errorMessage }}</h6>

                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        </li>
                        @endforeach
                        @endforeach
                    </ul>
                </div>@endif


                </div>

            </div>
            
            
            <div class="widget-content">
                <br>
                <p><b> Ejemplo de excel a importar: </b></p>
                <table class="table  mt-1">
                    <thead>
                        <tr>
                            <td>CODIGO</td>
                            <td>CODIGO VARIACION</td>
                            <td>NOMBRE</td>
                            <td>COSTO</td>
                            <td>CANTIDAD</td>
                            <td>IVA</td>
                        </tr>
                    </thead>
                    <tdobdy>
                        <tr>
                            <td>P1</td>
                            <td></td>
                            <td>Producto Simple</td>
                            <td>1000</td>
                            <td>13</td>
                            <td></td>
                        </tr>
                         <tr>
                           <td>P2</td>
                            <td>NL</td>
                            <td>Producto Variable</td>
                            <td>1000</td>
                            <td>7</td>
                            <td>0.21</td>
                        </tr>
                    </tdobdy>
                </table>
                
                	<a  href="{{ url('report/excel-compras') }}">

						 Descargar excel de ejemplo </a>
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
		
		
	  window.livewire.on('import-wc', msg => {
      swal({
            title: 'IMPORTAR LOS REGISTROS TAMBIEN EN SU WOCOMMERCE',
            padding: '2em',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'SIGUIENTE'
          }).then(function(result) {
				if (result.value) {
					window.livewire.emit('wc', msg)
					swal.close()
				}

			})
		});
		
		





});
</script>
