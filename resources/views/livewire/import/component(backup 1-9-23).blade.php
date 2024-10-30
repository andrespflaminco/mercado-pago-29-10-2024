
<div class="row sales layout-top-spacing">
    
             

    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            
             <div class="widget-content">

                <div x-data="{ isUploading : false }">


                <div class="row mt-5">
                    <div class="col-sm-8 col-md-6">
                        <div class="form-group custom-file">
                            <label class="custom-file-label">Buscar excel PRODUCTOS</label>
                            <input type="file" class="custom-file-input form-control" wire:model="fileProducts" accept=".xlsx, .xls,">

                            @error('fileProducts') <span class="text-danger er">{{ $message}}</span>@enderror

                            <div  class="d-flex justify-content-between mx-5 mt-3 mb-5">

                            </div>

                        </div>

                    </div>



                    <div class="col-sm-4 col-md-1 text-right">
                        <button wire:loading.attr="disabled" wire:click.prevent="ValidateProducts()" {{$fileProducts =='' ? 'disabled' : ''}} class="btn btn-dark">Importar</button>
                    </div>

                </div>
                <!-- Primary -->
                <div hidden  class="progress br-10">
                    <div x-show="{isUploading:true}" class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>


                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
                <!-- display validation errors-->
                @if(count($validacion_errores) > 0)
                <div class="alert alert-danger alert-dismissible" role="alert">

                  <h5><strong>Se han encontrado errores en el archivo a importar:</strong></h5>
                  <br>


                    <ul>
                        @foreach($validacion_errores as $errorMessage)
                        <li>
                          <h6>{{ $errorMessage }}</h6>

                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        </li>
                        @endforeach
                    </ul>
                    
                </div>
                
                <p>
                     <button hidden type="button" class="btn btn-danger" wire:click="" name="submit"> CANCELAR IMPORTACION </button>
                    <button hidden type="button" class="btn btn-success" wire:click="parseImport" name="submit"><i class="fa fa-check"></i> IMPORTAR OMITIENDO LOS ERRORES </button>
                   
                    
                </p>
                
                @endif


                </div>

            </div>
            
            
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>Módulo de Importar Catálogos</b>
                </h4>

            </div>

            <div class="widget-content">
            <p><b> Descargar Excel de ejemplo: </b> <button wire:click="DescargarExcelEjemplo()" class="btn btn-success">Descargar</button> </p>
            </div>
            
            
           		<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C">
							<tr>
								<th class="table-th text-white">Tipo</th>
								<th class="table-th text-white">Fecha de importacion</th>
								<th class="table-th text-white text-center">Estado</th>
								<th class="table-th text-white text-center">Excel Importado</th>
							</tr>
						</thead>
						<tbody>
							@foreach($importaciones as $r)
							<tr>
								<td>
									<h6>
									    Importar Catalogo
									</h6>
								</td>
								
								<td>
									<h6>{{\Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i')}} hs.</h6>
								</td>
									<td class="text-center">
									    @if($r->estado == 0 )
											<span class="badge badge-secondary text-uppercase">A la espera</span>
								        @endif
								        @if($r->estado == 1 )
											<span class="badge badge-warning text-uppercase">En preparacion</span>
								        @endif
								        @if($r->estado == 2 )
								       <span class="badge badge-success text-uppercase">Importado</span>
								        @endif
									</td>
									<td class="text-center">
							        @if($r->estado == 2 )
								        <a href="javascript:void(0)" wire:click="Descargar('{{$r->id}}')" class="btn btn-dark" title="Descargar">
										<i class="fas fa-download"></i>
										
									    </a>
								        @endif

									</td>
								
							</tr>
							@endforeach
						</tbody>
					</table>
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
