
<div style="z-index: 99999 !important" wire:ignore.self class="modal fade" id="AbrirCaja" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>ABRIR CAJA</b>

              </div>
              <div style="margin: 0 auto !important;" class="modal-body w-100">
                  
               <div class="row">
                <div class="col-sm-12 col-md-2">
                
                  </div>
                <div class="col-sm-12 col-md-8">
                 <div class="form-group">
                  <label>Monto inicial</label>
                  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                    <div class="input-group-prepend">
                      <span class="input-group-text input-gp">
                        $
                      </span>
                    </div>
                    <input type="text" wire:model.lazy="monto_inicial" required class="form-control" placeholder="Ej: 10" >
                
                      </div>
                </div>
                </div>
                <div class="col-sm-12 col-md-2">
                
                  </div>



                </div>


              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="CerrarAgregarPago({{$NroVenta}})" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
                <button type="button" wire:click.prevent="AbrirCajaGuardar()" class="btn btn-submit" >GUARDAR</button>



              </div>
          </div>
      </div>
  </div>
