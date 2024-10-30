 @include('common.modalHead')


 <div class="row">
  <div class="col-sm-12 col-md-8">
   <div class="form-group">
    <label>Nombre</label>

    @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Código</label>
    <input type="text"  class="form-control" placeholder="ej: 02589" >
  @error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Costo</label>
    <input type="text"  class="form-control" placeholder="ej: 0.00" >
  @error('cost') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Precio</label>
    <input type="text" class="form-control" placeholder="ej: 0.00" >
  @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Categoría</label>
    <select  class="form-control">
      <option value="Elegir" disabled >Elegir</option>

    </select>
    @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Stock</label>
    <input type="number" class="form-control" placeholder="ej: 0" >
  @error('stock') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Inv. minimo</label>
    <input type="number" class="form-control" placeholder="ej: 10" >
  @error('alerts') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Inv. ideal</label>
    <input type="number"  class="form-control" placeholder="ej: 10" >
  @error('alerts') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Seccion del almacen</label>
    <select  class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="1" >Sin almacen</option>

    </select>
    @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Proveedor</label>
    <select  class="form-control">
      <option value="Elegir" disabled >Elegir</option>

    @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Maneja stock?</label>
    <select class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="si" > Activo </option>
      <option value="no" > Inactivo </option>

    </select>
    @error('stock_descubierto') <span class="text-danger err"></span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-8 ">
  <label>Imagen</label>
 <div class="form-group custom-file">

  <input type="file" class="custom-file-input" accept="image/x-png, image/gif, image/jpeg"  class="form-control">
  <label class="custom-file-label" for="customFile"></label>
  @error('image') <span class="error">{{ $message }}</span> @enderror
</div>
</div>



</div>


@include('common.modalFooter')
