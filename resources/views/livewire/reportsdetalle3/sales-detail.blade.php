<div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" role="dialog" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header  bg-dark">
        <h5 class="modal-title text-white" id="exampleModalLabel">
          <b>Detalle de Venta # {{$saleId}}</b>
        </h5> 
        <h6 class="text-center text-white" wire:loading>PROCESANDO...</h6>      
      </div>

      <div class="modal-body">
<div class="table-responsive">
          <table  class="table table-bordered table-striped  mt-1">
            <thead class="text-white" style="background: #3B3F5C">
              <tr>
                <th class="table-th text-center text-white">FOLIO</th>
                <th class="table-th text-center text-white">PRODUCTO</th>
                <th class="table-th text-center text-white">PRECIO</th>
                <th class="table-th text-center text-white">CANT</th>
                <th class="table-th text-center text-white">IMPORTE</th>

              </tr>
            </thead>
            <tbody>              
              @foreach($details as $d)
              <tr>                
                <td class="text-center"><h6>{{$d->id}}</h6></td>              
                <td class="text-center"><h6>{{$d->product}}</h6></td>
                <td class="text-center"><h6>${{number_format($d->price,2)}}</h6></td>
                <td class="text-center"><h6>{{number_format($d->quantity,0)}}</h6></td>
                <td class="text-center"><h6>${{number_format($d->quantity*$d->price,2)}}</h6>
                </td>
                

              </tr>

              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3"><h5 class="text-center font-weight-bold">TOTALES</h5></td>
                <td ><h5 class="text-center">                
                    {{$countDetails}}
                </h5></td>
                <td ><h5 class="text-center">
                    ${{number_format($sumDetails,2)}}
                </h5></td>
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