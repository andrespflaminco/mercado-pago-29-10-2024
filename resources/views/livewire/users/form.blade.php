@include('common.modalHead')

<div class="row">

<div class="col-sm-12 col-md-8">
	<div class="form-group">
		<label >Nombre</label>
		<input type="text" wire:model.lazy="name"
		class="form-control" placeholder="ej: Luis Fax"  >
		@error('name') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-4">
	<div class="form-group">
		<label >Teléfono</label>
		<input type="text" wire:model.lazy="phone"
		class="form-control" placeholder="ej: 351 115 9550" maxlength="10" >
		@error('phone') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Email</label>
		<input type="text" wire:model.lazy="email"
		class="form-control" placeholder="ej: luisfaax@gmail.com"  >
		@error('email') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Contraseña</label>
		<input type="password" wire:model.lazy="password"
		class="form-control"   >
		@error('password') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Estado</label>
		<select wire:model.lazy="status" class="form-control">
			<option value="Elegir" selected>Elegir</option>
			<option value="Activo">Activo</option>
			<option value="Inactivo">Inactivo</option>
		</select>
		@error('estado_pago') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

@if(auth()->user()->profile === "Admin")

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Estado de pago</label>
		<select wire:model.lazy="confirmed" class="form-control">
			<option value="0" selected>Elegir</option>
			<option value="1" selected>Pago</option>
			<option value="0" selected>No pago</option>
		</select>
		@error('estado_pago') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>


<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Plan a usar</label>
		<select wire:model.lazy="plan_admin" class="form-control">
			<option value="1" selected>Plan basico</option>
			<option value="2" selected>Plan intermedio</option>
			<option value="3" selected>Plan avanzado</option>
		</select>
		@error('plan') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

@endif


<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Asignar Rol</label>
		<select wire:model.lazy="profile" class="form-control">
			<option value="Elegir" selected>Elegir</option>
			@foreach($roles as $role)
			<option value="{{$role->name}}" selected>{{$role->name}}</option>
			@endforeach
		</select>
		@error('profile') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Imágen de Perfil</label>
		<input type="file" wire:model="image" accept="image/x-png, image/jpeg, image/gif" class="form-control">
		@error('image') <span class="text-danger er">{{ $message}}</span>@enderror

	</div>
</div>
</div>


@include('common.modalFooter')
