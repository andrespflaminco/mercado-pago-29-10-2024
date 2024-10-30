<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

<div class="row">

<div class="col-sm-12 col-md-8">
	<div class="form-group">
		<label >Nombre</label>
		<input type="text" wire:model.lazy="nombre"
		class="form-control" placeholder="ej: Juan Perez"  >
		@error('nombre') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-4">
	<div class="form-group">
		<label >Teléfono</label>
		<input type="text" wire:model.lazy="telefono"
		class="form-control" placeholder="ej: 351 115 9550" maxlength="10" >
		@error('telefono') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Email</label>
		<input type="text" wire:model.lazy="email"
		class="form-control" placeholder="ej: juanperez@gmail.com"  >
		@error('email') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >CUIT </label>
		<input type="text" wire:model.lazy="dni" class="form-control" placeholder="">
		@error('dni') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Provincia</label>
		<input type="text" wire:model.lazy="provincia"
		class="form-control" placeholder="ej: Cordoba"  >
		@error('provincia') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Ciudad</label>
		<input type="text" wire:model.lazy="localidad"
		class="form-control" placeholder="ej: Cordoba"  >
		@error('localidad') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Barrio</label>
		<input type="text" wire:model.lazy="barrio"
		class="form-control" placeholder="ej: Nueva Cordoba"  >
		@error('barrio') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Direccion</label>
		<input type="text" wire:model.lazy="direccion"
		class="form-control" placeholder="ej: Independencia 105"  >
		@error('direccion') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Estatus</label>
		<select wire:model.lazy="status" class="form-control">
			<option value="Elegir" selected>Elegir</option>
			<option value="Active" selected>Activo</option>
			<option value="Locked" selected>Bloqueado</option>
		</select>
		@error('status') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

@if($lista_precios != null)

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Lista de precios del cliente</label>
		<select wire:model.lazy="lista_precio_cliente" class="form-control">
			<option value="Elegir" selected>Elegir</option>
			<option value="0"> Precio base </option>
			@foreach($lista_precios as $lp)
			<option value="{{$lp->id}}">{{$lp->nombre}}</option>
			@endforeach
		</select>
		@error('lista_precio_cliente') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

@endif

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Imágen de Perfil</label>
		<input type="file" wire:model="image" accept="image/x-png, image/jpeg, image/gif" class="form-control">
		@error('image') <span class="text-danger er">{{ $message}}</span>@enderror

	</div>
</div>
<div class="col-sm-12 col-md-6">

</div>
<div class="col-sm-12 col-md-12">
	<div class="form-group">
		<label >Observaciones</label>
		<textarea class="form-control" wire:model="observaciones" style="width:100%;">
		    
		</textarea>

	</div>
</div>

<!----- GOOGLE MAPS ----->







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
