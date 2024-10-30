<div wire:ignore.self class="modal fade" id="Proveedor" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>PROVEEDORES</b> | AGREGAR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

 <div class="row">
     
     	<div class="col-lg-6 col-sm-6 col-12">
									<div class="form-group">
										<label>Cod proveedor</label>
									  <input maxlength="11" type="text" wire:model.lazy="id_proveedor" class="form-control" placeholder="" >
									  <p style="color:#637381;">* Maximo 11 digitos</p> 
                                         @error('id_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								
  <div class="col-sm-12 col-md-6">
   <div class="form-group">
    <label>Nombre</label>
      <input type="text" wire:model.lazy="nombre_proveedor" class="form-control" placeholder="" >
    @error('nombre_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>


<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Telefono</label>
    <input type="text" wire:model.lazy="telefono_proveedor" class="form-control" placeholder="" >
  @error('telefono_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Mail</label>
    <input type="mail" wire:model.lazy="mail_proveedor" class="form-control" placeholder="" >
  @error('mail_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>

								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Pais</label>
									<select class="form-control" wire:model.lazy="pais_proveedor">
									    <option value="1">Argentina</option>
									</select>
									@error('pais_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Provincia</label>
									<select class="form-control" wire:model="provincia_proveedor">
									    <option value="">Elegir</option>
									    @foreach($provincias as $p)
									    <option value="{{$p->provincia}}">{{$p->provincia}}</option>
									    @endforeach
									</select>
									@error('provincia_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								

								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Ciudad</label>
									<input type="text" wire:model.lazy="localidad_proveedor" class="form-control" placeholder="" >
                                    @error('localidad_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>							

								<div class="col-lg-4 col-sm-6 col-12">
									<div class="form-group">
									<label>Calle</label>
									<input type="text" wire:model.lazy="direccion_proveedor" class="form-control" placeholder="" >
                                     @error('direccion_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
																
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Altura</label>
									<input type="text" wire:model.lazy="altura_proveedor" class="form-control" placeholder="" >
                                     @error('altura_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
																
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Piso</label>
									<input type="text" wire:model.lazy="piso_proveedor" class="form-control" placeholder="" >
                                     @error('piso_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
																
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Departamento</label>
									<input type="text" wire:model.lazy="depto_proveedor" class="form-control" placeholder="" >
                                     @error('depto_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>			
								<div class="col-lg-2 col-sm-6 col-12">
									<div class="form-group">
									<label>Codigo postal</label>
									<input type="text" wire:model.lazy="codigo_postal_proveedor" class="form-control" placeholder="" >
                                     @error('codigo_postal_proveedor') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>


</div>

     <div class="modal-footer">

       <a wire:click.prevent="resetUIProveedor()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
       <a wire:click.prevent="StoreProveedor()" href="javascript:void(0);" class="btn btn-submit me-2">GUARDAR</a>

     </div>
   </div>
 </div>
</div>
</div>