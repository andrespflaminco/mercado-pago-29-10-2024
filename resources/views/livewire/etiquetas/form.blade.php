<head>
  <style media="screen">
  @media (min-width: 576px) {

.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">

      <div style="max-width: 300px !important;
      margin: 1.75rem auto;" class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Agregar Etiquetas</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
          <h5>{{$barcode}} - {{$name}}</h5>
          <br>
          <label for="">Cantidad de Etiquetas</label>
           <input style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="cantidad" >


           <input hidden type="text" id="selected_id" wire:model.lazy="selected_id" class="form-control" >
          <input hidden id="id_producto" type="text" wire:model.lazy="name" class="form-control" >


              </div>
              <div class="modal-footer row">
                <div class="col-6" style="padding-right:0px !important; margin:0px !important;"><button type="button" wire:click.prevent="resetUI()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button></div>
                <div class="col-6" style="padding-left:0px !important; margin:0px !important;"><button class="btn btn-submit" wire:click="AgregarDesdeModal"  title="Agregar al carrito">
    			    AGREGAR
    				</button>
    			</div>
              </div>
          </div>
      </div>
  </div>
