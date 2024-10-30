
<div style="z-index: 99999 !important" class="modal fade" id="SaldoInicial" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b> SALDO INICIAL </b>

              </div>
              <div style="margin: 0 auto !important; width: 100%;" class="modal-body">


                <div class="row">
                
                <a href="javascript:void(0)" wire:click="ModalAgregarEditarPago('0',{{$proveedor_id}})">+ Agregar pago</a>
                <div style="width: 100%;" class="mt-3 mb-4 table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th>Monto ($)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(0 < count($saldos_iniciales))
                            @foreach($saldos_iniciales as $si)
                            <tr>
                                <td>{{$si->concepto}}</td>
                                <td>$ {{number_format($si->monto,2,",",".")}}</td>
                                <td><a href="javascript:void(0)" wire:click="ModalAgregarEditarPago({{$si->id}},{{$proveedor_id}})"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg></a></td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                            <th>Saldo inicial</th>
                            <th>$ {{number_format($sum_si,2,",",".") }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>    
                </div>


              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="CerrarSaldoInicial()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
 
              </div>
          </div>
      </div>
  </div>
