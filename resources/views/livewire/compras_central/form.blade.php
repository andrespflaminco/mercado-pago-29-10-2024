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
                  <button type="button" class="close" wire:click.prevent="resetUI()" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
            <br>  
           <h6>Cod: {{$barcode}}</h6>
           <h6> {{$name}}</h6>
           <br>
          <label style="text-align:center;" for="">Cantidad</label>
           <input id="cantidad" max="{{$stock}}" onchange='calcular_agregar(this)' style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="cantidad" >
           <p>Stock disponible: {{$stock}} unid.</p>
           <br>
           <label style="text-align:center;" for="">Costo</label>
            <h6 style="text-align:center;">$ {{ $cost }}</h6>
           
           
           <input hidden id="costo" style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="cost" >
            
           <input hidden id="stock" style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="stock" >
           <input hidden type="text" id="selected_id" wire:model.lazy="selected_id" class="form-control" >
          <input hidden id="id_producto" type="text" wire:model.lazy="name" class="form-control" >
          <input hidden type="text" id="precio_venta" data-type='currency'  wire:model.lazy="price" class="form-control" >



              </div>
              <div class="modal-footer">
                  <div class="d-flex">
                    <div class="col-6"><a href="javascript:void(0);"  wire:click.prevent="resetUI()" style="margin-right:5px;"  class="btn btn-cancel" data-dismiss="modal">Cerrar</a></div>
                    <div class="col-6"><a href="javascript:void(0);"  class="btn btn-submit" style="margin-left:5px;" wire:click="Agregar" >Agregar</a>  </div>
                      
                  </div>
               </div>
          </div>
      </div>
  </div>
