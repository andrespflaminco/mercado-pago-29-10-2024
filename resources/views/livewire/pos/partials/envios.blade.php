<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="Envios" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">AGREGAR ENVIO</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body" style="width:90%;">
                <br>
                <label>Nombre de quien recibe</label>
                <input class="form-control" wire:model.lazy="nombre_envio">
                <label>Telefono</label>
                <input class="form-control" wire:model.lazy="telefono_envio">
                <label>Direccion</label>
                <input class="form-control" wire:model.lazy="direccion_envio">
                <label>Ciudad</label>
                <input class="form-control" wire:model.lazy="ciudad_envio">


              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUIEnvio()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

                <button type="button" wire:click.prevent="guardarEnvio()" class="btn btn-dark close-modal" >GUARDAR</button>
              </div>
          </div>
      </div>
  </div>
