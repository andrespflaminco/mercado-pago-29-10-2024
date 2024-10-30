<div wire:ignore.self class="modal fade" id="Categoria" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>CATEGORIA</b> | CREAR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

<div class="row">

<div class="col-sm-12">
		<input type="text" wire:model.lazy="name_categoria" class="form-control" placeholder="ej: Remeras" maxlength="255">
	@error('name_categoria') <span class="text-danger er">{{ $message }}</span> @enderror
</div>



</div>
</div>
		 <div class="modal-footer">

           <a wire:click.prevent="resetUICategoria()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
           <a wire:click.prevent="StoreCategoria()" href="javascript:void(0);" class="btn btn-submit me-2">GUARDAR</a>

		 </div>
	 </div>
 </div>
</div>
