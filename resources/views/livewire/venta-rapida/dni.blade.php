
<div wire:ignore.self class="modal fade" id="DNI" tabindex="-1" role="dialog">

      <div style="max-width: 500px !important;
      margin: 1.75rem auto;" class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Buscar DNI</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="width:100%;" class="modal-body">
                <br>
       <label for="">DNI</label>
        <input type="text" class="form-control"	wire:model="cliente_cuit"/>   
        <br>
       <label for="">Genero</label>
        <select class="form-control" wire:model="genero_dni">
            <option value="Elegir" selected>Elegir</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
            <option value="S">Sociedad</option>
        </select>
        
        <br><br><br>
              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUIDNI()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
                <button class="btn btn-submit" wire:click="getCuilCuit"  title="Buscar">
                   BUSCAR
              	</button>
              </div>
          </div>
      </div>
  </div>
