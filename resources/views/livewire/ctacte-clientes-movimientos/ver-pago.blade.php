
<div wire:ignore.self class="modal fade" id="VerPago" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>VER PAGO</b>

              </div>
              <div style="margin: 0 auto !important;" class="modal-body">
                                  <div class="row">
                  <div style="padding-top: 10px;" class="col-sm-12 col-md-6">
                @if($caja_ver != 0)
                <b style="color:green;"> Caja seleccionada: # {{$caja_ver}} </b>
                @else
                <b style="color:green;"> No relacionado a caja</b>
                @endif
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
                   <input autocomplete="off" type="number" id="title" readonly wire:model.lazy="monto_ver" class="form-control" required="">


               </div>
               </div>
               </div>
               <br>
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Forma de pago</label>
                   <input readonly wire:model="nombre_banco_ver" class="form-control">
                 </div>

                 </div>
                
                
                <br>
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Numero de comprobante</label>
                   <input type="text" class="form-control" wire:model.defer="nro_comprobante_ver" readonly>
                 </div>

                 </div>
                 
                <br>
                 <div class="col-sm-12 col-md-12">

                  <div class="form-group">
                   <label>Comprobante</label>
                   @if($comprobante_ver == null)
                   Sin comprobante 
                   @else
                        <a style="font-size: 14px !important;" href="{{ asset('storage/comprobantes/' . $comprobante_ver) }}" target="_blank" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Ver
                        </a>
                  
                        <div class="mt-3"><input type="file" class="form-control-file" wire:model="comprobante" @if(!$mostrarInputFile) style="display: none;" @endif></div>
                            
                   @endif
                  </div>

                 </div>

              </div>
              <br>
           
                 
            </p>




              </div>
              <div class="modal-footer">
                <br>
            
                
                <a href="javascript:void(0);" wire:click.prevent="CerrarVerPago({{$venta_id}})" class="btn btn-cancel" data-dismiss="modal">Cerrar</a>
            
              </div>
          </div>
      </div>
  </div>
