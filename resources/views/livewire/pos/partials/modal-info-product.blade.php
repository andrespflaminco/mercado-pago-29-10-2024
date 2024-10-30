<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="InfoProducto" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Stock</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>

                @if($producto_casa_central_pasar != null)

                <button value="{{$producto_casa_central->barcode.'|-|'.$producto_casa_central->comercio_id}}" id="code{{$producto_casa_central->comercio_id}}"  wire:click="$emit('scan-code-sucursal', $('#code{{$producto_casa_central->comercio_id}}').val())" wire:click.lazy="selectProduct"
                class="btn btn-dark" title="Click en el producto"> Casa Central ( {{ $producto_casa_central->stock }} unid.)
                </button>

                @endif


                @foreach($stock_sucursales_pasar as $product)

                <button value="{{$product->barcode.'|-|'.$product->referencia_variacion.'|-|'.$product->sucursal_id}}" id="code{{$product->sucursal_id}}"  wire:click="$emit('scan-code-sucursal', $('#code{{$product->sucursal_id}}').val())" wire:click.lazy="selectProduct"
                class="btn btn-dark" title="Click en el producto"> {{ $product->nombre_sucursal }} ( {{ $product->stock_sucursal }} unid.)
                </button>

                @endforeach



              </div>
              <div class="modal-footer">
                <br>
                </div>
          </div>
      </div>
  </div>
