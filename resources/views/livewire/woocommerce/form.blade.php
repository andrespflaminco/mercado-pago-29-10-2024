<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>CUPONES</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">

   <div class="col-sm-12 col-md-12">
    <div class="form-group">
     <label>Nombre del cupon</label>
      <input wire:model.prevent="cupon" class="form-control">
       @error('cupon') <span class="text-danger err">{{ $message }}</span> @enderror
   </div>
   </div>
   <div class="col-sm-12 col-md-12">
    <div class="form-group">
     <label>Descuento</label>
     <div style="margin-bottom: 0 !important;" class="input-group mb-4">

       <input type="text" wire:model.lazy="descuento" class="form-control" placeholder="Ej: 10" >
       <div class="input-group-append">
         <span class="input-group-text input-gp">
           %
         </span>
       </div>
         </div>

     @error('descuento') <span class="text-danger er">{{ $message }}</span> @enderror
   </div>
   </div>

</div>

</div>

     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

       @if($selected_id < 1)
       <button type="button" wire:click.prevent="Store()" class="btn btn-dark close-modal" >GUARDAR</button>
       @else
       <button type="button" wire:click.prevent="Update()" class="btn btn-dark close-modal" >ACTUALIZAR</button>
       @endif


     </div>
   </div>
 </div>
</div>
