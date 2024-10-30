
<div  wire:ignore.self class="modal fade" id="CambiarEstado" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>CAMBIAR ESTADO</b>

              </div>
              
              <div style="margin: 0 auto !important;" class="modal-body">

                 <div class="row"> 
                 <div class="col-sm-12 col-md-12">
                 
                 <label>Nuevo Estado</label>
                 @if($nuevo_estado == 0)
                 <div class="alert alert-danger">
                 <h6>PENDIENTE</h6>
                 </div>
                 @else
                 <div class="alert alert-success">
                  <h6>ACREDITADO</h6>     
                 </div>                 
                 @endif
                 
                 </div>
                 
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Numero de comprobante</label>
                   <input type="text" class="form-control" wire:model.defer="nro_comprobante">
                 </div>

                 </div>
                 <br>
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Comprobante</label>
                   @if($comprobante == null)
                        <input type="file" class="form-control-file"  wire:model="comprobante">
                   @else
                        <a style="font-size: 14px !important;" href="{{ asset('storage/comprobantes/' . $comprobante) }}" target="_blank" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Ver
                        </a>
                  
                  
                        <a style="font-size: 14px !important;" href="javascript:void(0)" wire:click="toggleInputFile()" class="btn btn-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Modificar
                        </a>
                        <div class="mt-3"><input type="file" class="form-control-file" wire:model="comprobante" @if(!$mostrarInputFile) style="display: none;" @endif></div>
                            
                   @endif
                  </div>

                 </div>

                
              @if(0 < count($deducciones))
              
                <div class="col-sm-12 col-md-12">
                    
                    <br><br>
                    <a href="#" wire:click.prevent="toggleMostrarDeducciones">
                        {{ $mostrarDeducciones ? 'Ocultar Deducciones' : 'Ver Deducciones' }}
                    </a>
                
                    @if($mostrarDeducciones)
                        @if(count($deducciones) > 0)
                            <div style="width: 100%;" class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Deduccion</th>
                                            <th class="text-center">%</th>
                                            <th class="text-center">Monto</th>
                                            <th>Acciones </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deducciones as $index => $deduccion)
                                            <tr>
                                                <td>
                                                <input style="padding: 0rem !important;" type="text" wire:model.defer="deducciones.{{ $index }}.concepto" class="form-control" />
                                                </td>
                                                <td class="text-center">
                                                    <input                                                         style="padding: 0rem !important;" 
                                                        type="text" 
                                                        wire:model.defer="deducciones.{{ $index }}.porcentaje" 
                                                        class="form-control" 
                                                        wire:change="recalcularMontoDeduccion({{ $index }})"
                                                        wire:keyup.enter="recalcularMontoDeduccion({{ $index }})"
                                                    />
                                                </td>
                                                <td class="text-center">
                                                    <input                                                         style="padding: 0rem !important;" 
                                                        type="text" 
                                                        wire:model.defer="deducciones.{{ $index }}.monto" 
                                                        class="form-control" 
                                                        wire:change="recalcularPorcentajeDeduccion({{ $index }})"
                                                        wire:keyup.enter="recalcularPorcentajeDeduccion({{ $index }})"
                                                    />
                                                </td>
                                                <td>
                                                    <a href="#" wire:click.prevent="removeDeduccion({{ $index }})">✖</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>


                @endif

              </div>

              </div>
              <div class="modal-footer">
                <br>
                <a href="javascript:void(0);" wire:click.prevent="CerrarCambiarEstado()" class="btn btn-cancel" data-dismiss="modal">Cerrar</a>
                <a wire:click.prevent="StoreCambiarEstado()" href="javascript:void(0);" class="btn btn-submit me-2" >Guardar</a>

              </div>
          </div>
      </div>
  </div>