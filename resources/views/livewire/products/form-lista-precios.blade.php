<div wire:ignore.self class="modal fade" id="theModalListaPrecios" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>LISTA DE PRECIOS</b> | CREAR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
<div class="row">

<div class="col-sm-12">
	<label for="">Nombre</label>
	<div class="input-group">

		<div class="input-group-prepend">
			<span class="input-group-text">
				<span class="fas fa-edit">

				</span>
			</span>
		</div>
		<input type="text" wire:model.lazy="nombre_lista" class="form-control" placeholder="ej: Mayorista" maxlength="255">
	</div>
	@error('nombre_lista') <span class="text-danger er">{{ $message }}</span> @enderror
</div>


<div class="col-sm-12 mt-3">
	<div class="form-group custom-file">
		<label for="">WooCommerce Key de la lista de precios</label>
		<input {{$wc_yes == 0 ? 'disabled' : '' }} wire:model="wc_key_lista" class="form-control" style="width:100%;" >

		@error('wc_key_lista') <span class="text-danger er">{{ $message }}</span> @enderror
	</div>
</div>


<div class="col-sm-12 mt-3">
	<div class="form-group custom-file">
		<label for="">Descripcion</label>
		<textarea wire:model="descripcion_lista" name="name" class="form-control" style="width:100%;" rows="8" cols="80">
		</textarea>
		@error('descripcion_lista') <span class="text-danger er">{{ $message }}</span> @enderror
	</div>
</div>



</div>


</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUIListaPrecios()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

       <button type="button" wire:click.prevent="StoreListaPrecio()" class="btn btn-dark close-modal" >GUARDAR</button>
       

     </div>
   </div>
 </div>
</div>
