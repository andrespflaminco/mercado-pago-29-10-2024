 @include('common.modalHead')


 <div class="row">

      <div class="form-group  col-12">
       <label>Nombre</label>
         <input type="text" wire:model.lazy="name" class="form-control" placeholder="ej: Caja de helados" >
       @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
     </div>
     <div class="form-group col-6">
      <label>Código</label>
        <input type="text" wire:model.lazy="barcode" class="form-control" placeholder="ej: 02589" >
      @error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
    </div>
    <div class="form-group col-6">
     <label>Costo</label>
       <input type="text" wire:model.lazy="cost" class="form-control" placeholder="ej: 0.00" >
     @error('cost') <span class="text-danger er">{{ $message }}</span> @enderror
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
  <label>Proveedor</label>
    <select wire:model='proveedor' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="1" >Sin proveedor</option>
      @foreach($prov as $pr)
      <option value="{{$pr->id}}">{{$pr->nombre}}</option>
      @endforeach
    </select>
    @error('proveedor') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-12" style="border-bottom: solid 1px #eee; padding: 15px; margin-bottom: 10px;">
Contenido del insumo
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Cantidad</label>
    <input type="number" wire:model.lazy="cantidad" class="form-control" placeholder="ej: 0" >
  @error('cantidad') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>


<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Tipo de unidad de medida</label>
    <select wire:model='tipo_unidad_medida' class="form-control">
      <option value="Elegir" selected >Elegir</option>
      @foreach($tipo_unidad_medida_select as $tu)
      <option value="{{$tu->id}}">{{$tu->nombre}}</option>
      @endforeach
    </select>
    @error('tipo_unidad_medida') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

@if($tipo_unidad_medida != "Elegir")
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Unidad de medida</label>
    <select wire:model='unidad_medida' class="form-control">
      <option value="Elegir" selected >Elegir</option>
      @foreach($unidad_medida_select as $pr)
      <option value="{{$pr->id}}">{{$pr->nombre_completo}} ( {{$pr->nombre}} )</option>
      @endforeach
    </select>
    @error('unidad_medida') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
@endif



</div>



@include('common.modalFooter')
