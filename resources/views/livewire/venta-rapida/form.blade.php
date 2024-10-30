
<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">

      <div style="max-width: 500px !important;
      margin: 1.75rem auto;" class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Agregar concepto</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="width:100%;" class="modal-body">
                <br>

                <label for="">Concepto</label>
               <input
                     style="font-size:14px !important;"
                     type="text"
                     class="form-control"
                     placeholder="Agregar concepto"
                     wire:model="query_concepto"
                     wire:keydown.escape="resetProduct"
                     wire:keydown.tab="resetProduct"
                     wire:keydown.enter="selectConcepto"
                 />
                 
           <label for="">Monto</label>

           <input style="text-align: center; font-weight: bold; width:100%;" wire:keydown.enter="Agregar" min="1" type="number" class="form-control" id="myText" wire:model.lazy="monto_concepto" >
           <br><br><br>
              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUI()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
                <button class="btn btn-submit" wire:click="Agregar"  title="Agregar al carrito">
                   AGREGAR
              	</button>
              </div>
          </div>
      </div>
  </div>
