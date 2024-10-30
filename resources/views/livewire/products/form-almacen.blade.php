<div wire:ignore.self class="modal fade" id="Almacen" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>ALMACEN</b> | CREAR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">
    <div class="col-sm-12">
  <input type="text" wire:model.lazy="name_almacen" class="form-control" placeholder="ej: Seccion congelados" >
    @error('name_almacen') <span class="text-danger er">{{ $message }}</span> @enderror
</div>



</div>
</div>
		 <div class="modal-footer">
           <a wire:click.prevent="StoreAlmacen()" href="javascript:void(0);" class="btn btn-submit me-2">GUARDAR</a>
           <a wire:click.prevent="resetUIAlmacen()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
		 </div>
	 </div>
 </div>
</div>
