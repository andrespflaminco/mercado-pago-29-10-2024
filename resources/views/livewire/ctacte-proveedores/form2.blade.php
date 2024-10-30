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
                  <h5 class="modal-title" id="exampleModalLabel">Carrito</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="width: 100% !important;
              margin: 0 !important;" class="modal-body">

              <label for="">Monto total</label>

              <input type="text" wire:model.lazy="monto_total" class="form-control"  >

              <label for="">Pago</label>
              <input type="text" class="form-control" wire:change="MontoPago()" wire:model="pago">

              <label>Metodo de pago</label>

              <select wire:model='metodo_pago_elegido' class="form-control">
                <option value="Elegir" disabled>Elegir</option>
                <option value="1">Efectivo</option>
                @foreach($metodo_pago as $mp)
                  <option value="{{$mp->id}}" >{{$mp->nombre}}</option>
                @endforeach
              </select>
               
              <br><br>
              <i>
                @if($deuda != null)
                <b>Deuda: $ {{$deuda}}</b>
                @endif
              </i>
                	</div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
                @if($cart->hasProducts())
    	           <button class="btn btn-dark close-btn text-light" wire:click.prevent="saveSale"  title="Guardar">
    						<i class="fa fa-shopping-cart"></i> GUARDAR
    						</button>
                @endif
              </div>
          </div>
      </div>
  </div>
