
<div  wire:ignore.self class="modal fade" id="AgregarPago" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>{{ $formato_modal > 0 ? 'EDITAR PAGO' : 'NUEVO PAGO' }}</b>

              </div>
              
              <div style="margin: 0 auto !important;" class="modal-body">

                  <div class="row">
                  <div style="padding-top: 10px;" class="col-sm-12 col-md-6">
                 @if($caja == null)

                  <b style="color:red;"> Caja cerrada. Debe seleccionar una caja. </b>
                  <br>
                  <br>
                  @else
                  <b style="color:green;"> Caja seleccionada: # {{$caja_seleccionada->nro_caja}} </b>
                  <br>
                  <br>
                  @endif

                  </div>
                  <div class="col-sm-12 col-md-6">

                   <div style="width:100%;" class="btn-group  mb-4 mr-2">
                   <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Seleccionar otra caja</button>
                   <div class="dropdown-menu">
                    @if($caja == null)
                    <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Abrir caja</p>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ModalAbrirCaja()">+ NUEVA CAJA </a>

                    @endif
                     <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja</p>
                     <div class="dropdown-divider"></div>
                     @foreach($ultimas_cajas as $uc)
                   <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ElegirCaja({{$uc->id}})">Caja # {{$uc->nro_caja}} ( {{\Carbon\Carbon::parse($uc->fecha_inicio)->format('d/m/Y')}} )</a>
                    @endforeach


                   <div class="dropdown-divider"></div>
                   <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja por fecha</p>
                   <div class="dropdown-divider"></div>
                      <input type="date" wire:change="CambioCaja()" wire:model="fecha_ap"  class="form-control " >

                   </div>
                   </div>
                  <br>
                  <br>

                  </div>
                  </div>




                <div class="row">
                @if($relacion_precio_iva == 1)
                 <div class="col-sm-12 col-md-12">
                     <div class="form-group">
                     <label> Monto sin IVA</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">

                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>

                   <input autocomplete="off" type="number" id="title" wire:model.lazy="monto_real" wire:keydown.enter="MontoPagoReal($event.target.value)" wire:change='MontoPagoReal($event.target.value)' class="form-control" required="">



               </div>
               </div>
               </div>
               @endif

                 <div class="col-sm-12 col-md-12">
                     <div class="form-group">
                     <label> Monto @if($relacion_precio_iva == 2 || $relacion_precio_iva == 1) con IVA @endif</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">

                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>

                   <input autocomplete="off" type="number" id="title" wire:model.lazy="monto_ap" wire:keydown.enter='MontoPagoEditarPago($event.target.value)' wire:change='MontoPagoEditarPago($event.target.value)' class="form-control" required="">



               </div>
               </div>
               </div>
               <br>
                 <div class="col-sm-12 col-md-12">
                   
                   <div class="form-group">
                    <label> Tipo de cobro</label>
                    
                   <select  wire:model='tipo_pago' wire:change='TipoPago($event.target.value)'  class="form-control">
                       <option value="1">Efectivo</option>
                       @foreach($tipos_pago as $tipos)
                       <option value="{{$tipos->id}}">{{$tipos->nombre}}</option>
                       @endforeach
                     </select>

                    </div>

                    @if($tipo_pago !=2)

                    <div class="form-group">
                   <label>Forma de cobro</label>
                   <select wire:model='metodo_pago_agregar_pago' wire:change='MetodoPago($event.target.value)'  class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     
                     @if($tipo_pago == 1)
                    <option value="1"  >Efectivo</option>
                    @endif
                    
                     @foreach($metodo_pago_agregar as $mp)
                     <option value="{{$mp->id}}">
                        {{$mp->nombre}}</option>
                     @endforeach
                     <option hidden value="1" >Efectivo</option>
                   </select>
                 </div>

                    @endif
                    
                    @if($datos_cliente != null)
                    @if($datos_cliente->sucursal_id != null)
                    <div class="form-group">
                    <label> Medio de pago de la sucursal</label>
                    
                   <select  wire:model='tipo_pago_sucursal'  class="form-control">
                       <option value="1">Efectivo</option>
                       @foreach($tipos_pago_sucursal as $tipos)
                       <option value="{{$tipos->id}}">{{$tipos->nombre}}</option>
                       @endforeach
                     </select>

                    </div>
                    @endif
                    @endif
                    
                    
                 </div>
                 
                                 
                <br>
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


              </div>
              <p class="text-muted">Subtotal @if($relacion_precio_iva == 2) (Con IVA) @endif: ${{$monto_ap}}</p>
              <p class="text-muted">Recargo @if($relacion_precio_iva == 2) (Con IVA) @endif: ${{number_format($recargo_total,2)}}</p>
              <h4 style=" width: 100%;
                padding: 0.375rem 0.75rem;
                font-size: 1.25rem;
                text-align:center;
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #ced4da;">A cobrar: ${{number_format($total_pago,2)}}</h4>
                
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
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deducciones as $index => $deduccion)
                                            <tr>
                                                <td>
                                                    <input style="padding: 0rem !important;" type="text" wire:model.defer="deducciones.{{ $index }}.concepto" class="form-control" />
                                                </td>
                                                <td class="text-center">
                                                    <input 
                                                        style="padding: 0rem !important;" 
                                                        type="text" 
                                                        wire:model.defer="deducciones.{{ $index }}.porcentaje" 
                                                        class="form-control" 
                                                        wire:change="recalcularMontoDeduccion({{ $index }})"
                                                        wire:keyup.enter="recalcularMontoDeduccion({{ $index }})"
                                                    />
                                                </td>
                                                <td class="text-center">
                                                    <input 
                                                        style="padding: 0rem !important;" 
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
              <div class="modal-footer">
                <br>
                <a href="javascript:void(0);" wire:click.prevent="CerrarAgregarPago({{$NroVenta}})" class="btn btn-cancel" data-dismiss="modal">Cerrar</a>
                @if($formato_modal > 0)
                 <a wire:click.prevent="ActualizarPago({{$id_pago}})" href="javascript:void(0);" class="btn btn-submit me-2" >Actualizar</a>
                @else
                <a {{ $caja == null ? 'disabled' : '' }} wire:click.prevent="CreatePago2({{$id_pago}})" href="javascript:void(0);" class="btn btn-submit me-2" >Guardar</a>
                @endif



              </div>
          </div>
      </div>
  </div>

