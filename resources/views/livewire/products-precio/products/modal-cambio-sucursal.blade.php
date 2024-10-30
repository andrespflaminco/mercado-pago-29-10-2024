<!-- Modal -->
<div class="modal fade" id="ModalCambioSucursal" tabindex="-1" role="dialog" aria-labelledby="ModalCambioSucursal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Elegir sucursal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
              <label for="">Elegir sucursal de destino</label>
              <select class="form-control" wire:model="sucursal_destino">
                <option value="Elegir" selected>Elegir</option>
                @foreach($sucursales as $item)
                <option value="{{$item->sucursal_id}}">{{$item->name}} </option>
                @endforeach
              </select>


              <br><br><br>

              <table>
                <thead>
                  <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sucursales as $item)
                  <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->stock}}</td>
                    <td>
                      <input type="text" class="form-control" value="">
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              productos_sucursal
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> CERRAR</button>
                <button type="button" class="btn btn-dark">GUARDAR</button>
            </div>
        </div>
    </div>
</div>
