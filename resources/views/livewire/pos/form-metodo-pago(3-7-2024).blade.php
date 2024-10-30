<div wire:ignore.self class="modal fade" id="ModalMetodoPago" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>METODO DE PAGO</b> | CREAR NUEVO
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body" style="width:90%;">

 <div class="row">
  <div class="col-sm-12 col-md-6">
   <div class="form-group">
    <label>Nombre</label>
      <input type="text" wire:model.lazy="nombre_metodo_pago" class="form-control" placeholder="Ej: Cuenta corriente a 60 dias." >
    @error('nombre_metodo_pago') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>
<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Recargo</label>
  <div style="margin-bottom: 0 !important;" class="input-group mb-4">

    <input type="text" wire:model.lazy="recargo_metodo_pago" class="form-control" placeholder="Ej: 10" >
    <div class="input-group-append">
      <span class="input-group-text input-gp">
        %
      </span>
    </div>
      </div>

  @error('recargo_metodo_pago') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>



<div class="col-sm-12 col-md-12">
  <label for="">Se muestra en las sucursales:</label>
  <div style="border: solid 1px #c8c8c8; padding:10px; border-radius:5px;">
 @if(auth()->user()->sucursal != 1)
   <div class="form-group">
    <input type="checkbox" style="margin-right: 10px;" wire:model.defer="muestra_sucursales.{{ $comercio_id }}" >Casa central
  </div>
 @endif
@if(0 < count($sucursales))
    @foreach($sucursales as $s)
   <div class="form-group">
    <input type="checkbox" {{ (auth()->user()->sucursal == 1) && ( $comercio_id != $s->sucursal_id ) ? 'disabled' : '' }} style="margin-right: 10px;" wire:model.defer="muestra_sucursales.{{ $s->sucursal_id }}" >{{$s->name}}
  </div>
  @endforeach
@endif

  </div>

</div>


</div>


</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUIMetodoPago()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>

       <button type="button" wire:click.prevent="StoreMetodoPago()" class="btn btn-submit" >GUARDAR</button>


     </div>
   </div>
 </div>
</div>
