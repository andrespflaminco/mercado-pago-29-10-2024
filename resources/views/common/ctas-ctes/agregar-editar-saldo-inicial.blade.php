
<div style="z-index: 99999 !important" wire:ignore.self class="modal fade" id="AgregarEditarSaldoInicial" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b> @if($formato_modal > 0) EDITAR PAGO @else AGREGAR PAGO @endif</b>

              </div>
              <div style="margin: 0 auto !important;" class="modal-body">

                <div class="row">
                  <div style="padding-top: 10px;" class="col-sm-12 col-md-6">
                  @if($caja == null)
                  <b style="color:red;"> Sin caja asociada</b>
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

                     <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja</p>
                     <div class="dropdown-divider"></div>
                     @foreach($ultimas_cajas as $uc)
                   <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ElegirCaja({{$uc->id}})">Caja # {{$uc->nro_caja}} ( {{\Carbon\Carbon::parse($uc->fecha_inicio)->format('d/m/Y')}} )</a>
                    @endforeach


                   <div class="dropdown-divider"></div>
                   <p hidden style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja por fecha</p>
                   <div hidden class="dropdown-divider"></div>
                      <input type="date" wire:change="CambioCaja()" wire:model="fecha_ap"  class="form-control " >

                   </div>
                   </div>
                  <br>
                  <br>

                  </div>
                </div>
                
                <div class="row">

                  <div class="col-sm-12 col-md-12">
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input autocomplete="off" type="number" id="title" wire:model.defer="monto_ap" class="form-control">


               </div>
               </div>
               <br>
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Forma de pago</label>
                   <select wire:model.defer='metodo_pago_agregar_pago' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>

                     @foreach($metodo_pago as $mp)
                     <option value="{{$mp->id}}">
                     {{$mp->nombre}}</option>
                     @endforeach
                   </select>
                 </div>

                 </div>


              </div>


              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="CerrarAgregarPago({{$selected_id}})" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
                @if($formato_modal > 0)
                <button type="button" wire:click.prevent="ActualizarPago({{$id_pago}})" class="btn btn-submit" >GUARDAR</button>
                @else
                <button type="button" wire:click.prevent="CreatePago({{$id_pago}})" class="btn btn-submit" >GUARDAR</button>
                @endif



              </div>
          </div>
      </div>
  </div>
