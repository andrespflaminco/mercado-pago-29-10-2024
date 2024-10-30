 @include('common.modalHead')


 <div class="row">
  <div class="col-sm-12 col-md-6">
   <div class="form-group">
    <label>Nombre del banco</label>
      <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Banco santander" >
    @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>
<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>CBU</label>
  <input type="text" wire:model.lazy="CBU" class="form-control" placeholder="Ej: 2009XXX " >
@error('CBU') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>CUIT</label>
    <input type="text" wire:model.lazy="cuit" class="form-control" placeholder="Ej: 20-32XXX "  >
  @error('cuit') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Tipo</label>
    <select wire:model='tipo' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="2">Banco</option>
      <option value="3">Plataforma de pago</option>
    </select>
    @error('tipo') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

@if(count($sucursales))

<div class="col-sm-12 col-md-12">
  <label for="">Se muestra en las sucursales:</label>
  <div style="border: solid 1px #c8c8c8; padding:10px; border-radius:5px;">
    @foreach($sucursales as $s)
   <div class="form-group">
    <input type="checkbox" wire:model="muestra_sucursales.{{ $s->id }}" ><label style="margin-left: 10px;">{{$s->nombre_sucursal}}</label>
  </div>
  @endforeach
  </div>

</div>

@endif


</div>



@include('common.modalFooter')
