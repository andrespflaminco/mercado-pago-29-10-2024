<div wire:ignore.self class="modal fade" id="AbrirCaja" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>NUEVA CAJA</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body" style="width: 100% !important;">
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

       <a href="javascript:void(0)" wire:click.prevent="resetUI()" class="btn btn-cancel">CERRAR</a>

       <a href="javascript:void(0)" id="btn-caja-abrir" wire:loading.attr="disabled" wire:click.prevent="AbrirCaja()" class="btn btn-submit" >
       <span wire:loading.remove>GUARDAR</span>
			 <span class="custom-loader" wire:loading></span>       
       </a>
     </div>
   </div>
 </div>
</div>
<script>
    document.getElementById("btn-caja-abrir").addEventListener("click", btnDisable);

    function btnDisable() {
      document.getElementById("btn-caja-abrir").disabled = true;
    }
</script>