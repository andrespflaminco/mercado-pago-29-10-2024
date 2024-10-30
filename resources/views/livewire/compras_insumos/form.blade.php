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
                  <h5 class="modal-title" id="exampleModalLabel">Agregar producto</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>
          <label for="">Cantidad</label>
           <input id="cantidad" onchange='calcular_agregar(this)' style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="cantidad" >
           <label for="">Costo</label>

           <input id="costo" style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="cost" >

           <input hidden id="stock" style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="stock" >
           <input hidden type="text" id="selected_id" wire:model.lazy="selected_id" class="form-control" >
          <input hidden id="id_producto" type="text" wire:model.lazy="name" class="form-control" >
          <input hidden type="text" id="precio_venta" data-type='currency'  wire:model.lazy="price" class="form-control" >



              </div>
              <div class="modal-footer">
                <br>
                <button style="min-width:120px !important;" type="button" wire:click.prevent="resetUI()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
   	            <button style="min-width:120px !important;" class="btn btn-submit" wire:click="Agregar"  title="Agregar al carrito"><i class="fa fa-shopping-cart"></i> AGREGAR</button>
              </div>
          </div>
      </div>
  </div>
