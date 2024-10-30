
<div  wire:ignore.self class="modal fade" id="ElegirCuit" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header text-center">
                  <b>ELEGIR UN PUNTO DE VENTA PARA FACTURAR</b>

              </div>
              
              <div style="margin: 0 auto !important;" class="modal-body">
                <div class="row">
                    <div class="col-12">
                    @if(0 < count($listado_cuits))
                    @foreach($listado_cuits as $lc)
                    <button class="btn btn-cancel w-100 mt-2 mb-2" wire:click="ElegirCuitYFacturar({{$NroVenta}},{{$lc->id}})">
                        {{$lc->razon_social}} - PTO {{$lc->pto_venta}} (CUIT: {{$lc->cuit}})
                    </button>
                    @endforeach     
                    @else
                    <a target="_blank" href="{{ url('puntos-venta') }}" class="btn btn-cancel w-100 mt-2 mb-2">+ AGREGAR DATOS DE FACTURACION </a>
                    @endif
                    </div>
                </div>
              </div>
              <div class="modal-footer">
              </div>
          </div>
      </div>
  </div>

