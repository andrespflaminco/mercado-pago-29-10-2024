                  
    <!-- Modal -->
<div class="modal fade" id="MailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ingrese un mail</h5>
                <button type="button" class="close" wire:click.prevent="CerrarModalMail()" aria-label="Close">
                  x
                </button>
            </div>
            <div style="width: 100% !important;" class="modal-body">
            <div class="col-12">
            <label>Mail</label>
            <input type="text" wire:model.defer="mail_ingresado" class="form-control" >    
            </div>
             </div>
            <div class="modal-footer">
                 <a href="javascript:void(0);" wire:click.prevent="CerrarModalMail()" class="btn btn-cancel">Cerrar</a>
                 <a wire:click.prevent="EnviarMail()" href="javascript:void(0);" class="btn btn-submit me-2" >Enviar</a>
            </div>
        </div>
    </div>
</div>