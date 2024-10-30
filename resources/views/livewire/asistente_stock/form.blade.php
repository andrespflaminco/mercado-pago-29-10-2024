 @include('common.modalHead')


 <div class="row">
  <div class="col-sm-12 col-md-8">
   <div class="form-group">
    <label>Nombre</label>
      <input type="text" wire:model.lazy="name" class="form-control" placeholder="ej: Caja de helados" >
    @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Código</label>
    <input type="text" wire:model.lazy="barcode" class="form-control" placeholder="ej: 02589" >
  @error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Costo</label>
    <input type="text" wire:model.lazy="cost" class="form-control" placeholder="ej: 0.00" >
  @error('cost') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Precio</label>
    <input type="text" wire:model.lazy="price" class="form-control" placeholder="ej: 0.00" >
  @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Categoría</label>
    <select wire:model='categoryid' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      @foreach($categories as $c)
      <option value="{{$c->id}}">{{$c->name}}</option>
      @endforeach
    </select>
    @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Stock</label>
    <input type="number" wire:model.lazy="stock" class="form-control" placeholder="ej: 0" >
  @error('stock') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Inv. minimo</label>
    <input type="number" wire:model.lazy="alerts" class="form-control" placeholder="ej: 10" >
  @error('alerts') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Inv. ideal</label>
    <input type="number" wire:model.lazy="inv_ideal" class="form-control" placeholder="ej: 10" >
  @error('alerts') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Seccion del almacen</label>
    <select wire:model='almacen' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="1" >Sin almacen</option>
      @foreach($almacenes as $a)
      <option value="{{$a->id}}">{{$a->nombre}}</option>
      @endforeach
    </select>
    @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Proveedor</label>
    <select wire:model='proveedor' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="1" >Sin proveedor</option>
      @foreach($prov as $pr)
      <option value="{{$pr->id}}">{{$pr->nombre}}</option>
      @endforeach
    </select>
    @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Maneja stock?</label>
    <select wire:model='stock_descubierto' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="si" > Activo </option>
      <option value="no" > Inactivo </option>

    </select>
    @error('stock_descubierto') <span class="text-danger err">{{ $stock_descubierto }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-8 ">
  <label>Imagen</label>
 <div class="form-group custom-file">

  <input type="file" class="custom-file-input" wire:model="image" accept="image/x-png, image/gif, image/jpeg"  class="form-control">
  <label class="custom-file-label" for="customFile">Imágen {{$image}}</label>
  @error('image') <span class="error">{{ $message }}</span> @enderror
</div>
</div>



</div>



@include('common.modalFooter')
