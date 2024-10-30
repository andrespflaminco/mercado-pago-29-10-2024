
<div wire:ignore.self class="modal fade" id="AgregarPago" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>{{ $formato_modal > 0 ? 'EDITAR PAGO' : 'NUEVO PAGO' }} || VENTA {{$venta_id}}</b>

              </div>
              <div style="margin: 0 auto !important;" class="modal-body">
                                  <div class="row">
                  <div style="padding-top: 10px;" class="col-sm-12 col-md-6">
                @if($caja == null)

                <b style="color:red;"> Sin caja seleccionada. </b>
               
                @else
                <b style="color:green;"> Caja seleccionada: # {{$caja_seleccionada->nro_caja}} </b>
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
                    <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="SinCaja()">Sin caja </a>

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
                  <div class="col-sm-12 col-md-12">
                <div class="form-group">
                   <label>Monto</label>
                
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input autocomplete="off" type="number" id="title" wire:model.lazy="monto_ap" class="form-control" required="">


               </div>
               </div>
               </div>
               <br>
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Forma de pago</label>
                   <select wire:model='metodo_pago_agregar_pago' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>

                     @foreach($metodo_pago as $mp)
                     <option value="{{$mp->id}}">
                       {{$mp->nombre}}</option>
                     @endforeach
                   </select>
                 </div>

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
              <br>
              <br>
              <p>A pagar: ${{number_format($total_pago,2)}}</p>
              <br>
              <p>Deuda: $  
              
              @foreach ($total as $t)
                {{($t->total) - ($suma_monto) }}
              @endforeach 
                 
            </p>




              </div>
              <div class="modal-footer">
                <br>
                
                
                <a href="javascript:void(0);" wire:click.prevent="CerrarAgregarPago({{$venta_id}})" class="btn btn-cancel" data-dismiss="modal">Cerrar</a>
                @if($formato_modal > 0)
                <a wire:click.prevent="ActualizarPago({{$id_pago}})" href="javascript:void(0);" class="btn btn-submit me-2" >Actualizar</a>
                @else
                <a wire:click.prevent="CreatePago({{$id_pago}})" href="javascript:void(0);" class="btn btn-submit me-2" >Guardar</a>
                @endif



              </div>
          </div>
      </div>
  </div>
  <script>
      document.addEventListener('DOMContentLoaded', function(){




          flatpickr(document.getElementsByClassName('flatpickr'),{
              enableTime: false,
              dateFormat: 'Y-m-d',
              locale: {
                  firstDayofWeek: 1,
                  weekdays: {
                      shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                      longhand: [
                      "Domingo",
                      "Lunes",
                      "Martes",
                      "Miércoles",
                      "Jueves",
                      "Viernes",
                      "Sábado",
                      ],
                  },
                  months: {
                      shorthand: [
                      "Ene",
                      "Feb",
                      "Mar",
                      "Abr",
                      "May",
                      "Jun",
                      "Jul",
                      "Ago",
                      "Sep",
                      "Oct",
                      "Nov",
                      "Dic",
                      ],
                      longhand: [
                      "Enero",
                      "Febrero",
                      "Marzo",
                      "Abril",
                      "Mayo",
                      "Junio",
                      "Julio",
                      "Agosto",
                      "Septiembre",
                      "Octubre",
                      "Noviembre",
                      "Diciembre",
                      ],
                  },

              }

          })

        });
        </script>

