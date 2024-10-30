 @include('common.modalHead')


 <div class="row">
  <div class="col-sm-12 col-md-6">
   <div class="form-group">
    <label>Nombre</label>
      <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="" >
    @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>


<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Telefono</label>
    <input type="text" wire:model.lazy="telefono" class="form-control" placeholder="" >
  @error('telefono') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Mail</label>
    <input type="mail" wire:model.lazy="mail" class="form-control" placeholder="" >
  @error('mail') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Direccion</label>
    <input type="text" wire:model.lazy="direccion" class="form-control" placeholder="" >
  @error('direccion') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Ciudad</label>
    <input type="text" wire:model.lazy="localidad" class="form-control" placeholder="" >
  @error('localidad') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Provincia</label>
    <input type="text" wire:model.lazy="provincia" class="form-control" placeholder="" >
  @error('provincia') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>



</div>



@include('common.modalFooter')
