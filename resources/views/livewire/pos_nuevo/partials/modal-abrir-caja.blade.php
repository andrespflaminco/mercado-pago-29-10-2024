<div class="row">
    <div class="col-sm-12 col-md-2">

    </div>
    <div class="col-sm-12 col-md-8">
        <div class="form-group">
            <h5>Abrir Caja</h5>
            <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                <div class="input-group-prepend">
                    <span class="input-group-text input-gp">
                        Monto inicial: $
                    </span>
                </div>
                <input type="text" wire:model.lazy="monto_inicial" required class="form-control" placeholder="Ej: 10" >
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-2">
    </div>

    <div class="col-sm-12 col-md-2">
    </div>

    <div class="col-sm-12 col-md-8">
        <button style="float:right;" type="button"  wire:loading.attr="disabled" id="caja-abrir" wire:click.prevent="AbrirCaja()" class="btn btn-dark close-modal" >GUARDAR</button>
    </div>

    <div class="col-sm-12 col-md-2">
    </div>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</div>
