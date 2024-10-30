<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div style="z-index: 99999 !important" wire:ignore.self class="modal fade" id="AgregarPago" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Agregar pago</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>
                <div class="row">
                  <div class="col-sm-12 col-md-6">
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">

                   <input autocomplete="off" type="date" wire:model.lazy="fecha_ap" class="form-control" required="">



               </div>
               </div>
                  <div class="col-sm-12 col-md-6">
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input autocomplete="off" type="text" id="title" wire:model.lazy="monto_ap" class="form-control" required="">



               </div>
               </div>
               <br>
                 <div class="col-sm-12 col-md-12">
                  <div class="form-group">
                   <label></label>
                   <select wire:model='metodo_pago_ap' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>
                     @foreach($metodos as $mp)
                     <option value="{{$mp->id}}">{{$mp->nombre}}</option>
                     @endforeach
                   </select>
                 </div>

                 </div>


              </div>

      


              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
                <button type="button" wire:click.prevent="CreatePago({{$id_pago}})" class="btn btn-dark close-modal" >GUARDAR</button>
              </div>
          </div>
      </div>
  </div>
