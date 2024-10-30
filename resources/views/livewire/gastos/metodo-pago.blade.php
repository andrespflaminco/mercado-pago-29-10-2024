<div class="col-sm-12 col-md-9 mb-3">
                                             <label> Pagos </label>
                                            
                                             <div class="col-sm-12 col-md-12">
                                               <div class="form-group">
                                                 <div class="table-responsive mb-0 mt-1">
                                                     <table class="multi-table table table-hover" style="width:100%">
                                                         <thead>
                                                             <tr>
                                                                 <th class="text-center">Monto Pago</th>
                                                                 <th class="text-center">Metodo de pago</th>
                                                                 <th></th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                             
                                                        @foreach($metodos_pago_dividido as $index => $metodo_pago)
                                                        @if($metodo_pago['eliminado'] == 0)
                                                        
                                                        <tr>
                                                        <td>
                                                          <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                                                                <div class="input-group-prepend">
                                                                  <span style="height:100%;" class="input-group-text input-gp">
                                                                    $
                                                                  </span>
                                                                </div>
                                                                <input autocomplete="off" type="text" min="0" type="number" 
                                                                    wire:change="CambiarMontoPago({{ $index }}, $event.target.value)"
                                                                    wire:keyup.enter="CambiarMontoPago({{ $index }}, $event.target.value)"
                                                                    class="form-control text-center"
                                                                    value="{{ number_format($metodo_pago['efectivo'], 2) }}"
                                                                    wire:model.lazy="metodos_pago_dividido.{{ $index }}.efectivo"
                                                                    required="">
                                                          </div>
                                                          @error('monto_ap_div.{{$index}}') <span class="text-danger err">{{ $message }}</span> @enderror
                        								  
                                                        </td>
                                                        <td>
                                                        <div style="margin-bottom: 0 !important;" class="input-group mb-4">
                                                           
                                                        <select class="form-control text-center" wire:model.lazy="metodos_pago_dividido.{{ $index }}.metodo_pago_ap_div">
                                                           <option value="Elegir" disabled >Elegir</option>
                                                           <option value="1" >Efectivo</option>
                                                           @foreach($this->metodo_pago as $mp)
                                                               <option value="{{ $mp['id'] }}">{{ $mp['nombre'] }}</option>
                                                           @endforeach
                                                        </select>

                                                           
                                                        </div>
                                                        </td>
                                                        <td>
                                                        <span style="margin-left: 12px !important;  background-color: white !important; border: none; padding: 0.375rem 0px !important; " class="input-group-text input-gp">
                                                             <a style="color: #212529 !important; " href="javascript:void(0)" wire:click="quitarMetodoPago({{$index}})"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> </a>
                                                         </span>
                                                        </td>
                                                        </tr>
                                                        
                                                        @endif
                                                        @endforeach
                                                        
                                                        @if(count($metodos_pago_dividido) < 1)
                                                        <tr>
                                                        <td colspan="3">No hay pagos agregados</td>    
                                                        </tr>
                                                        @endif
                                                         </tbody>
                                                         <tfoot></tfoot>
                                                     </table>
                                                 </div>



                                              </div>
                                            <p>@if($deuda) Deuda: $ {{$deuda}} @endif</p>    
                                             </div>
                                                <a class="mt-2" href="javascript:void(0)" wire:click="agregarMetodoPago"> + Agregar Método de Pago</a>
                                              </div>
