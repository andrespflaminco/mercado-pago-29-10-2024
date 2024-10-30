
<div class="row card">

    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title mt-3">
                    <b>Módulo de Importar recetas</b>
                </h4>

            </div>


            <div class="widget-content">


                  <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"  x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">




                <div class="row mt-5">
                    <div class="col-sm-8 col-md-6">
                        <div class="form-group custom-file">
                            <label class="custom-file-label">Buscar excel RECETAS</label>
                            <input type="file" class="custom-file-input form-control" wire:model="fileRecetas" accept=".xlsx, .xls,">

                            @error('fileRecetas') <span class="text-danger er">{{ $message}}</span>@enderror

                            <div  class="d-flex justify-content-between mx-5 mt-3 mb-5">

                            </div>

                            <div x-show="isUploading">
                                <progress max="100" x-bind:value="progress"></progress>
                            </div>

                        </div>

                    </div>
                    <div class="col-sm-4 col-md-6 text-right"></div>


                    <div class="col-sm-4 col-md-1 text-right mb-3">
                    @if (count($validacion_errores) > 0)
                        <a href="{{ url('import-recetas') }}" class="btn btn-dark">Cancelar</a>
                    @else
                        <button wire:loading.attr="disabled" wire:click.prevent="ValidateProducts()" {{$fileRecetas =='' ? 'disabled' : ''}} class="btn btn-dark">Importar</button>
                    @endif
                    </div>

                </div>
                <!-- Primary -->
                <div hidden  class="progress br-10">
                    <div x-show="{isUploading:true}" class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>


                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
                <!-- display validation errors-->
                @if (count($validacion_errores) > 0)
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <h5>
                            <strong>Se han encontrado errores en el archivo a importar:</strong>
                        </h5>
                        <br>
                        <ul>
                            @foreach ($validacion_errores as $errorMessage)
                                <li>
                                    <h6>{{ $errorMessage }}</h6>
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <p>
                        <button hidden type="button" class="btn btn-danger" wire:click="" name="submit">
                            CANCELAR IMPORTACION
                        </button>
                        <button hidden type="button" class="btn btn-success" wire:click="parseImport" name="submit">
                            <i> class="fa fa-check"></i> IMPORTAR OMITIENDO LOS ERRORES
                        </button>
                    </p>
                
                    </div>
                @endif
          


                </div>

            </div>
            <a href="javascript:void(0)" wire:click="ExportarRecetas" class="btn btn-success">Exportar Excel de Recetas</a>
            <div class="widget-content">
                <p><b> Unidades de medida </b></p>
                <table class="table table-bordered table-striped  mt-1">
                    <thead>
                        <tr>
                            <td>Tipo de unidad de medida</td>
                            <td>Nombre</td>
                            <td>Nomenclatura a utilizar</td>
                        </tr>
                    </thead>
                    <tdobdy>
                        <tr>
                            <td>Peso</td>
                            <td>Kilogramo</td>
                            <td>KG</td>
                        </tr>
                         <tr>
                            <td>Peso</td>
                            <td>Gramo</td>
                            <td>GR</td>
                        </tr>
                         <tr>
                            <td>Peso</td>
                            <td>Miligramo</td>
                            <td>MG</td>
                        </tr>
                         <tr>
                            <td>Liquidos</td>
                            <td>Litro</td>
                            <td>LTRS</td>
                        </tr>
                         <tr>
                            <td>Liquidos</td>
                            <td>Mililitros</td>
                            <td>ML</td>
                        </tr>
                         <tr>
                            <td>Unidades</td>
                            <td>Unidad</td>
                            <td>UN</td>
                        </tr>
                         <tr>
                            <td>Unidades</td>
                            <td>Docena</td>
                            <td>DOC</td>
                        </tr>
                    </tdobdy>
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





});
</script>
