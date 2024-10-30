<style media="screen">
.table-hover:not(.table-dark) tbody tr:hover {
    background-color: transparent !important;
}
	.boton-etiqueta:hover {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta:focus {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border: none;
		text-align: center;
	}
</style>
<div wire:ignore.self class="modal fade" id="tabsModal" tabindex="-1" role="dialog" aria-labelledby="tabsModalLabel" aria-hidden="true">
  <div style="max-width:1000px;" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tabsModalLabel">Caja {{$caja_id}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="padding: 2.5rem;" class="modal-body">

                <div class="row">


                <div class="col-sm-12 col-md-6">


                <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">


                    @if($count_efectivo > 0)
                    <table class="multi-table table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-left">Efectivo</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>

                          @foreach($details_efectivo as $ce)

                          <tr>
                              <td class="text-left">
                                <h6>Monto inicial</h6>
                                </td>
                                <td class="text-center">
                                  <h6> $ {{number_format($ce->monto_inicial,2)}} </h6>
                                </td>

                          </tr>
                          <tr>
                              <td class="text-left">
                                <h6> +  Ventas</h6>
                              </td>
                              <td class="text-center">
                                <h6> $
                                @if($ce->total == null)
                                0
                                @else
                                {{number_format($ce->total,2)}}
                                @endif </h6>

                              </td>

                          </tr>

                          <tr>
                              <td class="text-left">
                                <h6> - Faltante de caja</h6>
                              </td>
                              <td class="text-center">
                                <h6>
                                  $
                                @if($ce->estado == 1)
                                {{number_format(($ce->monto_inicial+$ce->total-$ce->monto_final),2)}}
                                @else
                                -
                                @endif
                                </h6>
                              </td>

                          </tr>




                          </tbody>
                              <tfoot style="border-top: solid 1.5px #dcd9d9;">
                                <td class="text-left">  <h6><b>Monto final</b></h6>  </td>
                                <td class="text-center"> <h6> <b> $
                                  @if($ce->estado == 1)

                                  {{number_format($ce->monto_final,2)}}

                                  @else
                                  {{number_format(($ce->monto_inicial+$ce->total-$ce->monto_final),2)}}
                                  @endif

                                </b>
                                </h6></td>
                                <td class="text-center"></td>
                          </tfoot>
                            @endforeach

                          </table>
                          </div>

                          @else

                          @endif

                          </div>






                          <div class="col-sm-12 col-md-6">




                          @if($count_bancos > 0)

                          <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">

                              <table class="multi-table table table-hover" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th class="text-left">Bancos</th>
                                          <th class="text-center">Total</th>
                                      </tr>
                                  </thead>
                                  <tbody>

                            @foreach($details_bancos as $cj)
                            @if(count($details_bancos) > 0)

                            <tr>
                                <td class="text-left">
                                  <h6>{{$cj->metodo_pago}}</h6>


                                  </td>
                                <td class="text-center">
                                  <h6> $ {{number_format($cj->total,2)}}</h6>

                                </td>

                            </tr>
                            @else

                            @endif
                            @endforeach


                        </tbody>
                        <tfoot style="border-top: solid 1.5px #dcd9d9;">
                          <td class="text-left">  <h6><b>Total</b></h6>  </td>

                          <td class="text-center"> <h6> <b>$ {{number_format($total_bancos,2)}}</b>
                          </h6></td>
                          <td class="text-center"></td>
                        </tfoot>

                    </table>
                </div>

                @else

                @endif


              </div>






              <div class="col-sm-12 col-md-6">


                @if($count_plataformas > 0)

                <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">

                    <table class="multi-table table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-left">Plataformas</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>

                  @foreach($details_plataformas as $cp)

                  <tr>
                      <td class="text-left">
                        <h6>{{$cp->metodo_pago}}</h6>


                        </td>
                      <td class="text-center">
                        <h6> $ {{number_format($cp->total,2)}}</h6>

                      </td>

                  </tr>

                  @endforeach


              </tbody>
              <tfoot style="border-top: solid 1.5px #dcd9d9;">
                <td class="text-left">  <h6><b>Total</b></h6>  </td>

                <td class="text-center"> <h6> <b>$ {{number_format($total_plataformas,2)}}</b>
                </h6></td>
                <td class="text-center"></td>
              </tfoot>

          </table>
      </div>

      @else

      @endif

    </div>






    <div class="col-sm-12 col-md-6">

      @if($count_a_cobrar > 0)

      <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">

          <table class="multi-table table table-hover" style="width:100%">
              <thead>
                  <tr>
                      <th class="text-left">A cobrar</th>
                      <th class="text-center">Total</th>
                  </tr>
              </thead>
              <tbody>

        @foreach($details_a_cobrar as $ca)

        <tr>
            <td class="text-left">
              <h6>{{$ca->metodo_pago}}</h6>


              </td>
            <td class="text-center">
              <h6> $ {{number_format($ca->total,2)}}</h6>

            </td>

        </tr>
        @endforeach


    </tbody>
    <tfoot style="border-top: solid 1.5px #dcd9d9;">
      <td class="text-left">  <h6><b>Total</b></h6>  </td>

      <td class="text-center"> <h6> <b>$ {{number_format($total_a_cobrar,2)}}</b>
      </h6></td>
      <td class="text-center"></td>
    </tfoot>

</table>
</div>

@else

@endif


</div>
</div>


<br>

@foreach($details_efectivo as $ce)
@if($ce->estado == 1)
<h5>
  
<b>TOTAL: $ {{number_format(($total_a_cobrar+$total_plataformas+$total_bancos+$ce->monto_final),2)}} </b>
</h5>
@else
<h5>

<b>TOTAL: $ {{number_format(($total_a_cobrar+$total_plataformas+$total_bancos+$ce->monto_inicial+$ce->total-$ce->total_gasto-$ce->monto_final),2)}} </b>
</h5>
@endif
@endforeach

        </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> CERRAR</button>
      </div>
    </div>
  </div>
</div>
