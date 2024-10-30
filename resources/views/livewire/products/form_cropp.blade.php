<div wire:ignore.self class="modal fade" id="ModalCroppr" style="overflow: auto !important;" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

<div class="row">
<div class="col-12"wire:ignore>
    <p>Recorte la imagen</p>
        <!-- Editor donde se recortará la imagen con la ayuda de croppr.js -->
        <div id="editor"></div>

      
        <!-- Previa del recorte -->
        <canvas hidden id="preview"></canvas>

       
        <!-- Muestra de la imagen recortada en Base64 -->
        
        <textarea hidden id="base64"></textarea>
        
        
        
        
</div>

</div>
</div>
 <div style="margin-top:50px;" class="modal-footer">

			 <button type="button" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

			 <button  type="button" wire:click="Base64( $('#base64').val() )" class="btn btn-dark close-modal" >GUARDAR</button>

		 </div>

	
	 </div>
 </div>
</div>
<script type="text/javascript">
function jsSave() {
	var nombre_lw = $("#nombre").val();
	var contenido_lw = $("#contenido").val();
	window.livewire.emit('Contenido', contenido_lw, nombre_lw)
}

</script>
