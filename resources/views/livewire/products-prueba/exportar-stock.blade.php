<!----- Modal EXPORTAR LISTA DE PRECIOS ----->
<div class="modal fade" id="ExportarStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Exportar Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">

              <div class="col-12">
                <br>

                    <button wire:click="ExportarStock('{{'1' . '/' . ($id_categoria == '' ? '0' : $id_categoria)  . '/' . ($id_almacen == '' ? '0' : $id_almacen) . '/' . ($proveedor_elegido == '' ? '1' : $proveedor_elegido)}}')" class="btn btn-dark mr-3">Stock Casa Central</button>

                  @foreach($sucursales as $s)


                    <button wire:click="ExportarStock('{{$s->sucursal_id . '/' . ($id_categoria == '' ? '0' : $id_categoria)  . '/' . ($id_almacen == '' ? '0' : $id_almacen) . '/' . ($proveedor_elegido == '' ? '1' : $proveedor_elegido)}}')" class="btn btn-dark mr-3">Stock {{$s->name}}</button>

                    @endforeach

                <br>

              </div>

            </div>
            <div class="modal-footer">

        </div>
        </div>
    </div>
</div>
