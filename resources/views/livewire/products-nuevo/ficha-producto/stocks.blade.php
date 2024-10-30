                    <div class="row">
								<div class="col-lg-12 col-sm-12 col-12">
								<div class="form-group">
                                <h6 class="mt-2 mb-2">Stocks</h6>								
                                <div class="table-responsive mt-3">
                                    <label></label>
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Sucursal</th>
                                                <th>Almacen</th>
                                                <th>Stock real <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Stock real a encontrar en un recuento fisico"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div></th>
                                                <th>Stock disponible  <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Cantidad de unidades disponibles para vender. Es el stock real, menos las unidades ya vendidas pero aun no entregadas"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div> </th>
                                                <th>Stock comprometido <div style="margin-left:3px; cursor:pointer;" class="example-popover" data-container="body" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Es el stock aun en nuestro poder, pero que ya esta vendido, esperando por ser entregado."><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>  </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr {{ auth()->user()->sucursal != 1 ? '' : 'hidden' }}>
                                                <td>{{auth()->user()->name}}</td>
                                                <td>
                                                    <select id="almacen_id_0_{{ $variacion }}" {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
                                                        <option value="1" >Sin almacen</option>
                                                        @foreach($almacenes as $a)
                                                            <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" id="stock_real_0_{{ $variacion }}" {{ $forma_edit == 1 ? 'readonly' : '' }} required onchange="CambiarStockDisponible(0,'{{ $variacion }}')" class="form-control">
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg </span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif

                                                    </div>
                                                    @error('real_stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" readonly class="form-control" id="stock_disponible_0_{{ $variacion }}">
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" id="stock_comprometido_0_{{ $variacion }}">
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            
                                            @foreach ($sucursales as $llave => $sucu)
                                            <tr {{ ($muestra_stock_otras_sucursales == 0) && ($sucu->sucursal_id != $comercio_id) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>
                                                <td>{{$sucu->name}}</td>
                                                <td>
                                                    <select id='almacen_id_{{ $sucu->sucursal_id }}_{{ $variacion }}' {{ $forma_edit == 1? 'disabled' : '' }} class="form-control">
                                                        <option value="1" >Sin almacen</option>
                                                        @foreach($almacenes as $a)
                                                            <option value="{{$a->id}}">{{$a->nombre}}</option>
                                                        @endforeach
                                                    </select>    
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" id='stock_real_{{ $sucu->sucursal_id }}_{{ $variacion }}' onchange="CambiarStockDisponible({{ $sucu->sucursal_id }},'{{ $variacion }}')"  {{ ($es_sucursal == 1) && (auth()->user()->id != $sucu->sucursal_id ) ? 'readonly' : '' }}  {{ $forma_edit == 1 ? 'readonly' : '' }} required class="form-control" />
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @error('real_stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" id='stock_disponible_{{ $sucu->sucursal_id }}_{{ $variacion }}' readonly class="form-control" >
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td> 
                                                    <div class="input-group">
                                                        <input id='stock_comprometido_{{ $sucu->sucursal_id }}_{{ $variacion }}' onchange="CambiarStockDisponible({{ $sucu->sucursal_id }},'{{ $variacion }}')"  type="number" step="{{ $tipo_unidad_medida == 1 ? '0.001' : '1' }}" readonly class="form-control" >
                                                        @if($tipo_unidad_medida == 9)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Unid</span>
                                                            </div>
                                                        @endif
                                                        @if($tipo_unidad_medida == 1)
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Kg</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

    
								</div>
                                </div>
                                </div>