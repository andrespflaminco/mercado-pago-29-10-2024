<div wire:ignore.self class="modal2 fade" id="theModal2" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>Configuracion</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
<div class="row">

<div class="col-sm-12 col-md-8">
	<div class="form-group">
		<label >Nombre</label>
		<input type="text" wire:model.lazy="name"
		class="form-control product-name" placeholder="ej: Curso Laravel" autofocus >
		@error('name') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-4">
	<div class="form-group">
		<label >Código</label>
		<input type="text" wire:model.lazy="barcode"
		class="form-control"
		{{ $selected_id > 0 ? 'disabled' : '' }}
		placeholder="ej: 025974" >
		@error('barcode') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-4">
	<div class="form-group">
		<label >Costo</label>
		<input type="text" data-type='currency' wire:model.lazy="cost" class="form-control" placeholder="ej: 0.00" >
		@error('cost') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-4">
	<div class="form-group">
		<label >Precio</label>
		<input type="text" data-type='currency' wire:model.lazy="price" class="form-control" placeholder="ej: 0.00" >
		@error('price') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-4">
	<div class="form-group">
		<label >Stock</label>
		<input type="number"  wire:model.lazy="stock" class="form-control" placeholder="ej: 0" >
		@error('stock') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-4">
	<div class="form-group">
		<label >Alertas</label>
		<input type="number"  wire:model.lazy="alerts" class="form-control" placeholder="ej: 10" >
		@error('alerts') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>


<div class="col-sm-12 col-md-4">
<div class="form-group">
	<label>Categoría</label>
	<select wire:model='categoryid' class="form-control">
		<option value="Elegir" disabled>Elegir</option>
		@foreach($categories as $category)
		<option value="{{$category->id}}" >{{$category->name}}</option>
		@endforeach
	</select>
	@error('categoryid') <span class="text-danger er">{{ $message}}</span>@enderror
</div>
</div>



<div class="col-sm-12 col-md-8">
<div class="form-group custom-file">
	<input type="file" class="custom-file-input form-control" wire:model="image"
	accept="image/x-png, image/gif, image/jpeg"
	 >
	 <label class="custom-file-label">Imágen {{$image}}</label>
	 @error('image') <span class="text-danger er">{{ $message}}</span>@enderror
</div>
</div>



</div>




@include('common.modalFooter')
