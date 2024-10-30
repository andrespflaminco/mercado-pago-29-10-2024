<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self style="z-index:99999999999999999 !important;" class="modal fade" id="EditarCliente" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 500px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Modificar el cliente de la venta</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="width: 100% !important;" class="modal-body">
                          <div class="col-lg-12 col-md-12 col-sm-12">
                        <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                          <div class="input-group-prepend">
                            <span class="input-group-text input-gp">
                              <i class="fas fa-clipboard-list"></i>
                            </span>
                          </div>
                
                
                            <input
                                style="font-size:14px !important;"
                                type="text"
                                class="form-control"
                                placeholder="Buscar Cliente"
                                wire:model="query_cliente"
                                wire:keydown.escape="resetCliente"
                                wire:keydown.tab="resetCliente"
                                wire:keydown.enter="selectCliente"
                            />
                         </div>
            

                        @if(!empty($query_cliente))
                            <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>
            
                            <div style="position:absolute;" class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                                @if(!empty($clientes_s))
                                    @foreach($clientes_s as $i => $cliente)
                                    <a style="z-index: 9999;" href="javascript:void(0)"
                                    wire:click="selectCliente({{$cliente['id']}})"
                                    class="btn btn-light" title="Seleccionar">{{ $cliente['id'] }} - {{ $cliente['nombre'] }}
                                    </a>
            
                                    @endforeach
            
                                @else
                                <a class="btn btn-light">
                                    No hay registros para la busqueda
                                </a>
                                @endif
                            </div>
                        @endif



        </div>

              <br><br><br>
            
            @if($id_cliente_elegido != null)
            <h6> Cliente elegido: {{$nombre_cliente_elegido}} </h6>
            @endif
            
              </div>
                  <div class="modal-footer"> 

                        <button type="button" wire:click.prevent="resetCliente()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

                          <button type="button" wire:click.prevent="UpdateCliente()" class="btn btn-dark close-modal" >ACEPTAR</button>


                 </div>
          </div>
      </div>
  </div>
