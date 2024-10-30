<div>
    <div class="page-header">
        <div class="page-title">
            <h4>Importar. </h4>
        </div>
        <div class="page-btn">
            <a href="{{ url('products') }}" class="btn btn-added"> Volver</a>
        </div>
    </div>

@if($respuesta == null)
    <!-- /product list -->
    <div class="card">
        <div class="card-body">
            @if($importaciones->count() < 1)
            
            @include('livewire.import.pasos')
            
            
            @endif
           
           <!----- Paso 3 ------>
           <div style="{{$paso3}}">

            @include('livewire.import.elegir-accion')
 
            @if($accion != 0)
            <div class="row">
                
                @if($importaciones->count() < 1)
                <h6>Selecciona el excel que contiene los datos de tus productos, luego presiona importar.</h6>
                @endif
                

                
                <div x-data="{ isUploading: false }">
                    @if ($estadoImportacion === 0 || $estadoImportacion === 3)
                        <?php /*  {{ $estadoImportacion }} */ ?>
                        @if(count($columnas_excel) < 1)
                        <div class="row mt-5">
                            <div class="col-sm-12 col-md-12">
                                
                                <div class="col-sm-12 col-md-8">
                                <div class="form-group custom-file">

                                    <label class="custom-file-label">Buscar excel</label>
                                    <input type="file" class="custom-file-input form-control"
                                        wire:model="fileProducts" accept=".xlsx, .xls,">
                                    
                                    @if($accion != 1)
                                    <a href="javascript:void(0)" wire:click="AbrirAyuda({{$accion}})">Ayuda Â¿Como confecciono la tabla?</a>
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
                        @endif                      

                            <div class="col-sm-12 col-md-8 text-right mb-3">
                                <button hidden wire:loading.attr="disabled" wire:click.prevent="ValidateProducts()"
                                    {{ $fileProducts == '' ? 'disabled' : '' }} class="btn btn-success">
                                    Importar 
                                </button>
                                 @if(count($columnas_excel) < 1)
                                 <a style="width: 200px;" wire:loading.attr="disabled" href="{{ url('import') }}"
                                  {{ $fileProducts == '' ? 'disabled' : '' }} class="btn btn-dark">
                                  CANCELAR
                                  </a>
                                <button style="width:200px !important;" wire:loading.attr="disabled" wire:click.prevent="GetMatchColumnas()"
                                    {{ $fileProducts == '' ? 'disabled' : '' }} class="btn btn-success">
                                    IMPORTAR
                                </button>
                                @endif
                            </div>
                        </div>
                 
                <!-------- MATCH DE COLUMNAS ------->
            
                @if(0 < count($columnas_excel))

                <a href="javascript:void(0)" wire:click="AbrirAyuda(5)">Ayuda Â¿Como asigno las columnas de la tabla?</a>
                
               <div class="row">
                    <div class="table-responsive">
                    <table class="table">
                    <thead>
                        <tr>
                          @foreach($columnas_excel as $ce)
                            @if($ce != "")
                              <td>
                              <select class="form-control" wire:model="columna_excel.{{$ce}}">
                                <option value="0">No importar</option>
                                @foreach($columnas_base as $cb)
                                <option value="{{$cb}}">{{$cb}}</option>
                                @endforeach
                              </select>
                              </td>
                            @endif
                          @endforeach
                        
                        </tr>
                         <tr>
                          @foreach($columnas_excel as $ce)
                            @if($ce != "")
                              <td style="padding: 0.45rem 1.75rem 0.45rem 1rem !important; font-size: 1rem !important; font-weight: 600 !important; line-height: 1.5 !important; color: #212529 !important;">{{$ce}}</td>
                            @endif
                          @endforeach
                        </tr>
                        
                    </thead>
                    <tbody>
                        @foreach (array_slice($dataForView, 0) as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{{ is_array($cell) ? implode(', ', $cell) : $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                  <br><br>
                  <div class="row">
                      <a style="max-width: 200px; margin-left:10px;" wire:loading.attr="disabled" href="{{ url('import') }}"
                      {{ $fileProducts == '' ? 'disabled' : '' }} class="btn btn-dark mt-3">
                      CANCELAR
                      </a>
                      <button style="max-width: 200px; margin-left:10px;" wire:loading.attr="disabled" wire:click.prevent="SetMatchColumnas()"
                      class="btn btn-success mt-3">
                      IMPORTAR
                      </button>                        
                  </div>

                </div>


                @endif
              
                <!-------- // MATCH DE COLUMNAS -------->
              
              
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
                                            LA IMPORTACION SE HA COMPLETADO CORRECTAMENTE {{$respuesta}}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                window.addEventListener('load', function() {
                                    window.livewire.emit('CheckLastImportCompleted');
                                });
                            </script>
                          @endif
                    @endif
                    
                    @if (count($validacion_errores) > 0 && $estadoImportacion === 1)
                        <?php /* {{ $estadoImportacion }} */ ?>
                        <div class="row mt-5">
                            <div class="col-sm-10 col-md-10">
                                <div class="form-group custom-file">
                                    <h5><strong>Se han encontrado errores en el archivo a importar, desea continuar con
                                            la importacion salteando las filas con errores?</strong></h5>
                                    <button style="width: 200px;" wire:loading.attr="disabled" wire:click.prevent="DeleteFile()"
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

                                    @if($tipo_procesando == 1)
                                    <h6 class="text-center" wire:loading>VALIDANDO PRODUCTOS, COMPROBANDO QUE ESTEN BIEN LOS DATOS..</h6>
                                    @endif
                                    
                                    @if($tipo_procesando == 2)
                                    <h6 class="text-center" wire:loading>IMPORTANDO PRODUCTOS, POR FAVOR ESPERE</h6>

                                    @endif                                    
                                    
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"></div>
                                    </div>
                                    <p>Procesando: {{$fila_procesadas}} de {{$fila_totales}} filas.</p>

                                </div>
                            </div>
                        </div>
                        <script>

                                window.livewire.on('estatus-proceso-importacion', (filaProcesada, totalFilas, import_id) => {
                                    if (filaProcesada <= totalFilas) {
                                        window.livewire.emit('checkProgress');
                                    } else {
                                        window.location.href = 'https://app.flamincoapp.com.ar/import?respuesta=' + import_id;
                                    }
                                });
                            
                            window.livewire.on('estatus-proceso-validacion', (filaProcesada, totalFilas,import_id) => {
                                if (filaProcesada <= totalFilas) {
                                    window.livewire.emit('checkProgressValidacion');
                                } else {
                                    window.livewire.emit('checkValidacion',import_id);
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
                
            </div>
        @endif
          
            
            @if(0 < $importaciones->count() )
            @if(count($columnas_excel) == 0)
            <!-- /Filter -->
            <div class="row">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Fecha de importacion</th>
                                <th>Estado</th>
                                <th>Proceso</th>
                                <th>Excel Importado </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($importaciones as $r)
                                    @php
                                    $estado = $r->estado;
                                    $timeElapsed = \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($r->created_at));
                            
                                    // Si el estado es 0 y han pasado m«¡s de 2 horas, cambiar el estado a 3 (Fallido)
                                    if ($estado == 0 && $timeElapsed > 2) {
                                        $estado = 3;
                                    }
                                @endphp
                                <tr>
                                    <td>Importar Catalogo</td>
                                    <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i') }} hs.</td>
                                    <td>
                                        @if ($r->estado == 0)
                                            <span class="badges bg-lightyellow">A la espera</span>
                                        @endif
                                        @if ($r->estado == 1)
                                            <span class="badges bg-lightyellow"
                                                style="background: #6c757d !important;">En preparacion</span>
                                        @endif
                                        @if ($r->estado == 2)
                                            <span class="badges bg-lightgreen">Importado</span>
                                        @endif
                                        @if ($r->estado == 3)
                                            <span class="badges bg-lightred">Fallido</span>
                                        @endif
                                    </td>
                                    <td>
                                        
                                        @php
                                            // Inicializar las variables por defecto como vac«¿as
                                            $fila = '';
                                            $total_fila = '';
                                    
                                            // Verificar si $r->proceso no es null antes de hacer explode
                                            if (!is_null($r->proceso)) {
                                                $proceso = explode('/', $r->proceso);
                                    
                                                // Verificar si el resultado del explode tiene las partes esperadas
                                                $fila = isset($proceso[0]) ? $proceso[0] : '';
                                                $total_fila = isset($proceso[1]) ? $proceso[1] : '';
                                            }
                                        @endphp
                                    
                                        @if (!is_null($r->proceso) && ($r->estado == 2))
                                            {{ $fila }} de {{ $total_fila }} productos procesados
                                        @endif
                                    </td>

                                    <td>
                                        @if ($r->estado == 2)
                                            <a href="javascript:void(0)" wire:click="Descargar('{{ $r->id }}')"
                                                class="btn btn-dark text-white" title="Descargar">
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
            @endif
            @endif
            @endif
           </div>
            
            
           <!----- / Paso 3 ------>
            
            @if($importaciones->count() < 1)
            
            <!----- Botones ------>
           @include('livewire.import.botones')
            
            @endif
            <!----- // Botones ------>
            
            
        </div>

@else
 <div class="card">
     <div class="card-body">
@include('livewire.import.respuesta')
</div>
</div>

@endif


@include('livewire.import.form')
    </div>

<script>

    	document.addEventListener('DOMContentLoaded', function() {

        window.addEventListener('load', function() {
            window.livewire.emit('checkLastImport');
        });
                            
		window.livewire.on('show-modal', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('hide-modal', msg => {
			$('#theModal').modal('hide')
		});


	});


</script>