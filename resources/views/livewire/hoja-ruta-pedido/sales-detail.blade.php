<div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>Detalle de Venta # {{$saleId}}</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

        <div class="table-responsive">
          <table class="table table-bordered table striped mt-1">
            <thead class="text-white" style="background: #3B3F5C">
              <tr>
                <th class="table-th text-white text-center">PRODUCTO</th>
                <th class="table-th text-white text-center">PRECIO</th>
                <th class="table-th text-white text-center">CANT</th>
                <th class="table-th text-white text-center">IMPORTE</th>
              </tr>
            </thead>
            <tbody>
              @foreach($details as $d)
              <tr>
                <td class='text-center'><h6>{{$d->product}}</h6></td>
                <td class='text-center'><h6>{{number_format($d->price,2)}}</h6></td>
                <td class='text-center'><h6>{{number_format($d->quantity,0)}}</h6></td>
                <td class='text-center'><h6>{{number_format($d->price * $d->quantity,2)}}</h6></td>

              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2"><h5 class="text-center font-weight-bold"></h5></td>
                <td><h5 class="text-center"></td>
                <td>
                  <p class="text-center">SUBTOTAL: ${{number_format($sumDetails,2)}}</p>
                <br>
                <p class="text-center">RECARGO: ${{number_format($recargoDetails,2)}}</p>
                <br>
                <h6 class="text-center">
                TOTAL:  ${{number_format($sumDetails + $recargoDetails,2)}}
              </h6></td>
              </tr>
            </tfoot>
          </table>
        </div>




      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>
      </div>
    </div>
  </div>
</div>
