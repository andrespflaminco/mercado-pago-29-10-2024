<div wire:ignore.self class="modal fade" id="Categoria" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>CATEGORIA</b> | CREAR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

<div class="row">

<div class="col-sm-12">
	<div class="input-group">
		<div class="input-group-prepend">
			<span class="input-group-text">
				<span class="fas fa-edit">

				</span>
			</span>
		</div>
		<input type="text" wire:model.lazy="name_categoria" class="form-control" placeholder="ej: Remeras" maxlength="255">
	</div>
	@error('name_categoria') <span class="text-danger er">{{ $message }}</span> @enderror
</div>

<div class="col-sm-12 mt-3">
	<div class="form-group custom-file">
		<input type="file" class="custom-file-input form-control" wire:model="image_categoria" accept="image/x-png, image/gif, image/jpeg" >
		<label  class="custom-file-label">Imágen {{$image_categoria}}</label>
		@error('image_categoria') <span class="text-danger er">{{ $message }}</span> @enderror
	</div>
</div>



</div>
</div>
		 <div class="modal-footer">

			 <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>


			 <button type="button" wire:click.prevent="StoreCategoria()" class="btn btn-dark close-modal" >GUARDAR</button>


		 </div>
	 </div>
 </div>
</div>
