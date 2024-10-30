
<div wire:ignore.self class="modal fade" id="VerPagos" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 800px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>VER PAGOS</b>

              </div>
              <div style="margin: 0 auto !important; width:90% !important;" class="modal-body">


                
              <br>

            	<div class="row">
								<div class="col-lg-12 col-sm-12 col-12">
								<br>    
								<label><strong>Pagos</strong></label>
								<br>
								<div class="table-responsive">
										<table class="table mb-0">
											<thead>
												<tr>
													<th>Caja</th>
                                                    <th>Fecha</th>
                                                    <th>Metodo de pago</th>
                                                    <th>Pago</th>
                                                    <th>Total</th>
                                                    <th></th>
												</tr>
											</thead>
											<tbody>
											  @php
                                                $suma_monto = 0;
                                              @endphp
                                                
											  @foreach($pagos2 as $p2)
                                                
                                              @if ($p2->count() > 0)
                                                @php
                                                    $pago_total = $p2->monto + $p2->recargo + $p2->iva_recargo + $p2->iva_pago;
                                                    $total_con_actualizacion = $pago_total * (1 + $p2->actualizacion);
                                                    $suma_monto += $total_con_actualizacion;
                                                @endphp
												<tr>
													<td>
													
													@if($p2->nro_caja != null)
                                                    Caja # {{$p2->nro_caja}}
                                                    @else
                                                    No asociado a caja
                                                    @endif
                                                    
                                                    </td>
													<td>{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
													<td>{{$p2->metodo_pago}}</td>
													<td>$ {{number_format($p2->monto+$p2->recargo+$p2->iva_recargo+$p2->iva_pago,2) }}</td>
													<td>$ {{number_format( ($p2->monto+$p2->recargo+$p2->iva_recargo+$p2->iva_pago) *(1+$p2->actualizacion),2) }}</td>
													<td>
													@if($sucursales_agregan_pago_form == 1)
													<a href="javascript:void(0)" wire:click="EditPago({{$p2->id}},2)" >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    </a>
                                                     <a href="javascript:void(0)" onclick="ConfirmPago({{$p2->id}},2)" >
                                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                                    </a>
                                                    @endif
                                                    </td>
												</tr>
											  @else
                                              No hay pagos relacionados con esta compra
                                              
                                              @endif
                                              
                                              @endforeach
											</tbody>
											<tfoot>
                                              <tr>
                                                  <th>Total </th>
                                                  <th> </th>
                                                  <th> </th>
                                                  <th> </th>
                                                  <th>$ {{number_format($suma_monto,2)}}</th>
                                                  <th> </th>
                                              </tr>
                                          </tfoot>
										</table>
									  
									  <div class="form-group">
									  @if($sucursales_agregan_pago_form == 1)
                                      <a href="javascript:void(0);" wire:click.prevent="AgregarPago({{$id_pedido}},2)">Agregar pago </a>
                                      @endif
                                      </div>
								</div>
								
							<br><br>
							<br><br>
							</div>

              </div>
              <div class="modal-footer">
                <br>
                <button type="button" class="btn btn-cancel" wire:click="CerrarVerPagos()" data-dismiss="modal">CERRAR</button>

              </div>
          </div>
      </div>
