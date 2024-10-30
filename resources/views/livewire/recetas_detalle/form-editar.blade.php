<head>
  <style media="screen">
  @media (min-width: 576px) {

.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="theModalEditar" tabindex="-1" role="dialog">

      <div style="max-width: 300px !important;
      margin: 1.75rem auto;" class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Editar insumo {{$selected_id}}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>

           <input id="cantidad" onchange='calcular_agregar(this)' style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="cantidad" >

           <br>

           <select class="form-control" wire:model="unidad_medida_elegida">
             <option value="Elegir">Elegir</option>

             @foreach($unidad_medida as $ume)

             <option value="{{$ume->id}}">{{$ume->nombre}}</option>
             @endforeach

           </select>


           <input hidden id="stock" style="text-align: center; font-weight: bold;" min="1" type="number" class="form-control" wire:model.lazy="stock" >
           <input hidden type="text" id="selected_id" wire:model.lazy="selected_id" class="form-control" >
          <input hidden id="id_producto" type="text" wire:model.lazy="name" class="form-control" >
          <input hidden type="text" id="costo" data-type='currency' wire:model.lazy="cost" class="form-control" >
          <input hidden type="text" id="precio_venta" data-type='currency'  wire:model.lazy="price" class="form-control" >



              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUI()" button style="min-width:120px !important;" class="btn btn-cancel">CERRAR</button>

                @if($selected_id < 0)
    	        <button button style="min-width:120px !important;" class="btn btn-submit" wire:click="Agregar" > AGREGAR </button>
                @else
                <button button style="min-width:120px !important;" class="btn btn-submit" wire:click="Actualizar({{$selected_id}})">
                ACTUALIZAR
               </button>
                @endif
              </div>
          </div>
      </div>
  </div>
