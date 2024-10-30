<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">
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
          <div class="col-sm-12">
          <label>Nombre del Rol</label>
            <div class="input-group">
              <div class="input-group-prepend">

              </div>
              <input type="text" wire:model.lazy="roleName" class="form-control" placeholder="ej: Admin" maxlength="255">
            </div>
            @error('roleName') <span class="text-danger er">{{ $message }}</span> @enderror
          </div>
          
        
        @if(auth()->user()->sucursal != 1) 
        <div class="col-sm-12">
            <label>Se muestra en las sucursales?</label>
            <div class="input-group">
              <div class="input-group-prepend">

              </div>
              <select class="form-control" wire:model="mostrar_en_sucursales">
                  <option value="1">SI</option>
                  <option value="0">NO</option>
              </select>
            </div>
            @error('mostrar_en_sucursales') <span class="text-danger er">{{ $message }}</span> @enderror
        </div>
        @endif
        
    
        </div>


      </div>
      <div class="modal-footer">

        <button type="button" wire:click.prevent="Close()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>

        @if($selected_id < 1)
        <button type="button" wire:click.prevent="CreateRole()" class="btn btn-submit" >GUARDAR</button>
        @else
        <button type="button" wire:click.prevent="UpdateRole()" class="btn btn-submit" >ACTUALIZAR</button>
        @endif


      </div>
    </div>
  </div>
</div>