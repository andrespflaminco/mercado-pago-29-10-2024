<div wire:ignore.self class="modal fade" id="Almacen" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>ALMACEN</b> | CREAR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">
    <div class="col-sm-12">
    <div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">
      <span class="fas fa-edit"></span>
    </span>
  </div>
  <input type="text" wire:model.lazy="name_almacen" class="form-control" placeholder="ej: Seccion congelados" >
</div>
    @error('name_almacen') <span class="text-danger er">{{ $message }}</span> @enderror
</div>



</div>
</div>
		 <div class="modal-footer">

			 <button type="button" wire:click.prevent="resetUIAlmacen()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>


			 <button type="button" wire:click.prevent="StoreAlmacen()" class="btn btn-dark close-modal" >GUARDAR</button>


		 </div>
	 </div>
 </div>
</div>
