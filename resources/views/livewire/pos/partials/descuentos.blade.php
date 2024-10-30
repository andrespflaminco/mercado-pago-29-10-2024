<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="Descuentos" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Descuento al producto</h5>
                  <button type="button" class="close" onclick="cerrarModalDescuentos()" >
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>
                <br>
                <label>Porcentaje de descuento (%)</label>
            	<div class="input-group mb-0">				
				<input style="width:100%; text-align:center;" wire:model.defer="descuento_promo_form" type="number" class="form-control">
                <div class="input-group-prepend">
                    <span style="height: 100% !important;" class="input-group-text input-gp">
                    %
                    </span> 
                    </div>
				</div>
                <br>
                <br>
                <label>Cantidad de unidades a las que se le aplica el descuento</label>
            	<div class="input-group mb-0">				
				<input style="width:100%; text-align:center;" max="{{$cantidad_promo_max_form}}" wire:model.defer="cantidad_promo_form" type="number" class="form-control">
	            </div>
                <br>

                <br>
              </div>
              <div class="modal-footer">
                <br>
                <button type="button" onclick="cerrarModalDescuentos()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>

                <button type="button" wire:click.prevent="guardarPromoIndividual('{{$Id_cart}}')" class="btn btn-submit" >GUARDAR</button>
              </div>
          </div>
      </div>
  </div>
