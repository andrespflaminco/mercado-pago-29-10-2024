<div wire:ignore.self class="modal fade" id="FormImprimir" tabindex="-1" role="dialog">
  <div style="max-width: 500px !important;" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b> IMPRIMIR </b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
          <br><br>
 <div class="row" style="margin: 0 auto;">
     
     
              <a style="margin-left: 10px !important;" class="btn btn-dark" href="{{ url('report-factura/pdf' . '/' . $ventaId) }}" target="_blank"> DETALLE DE VENTA/FACTURA </a>

              <a style="margin-left: 10px !important;" class="btn btn-dark mt-2" href="{{ url('report-remito/pdf' . '/' . $ventaId) }}" target="_blank"> REMITO </a>

    
</div>
<br><br>

</div>

   </div>
 </div>
</div>

