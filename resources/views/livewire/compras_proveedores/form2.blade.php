@inject('cart', 'App\Services\Cart')
<head>
  <style media="screen">
  @media (min-width: 576px) {

.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="theModal2" tabindex="-1" role="dialog">

      <div style="max-width: 1050px !important;
      margin: 1.75rem auto;" class="modal-dialog" role="document">
          <div style=" width: 40%;    margin: 0 auto !important;"  class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Factura</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="width: 100% !important;
              margin: 0 !important;" class="modal-body">

              <label for="">Numero de factura</label>

              <div class="input-group mb-4">
  <div class="input-group-prepend">
  <select style="color: #fff !important; background-color: #3b3f5c; text-align: center; font-weight: 600; padding:10px;" class="form-class" wire:model="tipo_factura">
    <option style="color: #fff !important; background-color: #3b3f5c; text-align: center; font-weight: 600; padding:10px;" value="">Sin factura</option>
    <option style="color: #fff !important; background-color: #3b3f5c; text-align: center; font-weight: 600; padding:10px;" value="A">A</option>
    <option style="color: #fff !important; background-color: #3b3f5c; text-align: center; font-weight: 600; padding:10px;" value="B">B</option>
    <option style="color: #fff !important; background-color: #3b3f5c; text-align: center; font-weight: 600; padding:10px;" value="C">C</option>
  </select>
  </div>
  <input type="text" class="form-control" wire:model="numero_factura">
</div>




              <br><br>
              <i>Para llevar un mejor control de las facturas recibidas y IVA compra le recomendamos que ingrese el numero de factura del proveedor.</i>

                	</div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="CerrarAgregarNroFactura()" class="btn btn-cancel">CERRAR</button>

    	           <button class="btn btn-submit" wire:click.prevent="saveSale"  title="Guardar">
    				 GUARDAR
    				</button>
              </div>
          </div>
      </div>
  </div>
