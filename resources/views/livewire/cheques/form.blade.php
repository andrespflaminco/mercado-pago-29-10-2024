@include('common.modalHead')


<br>
<div class="row">

	<div class="col-sm-12 col-md-4">
	 <div class="form-group">
		<label>Numero de cheque</label>
		<input type="text" wire:model="nro_cheque_ch" class="form-control">
		@error('nro_cheque_ch') <span class="text-danger err">{{ $message }}</span> @enderror


	</div>
	</div>

	<div class="col-sm-12 col-md-4">
	 <div class="form-group">
		<label>Emisor</label>
		<input type="text" wire:model="emisor_ch" class="form-control">
		@error('emisor_ch') <span class="text-danger err">{{ $message }}</span> @enderror


	</div>
	</div>

	<div class="col-sm-12 col-md-4">
	 <div class="form-group">
		<label>Banco</label>
		<input type="text" wire:model="banco_ch" class="form-control">
		@error('banco_ch') <span class="text-danger err">{{ $message }}</span> @enderror


	</div>
	</div>


		<div class="col-sm-12 col-md-4">
		 <div class="form-group">
			<label>Monto</label>
			<input type="text" wire:model="monto_ch" class="form-control">
			@error('monto_ch') <span class="text-danger err">{{ $message }}</span> @enderror


		</div>
		</div>



			<div class="col-sm-12 col-md-8">
			 <div class="form-group">
				<label>Cliente</label>
				<select wire:model="cliente_id_ch" class="form-control">
					<option value="Elegir">Elegir</option>
					@foreach($clientes as $c)
					<option value="{{$c->id}}">{{$c->nombre}}</option>
					@endforeach
				</select>
				@error('cliente_id_ch') <span class="text-danger err">{{ $message }}</span> @enderror


			</div>
			</div>

	<div class="col-sm-12 col-md-4">
	 <div class="form-group">
		<label>Fecha de emision</label>
		<input type="date" wire:model="fecha_emision_ch" class="form-control">
		@error('fecha_cobro_ch') <span class="text-danger err">{{ $message }}</span> @enderror


	</div>
	</div>
	<div class="col-sm-12 col-md-4">
	 <div class="form-group">
		<label>Fecha de cobro</label>
		<input type="date" wire:model="fecha_cobro_ch" class="form-control flatpickr flatpickr-input">
		@error('fecha_emision_ch') <span class="text-danger err">{{ $message }}</span> @enderror


	</div>
	</div>


</div>


@include('common.modalFooter')
