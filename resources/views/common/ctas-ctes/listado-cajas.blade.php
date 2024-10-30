
<div style="z-index: 9999999 !important;" wire:ignore.self class="modal fade" id="ListadoCajas" tabindex="-1" role="dialog">

      <div style="  max-width: 300px !important;
        margin: 1.75rem auto;" class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">
                          Elegir caja
                    </h5>
                  <button type="button" class="close" wire:click="CerrarModalEstado()">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="  margin: 0 auto !important;" class="modal-body">
                <br>

              @foreach($lista_cajas_dia as $lcd)

              <button wire:click.prevent="ElegirCaja({{$lcd->id}})" style="min-width: 200px; width: 100%;"
                  class="btn btn-warning mb-2">
                  Caja nro {{$lcd->nro_caja}}
              </button>
              @endforeach


              </div>
              <div class="modal-footer">
                <br>
              </div>
          </div>
      </div>
  </div>
