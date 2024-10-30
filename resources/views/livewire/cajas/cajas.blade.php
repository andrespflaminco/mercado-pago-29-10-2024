
<div wire:ignore.self class="modal fade" id="tabsModal" tabindex="-1" role="dialog" aria-labelledby="tabsModalLabel" aria-hidden="true">
  <div style="max-width:1000px;" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tabsModalLabel">Caja #
            @foreach($detalle_nro_caja as $dt) {{$dt->nro_caja}} @endforeach</h5>
        <button type="button" class="close" wire:click="CerrarModalResumen" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="padding: 2.5rem;" class="modal-body">
            @foreach($detalle_nro_caja as $dt)
            <div class="row">
            <div class="col" style="vertical-align: bottom;"> Desde: {{\Carbon\Carbon::parse($dt->fecha_inicio)->format('d-m-Y H:i')}} hs. - Hasta: {{\Carbon\Carbon::parse($dt->fecha_cierre)->format('d-m-Y H:i')}} hs. @endforeach</div>
            <div class="col" style="text-align:right !important;">
            @foreach($detalle_nro_caja as $dt) 
            <text class="text-dark" style="border: solid 1px; padding: 5px 10px;">{{$dt->nombre_usuario}}</text> 
            @if($dt->estado == 1)
            <text class="text-danger" style="border: solid 1px; padding: 5px 10px;">CAJA CERRADA</text>                
            @else
            <text class="text-success" style="border: solid 1px; padding: 5px 10px;">CAJA ABIERTA</text>       
            @endif
            <text class="text-dark" style="border: solid 1px; padding: 5px 10px; color: #333;">
            <a style="color: #333;" href="{{ url('pdf/caja/' . $dt->id . '/' . '1') }}"  target="_blank"  data-bs-toggle="tooltip" data-bs-placement="top" title="print">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>  
            </a>    
            </text> 
        	
            @endforeach   
									
            </div>     
            </div>
           
                <div class="row">

            <!------------------- EFECTIVO --------------------->
            
                <div class="col-sm-12 col-md-6">

                 <h5>Efectivo</h5>
                <div style="margin-bottom: 0 !important;" class="table-responsive mb-2 mt-2">


                    <table class="multi-table table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-left">Detalle</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>


                          <tr>
                              <td class="text-left">
                                <h6>Monto inicial</h6>
                                </td>
                                <td class="text-center">
                                  <h6> $

                                     {{number_format($total_efectivo_inicial,2)}}

                                  </h6>
                                </td>

                          </tr>
                          
                          <tr>
                              <td class="text-left">
                                <h6> +  Ingresos</h6>
                              </td>
                              <td class="text-center">
                               <h6> $ {{number_format( ($total_ingresos_efectivo) ,2) }} </h6>
                              </td>

                          </tr>
                            
                            <tr>
                              <td class="text-left">
                                <h6> -  Retiros</h6>
                              </td>
                              <td class="text-center">
                                <h6> $ {{number_format( ($total_retiros_efectivo) ,2) }} </h6>
                              </td>

                          </tr>
                          
                          <tr>
                              <td class="text-left">
                                <h6> +  Ventas</h6>
                              </td>
                              <td class="text-center">
                                <h6> $
                                {{number_format( ($total_ventas_efectivo) ,2) }}
                              </h6>

                              </td>

                          </tr>
                          <tr>
                              <td class="text-left">
                                <h6> - Compras</h6>
                              </td>
                              <td class="text-center">
                                <h6> $ {{number_format($total_compras_efectivo,2)}} </h6>

                              </td>

                          </tr>
                          <tr>
                              <td class="text-left">
                                <h6> - Gastos</h6>
                              </td>
                              <td class="text-center">
                                <h6> $ {{number_format($total_gastos_efectivo,2)}} </h6>
                              </td>

                          </tr>
                        <!------- SI LA CAJA ESTA CERRADA ----->
                        @foreach($detalle_nro_caja as $dt) 
                        
                        @if($dt->estado == 1)
                          <tr>

                            <td class="text-left">  <h6>Dinero en caja</h6>  </td>
                            <td class="text-center"> <h6> $

                              {{number_format($total_efectivo_final,2)}}


                            </h6></td>
                            <td class="text-center"></td>

                          </tr>
                          @endif
                          
                          @endforeach
                          
                          
                        <!--------------------------------------->




                          </tbody>
                          <!------- SI LA CAJA ESTA CERRADA ----->
                            @foreach($detalle_nro_caja as $dt) 
                        
                            @if($dt->estado == 1)
                              <tfoot style="border-top: solid 1.5px #dcd9d9;">
                                <td class="text-left">
                                  <h6><b>  Diferencia de caja </b></h6>
                                </td>
                                <td class="text-center">
                                  <h6>
                                    <b>$
                                  {{number_format(($total_efectivo*-1),2)}}
                                  </b>
                                  </h6>
                                </td>
                                </tfoot>
                            @else
                              <tfoot style="border-top: solid 1.5px #dcd9d9;">
                                <td class="text-left">
                                  <h6><b>  Dinero que deberia haber en efectivo </b></h6>
                                </td>
                                <td class="text-center">
                                  <h6>
                                    <b>$
                                  {{number_format($total_efectivo,2)}}
                                  </b>
                                    </h6>
                                </td>
                                </tfoot>
                            @endif
                          
                            @endforeach
                          </table>
                          </div>


                          </div>

            <!------------------- BANCOS --------------------->
            
              @if($total_bancos != 0)
              <div class="col-sm-12 col-md-6">

                        <h5>Bancos</h5>
                        
                          <div style="margin-bottom: 0 !important;" class="table-responsive mb-2 mt-2">
                            @foreach($listado_bancos as $lb)
                              <table class="multi-table table table-hover mb-3" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th class="text-left">{{$lb->nombre}} </th>
                                          <th class="text-center"></th>
                                      </tr>
                                  </thead>
                                  <tbody>
                            
                            <!----------- VENTAS ---------------->
                            @if(0 < $details_bancos->count() )
                            <tr >
                                <td class="text-left"><h6 style="font-weight: 600 !important; color: #333 !important;"><b>Ventas</b></h6></td>
                                <td class="text-center"> </td>
                            </tr>
                            @endif
                            
                            @foreach($details_bancos as $cj)

                            @if($lb->id == $cj->banco_id)

                            
                            <tr>
                                <td class="text-left"><h6 style="padding-left:30px !important;"> {{$cj->metodo_pago}}</h6></td>
                                <td class="text-center"> <h6 style="padding-left:30px !important;"> $ {{number_format($cj->total_banco,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endforeach
                            <!----------- / VENTAS ---------------->
                            
                            <!----------- INGRESOS ---------------->
                            @foreach($ingresos_bancos as $ib)
                            
                            @if($lb->id == $ib->banco_id)
                            @if(count($ingresos_bancos) > 0)
                            
                            <tr>
                                <td class="text-left"><h6>+ Ingreso de capital</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($ib->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / INGRESOS ---------------->
                            
                                                        
                            <!----------- RETIROS ---------------->
                            @foreach($retiros_bancos as $rb)
                            
                            @if($lb->id == $rb->banco_id)
                            @if(count($retiros_bancos) > 0)
                            
                            <tr>
                                <td class="text-left"><h6>- Retiros de capital</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($rb->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / RETIROS ---------------->
                            
                            
                            
                            <!----------- COMPRAS ---------------->
                            
                            @foreach($compras_bancos as $cb)
                            
                            @if($lb->id == $cb->banco_id)
                            @if(0 < $compras_bancos->count() )
                            
                            <tr>
                                <td class="text-left"><h6  style="font-weight: 600 !important; color: #333 !important;">Compras</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($cb->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / COMPRAS ---------------->
                            
                            
                            <!----------- GASTOS ---------------->
                            
                            
                            @foreach($gastos_bancos as $gb)
                           
                            @if($lb->id == $gb->banco_id)
                            @if(0 < $gastos_bancos->count() )
                            
                            <tr>
                                <td class="text-left"><h6  style="font-weight: 600 !important; color: #333 !important;">Gastos</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($gb->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / GASTOS ---------------->
                            
                        </tbody>
                        <tfoot style="border-top: solid 1.5px #dcd9d9;">
                          <td class="text-left">  <h6><b>Total</b></h6>  </td>
                          <td class="text-center">
                            <h6>  
                            @foreach($totales_bancos as $tb)
                            @if($lb->id == $tb->banco_id)
                            $ {{number_format($tb->total,2)}}
                            @endif
                            @endforeach
                            </h6>
                          </td>
                        </tfoot>

                    </table>
                    @endforeach
                <h6 class="ml-1">Total todos los bancos: $ {{number_format($total_bancos,2)}}</h6>
                </div>
                        
                
                
              </div>
              @endif

            <!------------------- PLATAFORMAS --------------------->
              @if($total_plataformas != 0)
              <div class="col-sm-12 col-md-6 mt-6" style="margin-top: 50px;">

                        <h5>Plataformas de pago</h5>
                        
                          <div style="margin-bottom: 0 !important;" class="table-responsive mb-2 mt-2">
                            @foreach($listado_plataformas as $lp)
                              <table class="multi-table table table-hover mb-3" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th class="text-left">{{$lp->nombre}}</th>
                                          <th class="text-center"></th>
                                      </tr>
                                  </thead>
                                  <tbody>
                            
                            <!----------- VENTAS ---------------->
                                                        
                            @if(0 < $details_plataformas->count() )
                            <tr >
                                <td class="text-left"><h6 style="font-weight: 600 !important; color: #333 !important;"><b>Ventas</b></h6></td>
                                <td class="text-center"> </td>
                            </tr>
                            @endif
                            
                            @foreach($details_plataformas as $dp)

                            @if($lp->id == $dp->banco_id)
                            @if(0 < $details_plataformas->count() )
                            <tr>
                                <td class="text-left"><h6 style="padding-left: 30px !important;">{{$dp->metodo_pago}}</h6></td>
                                <td class="text-center"> <h6 style="padding-left: 30px !important;"> $ {{number_format($dp->total_plataforma,2)}}</h6> </td>
                            </tr>
                            
                            @endif
                                                        
                            @endif
                            @endforeach
                            <!----------- / VENTAS ---------------->
                            
                            <!----------- INGRESOS ---------------->
                            @foreach($ingresos_plataformas as $ip)
                            
                            @if($lp->id == $ip->banco_id)
                            @if(count($ingresos_plataformas) > 0)
                            
                            <tr>
                                <td class="text-left"><h6>+ Ingreso de capital</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($ip->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / INGRESOS ---------------->
                            
                                                        
                            <!----------- RETIROS ---------------->
                            @foreach($retiros_plataformas as $rp)
                            
                            @if($lp->id == $rp->banco_id)
                            @if(count($retiros_plataformas) > 0)
                            
                            <tr>
                                <td class="text-left"><h6>- Retiros de capital</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($rp->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / RETIROS ---------------->
                            
                            
                            
                            <!----------- COMPRAS ---------------->
                            
                            @foreach($compras_plataformas as $cp)
                            
                            @if($lp->id == $cp->banco_id)
                             @if(0 < $compras_plataformas->count() )
                            
                            <tr>
                                <td class="text-left"><h6  style="font-weight: 600 !important; color: #333 !important;">Compras</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($cp->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / COMPRAS ---------------->
                            
                            
                            <!----------- GASTOS ---------------->
                            
                            @foreach($gastos_plataformas as $gp)
                            
                            @if($lp->id == $gp->banco_id)
                           @if(0 < $gastos_plataformas->count() )
                            
                            <tr>
                                <td class="text-left"><h6  style="font-weight: 600 !important; color: #333 !important;">Gastos</h6></td>
                                <td class="text-center"> <h6> $ {{number_format($gp->total,2)}}</h6> </td>
                            </tr>
              
                            @endif
                            @endif
                            @endforeach
                            <!----------- / GASTOS ---------------->
                            
                        </tbody>
                        <tfoot style="border-top: solid 1.5px #dcd9d9;">
                          <td class="text-left">  <h6><b>Total</b></h6>  </td>
                          <td class="text-center">
                            <h6>  
                            @foreach($totales_plataformas as $tp)
                            
                            @if($lp->id == $tp->banco_id)
                            $ {{number_format($tp->total,2)}}
                            @endif
                            @endforeach
                            </h6>
                          </td>
                        </tfoot>

                    </table>
                    @endforeach
                <h6 class="ml-1">Total todas las plataformas: $ {{number_format($total_plataformas,2)}}</h6>
                </div>
                        
                
                
              </div>
              @endif






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
              <h6> $ {{number_format($ca->total+$ca->recargo,2)}}</h6>

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

<h5 style="text-align: center;
    border: 1px solid #c8c8c8;
    padding: 8px;
    margin-top: 15px;">
<b>TOTAL VENTAS: ${{number_format(($total_ventas_totales),2)}}</b>
</h5>



        </div>
      <div class="modal-footer">
          <a href="javascript:void(0);" wire:click="CerrarModalResumen" class="btn btn-cancel" data-dismiss="modal">Cerrar</a>
      </div>
    </div>
  </div>
</div>
