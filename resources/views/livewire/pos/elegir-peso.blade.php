<head>
  <style media="screen">
    @media (min-width: 576px) {
      .modal-body {
        margin: 0 auto !important;
      }
    }
  </style>
</head>
<div wire:ignore.self class="modal fade" id="ElegirPeso" tabindex="-1" role="dialog">
  <div class="modal-dialog" style="max-width: 450px !important; margin: 1.75rem auto;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> Ingresa el peso </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="modal-body">
          
      <div class="col-lg-12 col-sm-12 col-12">
		<div class="form-group">
		<label>Agregue el peso</label>
	    <div style="margin-bottom: 0 !important;" class="input-group mb-4">
        <input type="number"  wire:model.lazy="peso_agregar" class="form-control">
           <div class="input-group-prepend">
           <span style="height:100%;" class="input-group-text input-gp">
           GR
           </span>
           </div>
        </div>
        @error('peso_agregar') <span class="text-danger err">{{ $message }}</span> @enderror
		</div>
	  </div>
          
      </div>
    
    <div class="modal-footer">
    <br>
    <button style="min-width:120px !important;" type="button" wire:click.prevent="resetUIAgregarPeso()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
    <button style="min-width:120px !important;" class="btn btn-submit" wire:click="AgregarPeso"  >ACEPTAR</button>
    </div>
    
    
    </div>

  </div>
</div>

