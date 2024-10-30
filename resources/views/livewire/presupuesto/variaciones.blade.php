<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="Variaciones" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Variaciones</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>

                 @foreach($productos_variaciones_datos as $p)
                 <button class="btn btn-dark" wire:click="BuscarCodeVariacion('{{$barcode.'|-|'.$p->referencia_variacion}}')">
                    {{$p->variaciones}}
                  </button>
                 @endforeach





              </div>
                  <div hidden class="modal-footer">

                        <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

                          <button type="button" wire:click.prevent="ScanVariacion()" class="btn btn-dark close-modal" >ACEPTAR</button>


                 </div>
          </div>
      </div>
  </div>
