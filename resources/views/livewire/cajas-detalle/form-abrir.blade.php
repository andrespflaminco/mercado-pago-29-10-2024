<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$componentName}}</b> | ABRIR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">
<div class="col-sm-12 col-md-2">

  </div>
<div class="col-sm-12 col-md-8">
 <div class="form-group">
  <label>Monto inicial</label>
  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
    <div class="input-group-prepend">
      <span class="input-group-text input-gp">
        $
      </span>
    </div>
    <input type="text" wire:model.lazy="monto_inicial" required class="form-control" placeholder="Ej: 10" >

      </div>
</div>
</div>
<div class="col-sm-12 col-md-2">

  </div>



</div>
</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

       <button type="button" wire:click.prevent="AbrirCaja()" class="btn btn-dark close-modal" >GUARDAR</button>



     </div>
   </div>
 </div>
</div>
