<div class="modal fade" id="ModalVenta" tabindex="-1" role="dialog" aria-labelledby="notesMailModalTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
						<div class="modal-body">
								<svg wire:click="resetUI" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="modal"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
								<div class="notes-box">
										<div class="notes-content">
												<form action="javascript:void(0);" id="notesMailModalTitle">
														<div class="row">
														    
														    
                                                        <div class="col-12">

                                                    
                                                          <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                                                              <div class="widget-content widget-content-area br-6">
                                                                  <div class="table-responsive mb-4 mt-4">
                                                                      <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
                                                        <thead>
                                                          <tr>
                                                            <th>Producto</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Total</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          @foreach($detalle_venta as $r)
                                                          <tr>
                                                            <td>{{$r->product_name}}</td>
                                                            <td>{{$r->quantity}}</td>
                                                            <td> $ {{$r->price }}</td>
                                                            <td> $ {{$r->price*$r->quantity }}</td>
                                                          </tr>
                                                    
                                                          @endforeach
                                                    
                                                        </tbody>
                                                      </table>
                                                      
                                                      <br>
                                                      
                                                      @if($venta != null)
                                                      <div style="float: left;">
                                                        Metodo de pago: {{$venta->metodo_pago}}
                                                        </div>
                                                      <div style="float: right;">
                                                          
                                                          <table>
                                                              <tr>
                                                                  <td>Subotal:</td>
                                                                  <td>$ {{$venta->subtotal}}</td>
                                                              </tr>
                                                               <tr>
                                                                  <td>- Desc:</td>
                                                                  <td>$ {{$venta->descuento}}</td>
                                                              </tr>
                                                               <tr>
                                                                  <td>+ Rec:</td>
                                                                  <td>$ {{$venta->recargo}}</td>
                                                              </tr>
                                                               <tr style="border-top: solid 1px #888EA8;">
                                                                  <td>Total:</td>
                                                                  <td>$ {{$venta->total}}</td>
                                                              </tr>
                                                          </table>
                                                      <p> </p>
                                                    
                                                      </div>
                                                      
                                                      
                                                      @endif
                                                    
                                                     
                                                    </div>
                                                    
                                                    </div>
                                                    </div>
                                                    
                                                    </div>

														</div>

												</form>
										</div>
								</div>
						</div>
				</div>
		</div>
</div>
