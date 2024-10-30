<head>
  <style media="screen">
  @media (min-width: 576px) {

.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="datos-cliente" tabindex="-1" role="dialog">

      <div style="max-width: 500px !important;
      margin: 1.75rem auto;" class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Cliente</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="width:100%;" class="modal-body">
                <br>

                @if($mensaje == null)
                Nombre del cliente: {{$nombre_cliente}} {{$apellido_cliente}} <br>
                {{$tipo_clave}}:  {{$cuit}} <br>
                Direccion Fiscal: <br>
                {{$direccion}} , {{$localidad}} - {{$provincia}} <br>
                PERSONA {{$tipo_persona}}
                @else
                {{$mensaje}}
                @endif

           <br><br><br>
              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetCliente()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

    	           <button class="btn btn-dark close-btn text-light" wire:click="AgregarCliente"  title="Agregar">
                   AGREGAR
              	</button>
              </div>
          </div>
      </div>
  </div>
