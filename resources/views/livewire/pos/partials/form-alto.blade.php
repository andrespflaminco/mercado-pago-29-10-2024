<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="modalDetailsAlto" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Especificaciones</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>
                <div class="row">

                  
                <div class="col-sm-12 col-md-6">
                	<div class="form-group">
                		<label >Alto</label>
                		<input type="text" wire:model.lazy="alto"
                		class="form-control" placeholder=""  >
                		@error('alto') <span class="text-danger er">{{ $message}}</span>@enderror
                	</div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-group">
                    <label >Ancho</label>
                    <input type="text" wire:model.lazy="ancho"
                    class="form-control" placeholder=""  >
                    @error('ancho') <span class="text-danger er">{{ $message}}</span>@enderror
                  </div>
                </div>

                </div>




              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

                <button type="button" wire:click.prevent="guardarComentario({{$Id_cart}})" class="btn btn-dark close-modal" >GUARDAR</button>
              </div>
          </div>
      </div>
  </div>
