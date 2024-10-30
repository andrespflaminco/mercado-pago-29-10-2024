<div style="z-index: 99999 !important" wire:ignore.self class="modal fade" id="AgregarPago" tabindex="-1" role="dialog">
j
    <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <b>{{ $formato_modal > 0 ? 'EDITAR PAGO' : 'NUEVO PAGO' }}</b>

            </div>
            <div style="margin: 0 auto !important;" class="modal-body">


                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div style="margin-bottom: 0 !important;" class="input-group mb-12">

                            <input autocomplete="off" type="date" wire:model.lazy="fecha_ap" class="form-control"
                                   required="" value="{{$fecha_editar}}">


                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                            <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                            </div>
                            <input autocomplete="off" type="number" id="title" wire:model.lazy="monto_ap" min="0"
                                   wire:change='MontoPagoEditarPago($event.target.value)' class="form-control"
                                   required="">


                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group">
                            <label> Tipo de pago</label>
                            <select wire:model='tipo_pago' wire:change='TipoPago($event.target.value)'
                                    class="form-control">
                                <option value="1">Efectivo</option>
                                @foreach($tipos_pago as $tipos)
                                    <option value="{{$tipos->id}}">{{$tipos->nombre}}</option>
                                @endforeach
                            </select>

                        </div>

                        @if($tipo_pago != 1 && $tipo_pago !=2)

                            <div class="form-group">
                                <label> Forma de pago</label>
                                <select wire:model='metodo_pago_agregar_pago'
                                        wire:change='MetodoPagoEditarPago($event.target.value)' class="form-control">
                                    <option value="" disabled>Elegir</option>
                                    <option hidden value="1">Efectivo</option>
                                    @foreach($metodos as $mp)
                                        <option value="{{$mp->id}}">{{$mp->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                        @endif

                    </div>


                </div>
                <br>
                <p class="text-muted">Subtotal: ${{$monto_ap}}</p>
                <br>
                <p class="text-muted">Recargo: ${{number_format($recargo_total,2)}}</p>
                <br>
                <h5 class="text-muted">A cobrar: ${{number_format($total_pago,2)}}</h5>


            </div>
            <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="CerrarAgregarPago({{$ventaId}})"
                        class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR
                </button>
                @if($formato_modal > 0)
                    <button type="button" wire:click.prevent="ActualizarPago({{$id_pago}})"
                            class="btn btn-dark close-modal">GUARDAR
                    </button>
                @else
                    <button type="button" wire:click.prevent="CreatePago2({{$id_pago}})"
                            class="btn btn-dark close-modal">GUARDAR
                    </button>
                @endif


            </div>
        </div>
    </div>
</div>
