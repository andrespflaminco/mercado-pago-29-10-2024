<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
        
         <div class="row">
           <div class="col-sm-12 col-md-6">
            <div class="form-group">
             <label>Nombre del transportista</label>
             <div class="input-group mb-4">
        
               <input type="text" wire:model="nombre" class="form-control">
        
        
                 </div>
        
           </div>
           </div>
           <div class="col-sm-12 col-md-6">
            <div class="form-group">
             <label>Tipo de transporte</label>
             <select wire:model='tipo' class="form-control">
               <option value="Elegir" disabled >Elegir</option>
                <option value="" >SIN ASIGNAR</option>
               <option value="PROPIO" >PROPIO</option>
               <option value="TERCEROS" >DE TERCEROS</option>
        
             </select>
           </div>
           </div>
        
           <div class="col-sm-12 col-md-6">
            <div class="form-group">
             <label>Fecha de entrega</label>
             <div class="input-group mb-4">
        
               <input type="date" wire:model="fecha" class="form-control" placeholder="Click para elegir">
        
        
                 </div>
        
           </div>
           </div>
        
        <div class="col-sm-12 col-md-6">
         <div class="form-group">
          <label>Turno</label>
          <select wire:model='turno' class="form-control">
            <option value="Elegir" disabled >Elegir</option>
            <option value="" >SIN ASIGNAR</option>
            <option value="MAÑANA" >MAÑANA</option>
            <option value="TARDE" >TARDE</option>
        
          </select>
        </div>
        </div>
        
        <div class="col-sm-12 col-md-12">
         <div class="form-group">
          <label>Obserevaciones:</label>
          <div class="input-group mb-12">
        
          <textarea wire:model="observaciones_hr" class="form-control" rows="3" cols="60"></textarea>
        
        
              </div>
        
        </div>
        </div>
        
        
        </div>
        
        </div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>

       @if($selected_id < 1)
       <button type="button" wire:click.prevent="Store()" class="btn btn-submit" >GUARDAR</button>
       @else
       <button type="button" wire:click.prevent="Update()" class="btn btn-submit" >ACTUALIZAR</button>
       @endif


     </div>
   </div>
 </div>
</div>

