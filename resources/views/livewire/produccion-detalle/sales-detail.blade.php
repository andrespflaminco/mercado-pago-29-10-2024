<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-dialog {
  max-width: 300px !important;
  margin: 1.75rem auto;
}
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" role="dialog">

      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Cambiar de estado</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>

            

              <input hidden type="text" value="{{$id_produccion_detalle}}" wire:model="id_produccion_detalle" class="form-control">
              <!-- Warning -->
              <button wire:click.prevent="UpdateEstadoProduccion(1,2)" style="min-width:265px; width:100%;" class="btn btn-warning mb-2">Pendiente</button><br>
              <button wire:click.prevent="UpdateEstadoProduccion(2,2)" style="min-width:265px; width:100%;" class="btn btn-secondary mb-2">En proceso</button><br>
              <button wire:click.prevent="UpdateEstadoProduccion(3,2)" style="min-width:265px; width:100%;" class="btn btn-success mb-2">Terminado</button><br>
              <button wire:click.prevent="UpdateEstadoProduccion(5,2)" style="min-width:265px; width:100%;" class="btn btn-danger mb-2">Cancelado (sin desperdicios)</button>
              <button wire:click.prevent="UpdateEstadoProduccion(6,2)" style="min-width:265px; width:100%;" class="btn btn-danger mb-2">Cancelado (con desperdicios)</button>


              </div>
              <div class="modal-footer">
                <br>
              </div>
          </div>
      </div>
  </div>
