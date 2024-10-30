
<div style="z-index: 99999 !important" wire:ignore.self class="modal fade" id="AgregarEditarSaldoInicial" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  @if($modo_ver_saldo == 0)
                  <b> @if($selected_pago_saldo_id > 0) EDITAR PAGO @else AGREGAR PAGO @endif</b>
                  @else
                  <b> VER PAGO </b>
                  @endif
              </div>
              <div style="margin: 0 auto !important;" class="modal-body">


                <div class="row">

                <div class="col-sm-12 col-md-12">
                 <div class="form-group">
                   <label>Fecha</label>
                   <input {{$modo_ver_saldo == 1 ? 'disabled' : '' }} autocomplete="off" type="date" id="title" wire:model.defer="fecha_saldo" class="form-control" required="">
                 </div>
                </div>
               
                 <div class="col-sm-12 col-md-12">
                 <div class="form-group">
                   <label>Metodo de pago</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input {{$modo_ver_saldo == 1 ? 'disabled' : '' }} autocomplete="off" type="number" id="title" wire:model.defer="monto_saldo" class="form-control" required="">


               </div>
               </div>
               </div>
               <br>
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Forma de pago</label>
                   <select {{$modo_ver_saldo == 1 ? 'disabled' : '' }} wire:model.defer='metodo_pago_saldo' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>

                     @foreach($metodo_pago_agregar as $mp)
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
                <button type="button" wire:click.prevent="CerrarAgregarPagoSaldo()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
                
                @if($modo_ver_saldo == 0)
                
                @if($selected_pago_saldo_id > 0)
                <button type="button" wire:click.prevent="ActualizarPagoSaldo({{$selected_pago_saldo_id}})" class="btn btn-submit" >ACTUALIZAR</button>
                @else
                <button type="button" wire:click.prevent="CreatePagoSaldo()" class="btn btn-submit" >GUARDAR</button>
                @endif
                
                @endif



              </div>
          </div>
      </div>
  </div>
