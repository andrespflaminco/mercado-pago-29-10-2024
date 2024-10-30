<div class="row sales layout-top-spacing">          
    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            
             <div class="widget-content">

                <div x-data="{ isUploading : false }">

                @if($estadoImportacion === 0 || $estadoImportacion === 3)
              <?php /*  {{ $estadoImportacion }} */ ?>
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
                <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>                  
                </br>
                </br>
                    @if($estadoImportacion === 3)
                    
                        <div class="row mt-5">
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group custom-file">                              
                                <h6 class="text-center">                          
                               <svg xmlns="http://www.w3.org/2000/svg" height="1.6em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#0dca1b}</style><path d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"/></svg>	  
                                LA IMPORTACION SE HA COMPLETADO CORRECTAMENTE
                                </h6>        
                                </div>
                            </div> 
                        </div>
                
                    @endif  

                @endif

             
                @if(count($validacion_errores) > 0 && $estadoImportacion === 1)    
                
                <?php /* {{ $estadoImportacion }} */ ?>
                <div class="row mt-5">
                    <div class="col-sm-8 col-md-8">
                        <div class="form-group custom-file">   
                        <h5><strong>Se han encontrado errores en el archivo a importar, desea continuar con la importación salteando las filas con errores?</strong></h5>
                        </br>
                        <button wire:loading.attr="disabled" wire:click.prevent="DeleteFile()" {{$fileProducts =='' ? 'disabled' : ''}} class="btn btn-dark">CANCELAR</button> 
                        <button wire:loading.attr="disabled" wire:click.prevent="import()" {{$fileProducts =='' ? 'disabled' : ''}} class="btn btn-dark">CONTINUAR</button>                            
                        </br>     

                         
                         </div>
                    </div> 
                </div>
                @endif

                @if($estadoImportacion === 2)
                <?php /* {{ $estadoImportacion }}  */ ?>
                <div class="row mt-5">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group " style="padding: 0 0 2em 0;">      
                        <h6 class="text-center" wire:loading>IMPORTANDO PRODUCTOS, POR FAVOR ESPERE</h6>   
                        </br>         
                            <div class="progress" >                          
                                <div class="progress-bar" role="progressbar" style="width: "></div>
                                </div>
                            </div>            
                        </div>
                    </div> 
                </div>
                @endif      
 
                </br>              
                <!-- display validation errors-->    
                
                <script>
                        window.addEventListener('load', function () {
                        window.livewire.emit('checkLastImport');
                    });
                </script>
                 <script>                
                    window.livewire.on('estatus-proceso-importacion', (filaProcesada, totalFilas) => {        
                        if(filaProcesada <= totalFilas){                        
                            window.livewire.emit('checkProgress'); 
                        }else{
                            location.reload();
                        }
                       
                    });
                </script>

                <script>
                  window.livewire.on('progressUpdated', (progress) => {                 
                       const progressBar = document.querySelector('.progress-bar');
                        progressBar.style.width = progress + '%';
                        progressBar.innerHTML = (Math.round(progress * 100) / 100) + '%';                        
                    });
                </script>

                @if(count($validacion_errores) > 0)
                
                </br>

           
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
