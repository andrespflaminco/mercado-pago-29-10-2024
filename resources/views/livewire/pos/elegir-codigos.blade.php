<head>
  <style media="screen">
    @media (min-width: 576px) {
      .modal-body {
        margin: 0 auto !important;
      }
    }
  </style>
</head>
<div wire:ignore.self class="modal fade" id="ElegirCodigos" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width: 450px !important; margin: 1.75rem auto;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Hemos encontrado 2 códigos iguales</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <br>
        <h5>Elige el producto que quieres agregar</h5>
        <br>
        @if(0 < count($datos_pesables))
        <button class="btn btn-dark w-100 mb-3" wire:click="ScanearCode('{{$codigo_original}}', '{{$datos_pesables[1]}}', false, 1)">
           {{$datos_pesables[0]}} - {{$datos_pesables[2]}} x {{$datos_pesables[1]}} Kg
        </button>
        <button class="btn btn-dark w-100 mb-3" wire:click="ScanearCode('{{$codigo_original}}', 1, false, 2)">
          {{$codigo_original}} - {{$datos_no_pesables->name}} x 1 Unid
        </button>
        @endif
      </div>
    </div>
  </div>
</div>

