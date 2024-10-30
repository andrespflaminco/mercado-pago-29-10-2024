<div wire:ignore.self class="modal fade" id="ModalBanco" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>BANCOS</b> | CREAR NUEVO
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">
  <div class="col-sm-12 col-md-6">
   <div class="form-group">
    <label>Nombre del banco</label>
      <input type="text" wire:model.lazy="nombre_banco" class="form-control" placeholder="Ej: Banco santander" >
    @error('nombre_banco') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>
<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>CBU</label>
  <input type="text" wire:model.lazy="CBU" class="form-control" placeholder="Ej: 2009XXX " >
@error('CBU') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>CUIT</label>
    <input type="text" wire:model.lazy="cuit_banco" class="form-control" placeholder="Ej: 20-32XXX "  >
  @error('cuit_banco') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Tipo</label>
    <select wire:model='tipo_banco' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="2">Banco</option>
      <option value="3">Plataforma de pago</option>
    </select>
    @error('tipo_banco') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>


</div>

@if(count($sucursales))

<div class="col-sm-12 col-md-12">
  <label for="">Se muestra en las sucursales:</label>
  <div style="border: solid 1px #c8c8c8; padding:10px; border-radius:5px;">
    @foreach($sucursales as $s)
    
   <div class="form-group">
    <input type="checkbox" wire:model="muestra_sucursales.{{ $s->sucursal_id }}" ><label style="margin-left: 10px;">{{$s->name}}</label>
  </div>
  @endforeach
  </div>

</div>

@endif

</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUIBanco()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

       <button type="button" wire:click.prevent="StoreBanco()" class="btn btn-dark close-modal" >GUARDAR</button>


     </div>
   </div>
 </div>
</div>
