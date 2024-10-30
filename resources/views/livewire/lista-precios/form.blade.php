<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR # '.$selected_id : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
<div class="row">

<div class="col-sm-12">
	<label for="">Nombre</label>
	<div class="input-group">

		<div class="input-group-prepend">
			<span class="input-group-text">
				<span class="fas fa-edit">

				</span>
			</span>
		</div>
		<input type="text" wire:model.lazy="nombre" class="form-control" placeholder="ej: Mayorista" maxlength="255">
	</div>
	@error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
</div>


<div class="col-sm-12 mt-3">
	<div class="form-group custom-file">
		<label for="">WooCommerce Key de la lista de precios</label>
		<input wire:model="wc_key" class="form-control" style="width:100%;" >

		@error('wc_key') <span class="text-danger er">{{ $message }}</span> @enderror
	</div>
</div>



<div hidden class="col-sm-12 col-md-12">
 <div class="form-group">
     <label>Sucursales</label>
     <br>
    <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

      <div class="row col-12">

				<div class="col-4">
					<input type="checkbox" wire:model.defer="sucursales_elegidas" value="{{auth()->user()->id}}"> {{auth()->user()->name}}

			</div>

        @foreach ($sucursales as $llave => $suc)

                  <div class="col-4">
										<input type="checkbox" wire:model.defer="sucursales_elegidas" value="{{$suc->id}}"> {{$suc->name}}

                </div>

    				@endforeach




      </div>

     </div>



</div>
</div>



<div class="col-sm-12 mt-3">
	<div class="form-group custom-file">
		<label for="">Descripcion</label>
		<textarea wire:model="descripcion" name="name" class="form-control" style="width:100%;" rows="8" cols="80">
		</textarea>
		@error('descripcion') <span class="text-danger er">{{ $message }}</span> @enderror
	</div>
</div>



</div>



@include('common.modalFooter')
