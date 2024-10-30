<div wire:ignore.self class="modal fade" id="theModalFacturacion" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>Datos de facturacion</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">


<div class="row">


<!------------- DATOS FACTURACION  ---------------------------->

<br>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Razon social</label>
		<input type="text" wire:model.lazy="razon_social"
		class="form-control" placeholder=""  >
		@error('razon_social') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>
<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Domicilio comercial</label>
		<input type="text" wire:model.lazy="domicilio_comercial"
		class="form-control" placeholder=""  >
		@error('domicilio_comercial') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >CUIT</label>
		<input type="text" wire:model.lazy="cuit"
		class="form-control" placeholder=""  >
		@error('cuit') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Condicion ante el IVA</label>
		<select wire:model.lazy="condicion_iva" class="form-control">
			<option value="Elegir" selected>Elegir</option>
			<option value="IVA Responsable inscripto" >IVA Responsable inscripto</option>
			<option value="IVA exento" >IVA exento</option>
			<option value="Monotributo" >Monotributo</option>

		</select>
		@error('condicion_iva') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>

<div class="col-sm-12 col-md-6">
	<div class="form-group">
		<label >Ingresos brutos</label>
		<input type="text" wire:model.lazy="iibb"
		class="form-control" placeholder=""  >
		@error('iibb') <span class="text-danger er">{{ $message}}</span>@enderror
	</div>
</div>




</div>
</div>
		 <div class="modal-footer">

			 <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

			 @if($selected_id_facturacion < 1)
			 <button type="button" wire:click.prevent="StoreFacturacion()" class="btn btn-dark close-modal" >GUARDAR</button>
			 @else
			 <button type="button" wire:click.prevent="UpdateFacturacion()" class="btn btn-dark close-modal" >ACTUALIZAR</button>
			 @endif


		 </div>
	 </div>
 </div>
</div>
