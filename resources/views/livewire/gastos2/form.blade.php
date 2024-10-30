 @include('common.modalHead')


 <div class="row">
  <div class="col-sm-12 col-md-8">
   <div class="form-group">
    <label>Nombre</label>
      <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Alquiler octubre 2021" >
    @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Categoria</label>
  <select wire:model='categoria' class="form-control">
    <option value="Elegir" disabled >Elegir</option>
    <option value="Alquileres" >Alquileres</option>
    <option value="Limpieza" >Limpieza</option>
    <option value="Impuestos" >Impuestos</option>
    <option value="Proveedores" >Proveedores</option>
    <option value="Otros" >Otros</option>
  </select>
  @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror


</div>
</div>

<div class="col-sm-12 col-md-8">
 <div class="form-group">
  <label>Monto</label>
  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
    <div class="input-group-prepend">
      <span class="input-group-text input-gp">
        $
      </span>
    </div>
    <input type="text" wire:model.lazy="monto" class="form-control" placeholder="Ej: 10" >

@error('monto') <span class="text-danger err">{{ $message }}</span> @enderror
      </div>
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Etiquetas</label>
  <select wire:model='categoria' class="form-control">
    <option value="Elegir" disabled >Elegir</option>
    @foreach($etiquetas as $et)
      <option value="{{$et->id}}" >{{$et->nombre}}</option>
    @endforeach
  </select>
  @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror


</div>
</div>


</div>



@include('common.modalFooter')
