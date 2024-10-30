 <div style="{{$paso3}}">
            
            @if(0 < $importaciones->count() )
            
            @if($accion == 0)
            <div class="row">
                <div  class="col-6">
                    <div style="cursor:pointer; {{$accion == 1 ? 'background:#f9f9f9;' : '' }}" wire:click="SetAccion(1)" class="card">
                    <div class="card-body">
                        <h3>Crear y actualizar productos</h3>
                    </div>
                </div>
                </div>
                <div  class="col-6">
                  <div style="cursor:pointer; {{$accion == 3 ? 'background:#f9f9f9;' : '' }} " wire:click="SetAccion(3)" class="card">
                    <div class="card-body">
                        <h3>Actualizar stock</h3>
                    </div>
                </div>  
                </div>
                <div  class="col-6">
                <div style="cursor:pointer; {{$accion == 2 ? 'background:#f9f9f9;' : '' }}" wire:click="SetAccion(2)" class="card">
                    <div class="card-body">
                        <h3>Actualizar precios y costos</h3>
                    </div>
                </div>    
                </div>
                <div  class="col-6">
                  <div style="cursor:pointer; {{$accion == 4 ? 'background:#f9f9f9;' : '' }} " wire:click="SetAccion(4)" class="card">
                    <div class="card-body">
                        <h3>Importar nueva compra</h3>
                    </div>
                </div>  
                </div>
             </div>          
             @else
             <div class="row">
            <h4>Accion elegida: {{$accion == 1 ? 'Crear y actualizar productos' : ''}} {{$accion == 2 ? 'Actualizar precios y costos' : ''}} {{$accion == 3 ? 'Actualizar stock' : ''}} {{$accion == 4 ? 'Importar compra' : ''}}   </h4> <a href="javascript:void(0)" wire:click="SetAccion(0)" >Seleccionar otra</a>
             </div>
             @endif

            @if($accion != 0)
            <div class="row">
                
                @if($importaciones->count() < 1)
                <h6>Selecciona el excel que contiene los datos de tus productos, luego presiona importar.</h6>
                @endif
                

                
                <div x-data="{ isUploading: false }">
                    @if ($estadoImportacion === 0 || $estadoImportacion === 3)
                        <?php /*  {{ $estadoImportacion }} */ ?>
                        <div class="row mt-5">
                            <div class="col-sm-12 col-md-12">
                                
                                <div class="col-sm-12 col-md-8">
                                <div class="form-group custom-file">

                                    <label class="custom-file-label">Buscar excel</label>
                                    <input type="file" class="custom-file-input form-control"
                                        wire:model="fileProducts" accept=".xlsx, .xls,">
                                    
                                    @if($accion != 1)
                                    <a href="javascript:void(0)" wire:click="AbrirAyuda">Ayuda 多Como confecciono la tabla?</a>
                                    @endif
                                    
                                    @if($accion == 4)
                                    <div style="margin-left:5px;" class="d-flex mt-3">
                                    <input type="checkbox"  wire:model="actualizar_costos_4" style="margin-right:5px;"> Actualizar costos
                                    
                                    </div>
                                    
                                    @endif
                                    
                                    @error('fileProducts')
                                        <span class="text-danger er">{{ $message }}</span>
                                    @enderror
                                    
                                    </div>
                                    </div>
                                

                                </div>

                            </div>
                                              

                            <div class="col-sm-12 col-md-8 text-right mb-3">
                                <button wire:loading.attr="disabled" wire:click.prevent="ValidateProducts()"
                                    {{ $fileProducts == '' ? 'disabled' : '' }} class="btn btn-success">
                                    Importar
                                </button>
                            </div>
                        </div>
                        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
                        @if ($estadoImportacion === 3)
                            <div class="row mt-5">
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-group custom-file">
                                        <h6 class="text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1.6em"
                                                viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                <style>
                                                    svg {
                                                        fill: #0dca1b
                                                    }
                                                </style>
                                                <path
                                                    d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z" />
                                            </svg>
                                            LA IMPORTACION SE HA COMPLETADO CORRECTAMENTE
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if (count($validacion_errores) > 0 && $estadoImportacion === 1)
                        <?php /* {{ $estadoImportacion }} */ ?>
                        <div class="row mt-5">
                            <div class="col-sm-10 col-md-10">
                                <div class="form-group custom-file">
                                    <h5><strong>Se han encontrado errores en el archivo a importar, desea continuar con
                                            la importacion salteando las filas con errores?</strong></h5>
                                    <button wire:loading.attr="disabled" wire:click.prevent="DeleteFile()"
                                        {{ $fileProducts == '' ? 'disabled' : '' }} class="btn btn-dark">
                                        CANCELAR
                                    </button>
                                    {{-- <button wire:loading.attr="disabled" wire:click.prevent="import()"
                                        {{ $fileProducts == '' ? 'disabled' : '' }} class="btn btn-dark">
                                        CONTINUAR
                                    </button> --}}
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($estadoImportacion === 2)
                        <?php /* {{ $estadoImportacion }}  */ ?>
                        <div class="row mt-5">
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group " style="padding: 0 0 2em 0;">
                                    <h6 class="text-center" wire:loading>IMPORTANDO PRODUCTOS, POR FAVOR ESPERE</h6>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"></div>
                                    </div>
                                    
                                    <p>Procesando: {{$fila_procesadas}} de {{$fila_totales}} filas.</p>
                                </div>
                            </div>
                        </div>
                        <script>
                            window.addEventListener('load', function() {
                                window.livewire.emit('checkLastImport');
                            });
                            window.livewire.on('estatus-proceso-importacion', (filaProcesada, totalFilas) => {
                                if (filaProcesada <= totalFilas) {
                                    window.livewire.emit('checkProgress');
                                } else {
                                    location.reload();
                                }
                            });
                            window.livewire.on('progressUpdated', (progress) => {
                                const progressBar = document.querySelector('.progress-bar');
                                progressBar.style.width = progress + '%';
                                progressBar.innerHTML = (Math.round(progress * 100) / 100) + '%';
                            });
                        </script>
                    @endif
                </div>
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
                @endif
</div>