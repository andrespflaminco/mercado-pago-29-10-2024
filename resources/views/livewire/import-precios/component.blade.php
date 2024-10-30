<div>
    
    	                <div class="page-header">
					<div class="page-title">
							<h4>Actualizar precios precios</h4>
							<h6>Ingrese el excel para actualizar los precios de sus productos</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
						    <a class="btn btn-added"   href="{{ url('products-precios') }}">Volver</a>
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
						 <div class="widget-content">
						     
				               <div class="col-3">
                                <label for="">Lista de precios a importar</label>
                                <select class="form-control" wire:model="lista_id">
                                    <option value="Elegir">Elegir</option>
                                    <option value="1">Lista base</option>
                                  @foreach($listas as $l)
                                  <option value="{{$l->id}}">{{$l->nombre}}</option>
                                  @endforeach
                                </select>
                                @error('lista_id') <span class="text-danger er">{{ $message}}</span>@enderror
                              </div>


                  <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"  x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">




                <div class="row mt-5">
                    <div class="col-sm-8 col-md-6">
                        <div class="form-group custom-file">
                            <label class="custom-file-label">Buscar excel </label>
                            <input type="file" class="custom-file-input form-control" wire:model="fileListaPrecio" accept=".xlsx, .xls,">

                            @error('fileListaPrecio') <span class="text-danger er">{{ $message}}</span>@enderror

                            <div  class="d-flex justify-content-between mx-5 mt-3 mb-5">

                            </div>

                            <div x-show="isUploading">
                                <progress max="100" x-bind:value="progress"></progress>
                            </div>

                        </div>

                    </div>

                
              <div class="col-sm-12 col-md-12 text-left mb-3">
                    <button wire:loading.attr="disabled" wire:click.prevent="uploadLista()" {{$fileListaPrecio =='' ? 'disabled' : ''}} class="btn btn-dark">Importar</button>
                </div>
                
                
                </div>
                <!-- Primary -->
                <div hidden  class="progress br-10">
                    <div x-show="{isUploading:true}" class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>


                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
  

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