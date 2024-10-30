<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b> Movimiento de cuenta </b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body" style="padding: 5%;">


 <div class="row">

<div class="col-12">

@if($id_compra_modal > 0)

<h6> <b>Compra # {{$id_modal}}</b> </h6>  <br>

<h6> <b>Total: $ {{$monto_modal}}</b> </h6>  <br>

@endif



@if($id_pago_modal > 0)

<h6> <b>Pago # {{$id_modal}}</b> </h6>  <br>

@if($banco_modal != null)
<h6> <b>Banco:  {{$banco_modal}}</b> </h6>  <br>
@endif

@if($banco_modal != null)

<h6> <b>Metodo de pago: {{$metodo_pago_modal}}</b> </h6>  <br>

@endif

<h6> <b>Total: $ {{$monto_modal}}</b> </h6>  <br>

@endif

</div>

</div>

</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

     </div>
   </div>
 </div>
</div>
