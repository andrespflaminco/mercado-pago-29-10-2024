<<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="Cheque" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 650px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Agregar Cheque</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>
                <div class="row">

                  <div class="col-sm-12 col-md-4">
                   <div class="form-group">
                    <label>Numero de cheque</label>
                    <input type="text" wire:model="nro_cheque_ch" class="form-control">
                    @error('nro_cheque_ch') <span class="text-danger err">{{ $message }}</span> @enderror


                  </div>
                  </div>

                  <div class="col-sm-12 col-md-4">
                   <div class="form-group">
                    <label>Emisor</label>
                    <input type="text" wire:model="emisor_ch" class="form-control">
                    @error('emisor_ch') <span class="text-danger err">{{ $message }}</span> @enderror


                  </div>
                  </div>

                  <div class="col-sm-12 col-md-4">
                   <div class="form-group">
                    <label>Banco</label>
                    <input type="text" wire:model="banco_ch" class="form-control">
                    @error('banco_ch') <span class="text-danger err">{{ $message }}</span> @enderror


                  </div>
                  </div>

                  <div class="col-sm-12 col-md-4">
                   <div class="form-group">
                    <label>Fecha de emision</label>
                    <input type="date" wire:model="fecha_emision_ch" class="form-control">
                    @error('fecha_cobro_ch') <span class="text-danger err">{{ $message }}</span> @enderror


                  </div>
                  </div>
                  <div class="col-sm-12 col-md-4">
                   <div class="form-group">
                    <label>Fecha de cobro</label>
                    <input type="date" wire:model="fecha_cobro_ch" class="form-control">
                    @error('fecha_emision_ch') <span class="text-danger err">{{ $message }}</span> @enderror


                  </div>
                  </div>


               </div>

                 </div>

               <br>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

                <button type="button" wire:click.prevent="saveSale()" class="btn btn-dark close-modal" >GUARDAR</button>
              </div>
          </div>
      </div>
  </div>
