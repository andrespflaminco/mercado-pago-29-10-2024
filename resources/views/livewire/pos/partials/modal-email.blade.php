<!-- Modal -->
<div class="modal fade" id="MailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ingrese un mail</h5>
                <button wire:click="CerrarMail()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                  x
                </button>
            </div>
            <div class="modal-body" style="width:100%;">
            <label>Mail</label>
            <input type="text" wire:model.defer="mail_ingresado" class="form-control" >
             </div>
            <div class="modal-footer">
                <button class="btn" wire:click="VolverAImprimir()"><i class="flaticon-cancel-12"></i> Volver </button>
                <button class="btn btn-dark" wire:click="EnviarMail()"> Enviar </button>

            </div>
        </div>
    </div>
</div>