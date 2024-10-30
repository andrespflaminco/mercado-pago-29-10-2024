
<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>Importar stock en las sucursales</b>
                </h4>

            </div>


            <div class="widget-content">

              <div class="col-3">
                <label for="">Sucursales</label>
                <select class="form-control" wire:model="sucursal_id">
                    <option value="Elegir">Elegir</option>
                    <option value="1">Casa central</option>
                  @foreach($sucursales as $s)
                  <option value="{{$s->sucursal_id}}">{{$s->name}}</option>
                  @endforeach
                </select>

              </div>


                  <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"  x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">




                <div class="row mt-5">
                    <div class="col-sm-8 col-md-6">
                        <div class="form-group custom-file">
                            <label class="custom-file-label">Buscar excel</label>
                            <input type="file" class="custom-file-input form-control" wire:model="fileStockSucursales" accept=".xlsx, .xls,">

                            @error('fileProducts') <span class="text-danger er">{{ $message}}</span>@enderror

                            <div  class="d-flex justify-content-between mx-5 mt-3 mb-5">

                            </div>

                            <div x-show="isUploading">
                                <progress max="100" x-bind:value="progress"></progress>
                            </div>

                        </div>

                    </div>

                    <div class="col-sm-4 col-md-6 text-right">
                    </div>    

                    <div class="col-sm-4 col-md-1 text-right">
                        <button wire:loading.attr="disabled" wire:click.prevent="uploadLista()" {{$fileStockSucursales =='' ? 'disabled' : ''}} class="btn btn-dark">Importar</button>
                    </div>

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
