<div class="table-responsive mb-3" style="width: 80%;">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Lista de precios</th> 
                                                <th  @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif  class="text-center">% margen sobre Costo</th>
                                                <th>Precio</th>
                                                <th>Al modificar costos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <tr {{ ($lista_costo_defecto == 0 || $lista_costo_defecto == 1) ? '' : 'hidden' }} @if(!auth()->user()->can('ver costo defecto')) hidden @endif >
                                                <td>
                                                    <label>Costo</label>
                                                </td>
                                                <td></td>
                                                <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} id="cost_{{ $variacion }}" type="number" step="0.01" class="form-control text-center" onchange="calcularCostoDescuento('{{ $variacion }}'); calcularTodosLosPrecios('{{ $variacion }}');" />
                                                </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                
                                            <tr {{($lista_costo_defecto == 0 || $lista_costo_defecto == 1) ? '' : 'hidden' }} @if(!auth()->user()->can('ver costo defecto')) hidden @endif >
                                                <td>
                                                    <label>Descuento costo</label>
                                                </td>
                                                <td></td>
                                                <td>
                                                <div class="input-group">
                                                    <span class="input-group-text" style="border: none !important; "> </span>
                                                    <input style="border: none !important; padding: 0.375rem .75rem .375rem 1.25rem !important;" readonly id="descuento_costo_{{ $variacion }}" type="number" step="0.01" class="form-control  text-center" onchange="calcularCostoDescuento('{{ $variacion }}'); calcularTodosLosPrecios('{{ $variacion }}');" />
                                                </div>
                                                </td>
                                                <td>
                                                    <button  {{ $es_sucursal == 1 ? 'hidden' : '' }}  class="btn btn-light"  wire:click="DescuentosModal({{$selected_id ?? 0}},'{{ $variacion }}')" tittle="Configurar descuentos para los costos">%</button>
                                                </td>
                                            </tr>
                                
                                            <tr {{($lista_costo_defecto == 0 || $lista_costo_defecto == 1) ? '' : 'hidden' }} @if(!auth()->user()->can('ver costo defecto')) hidden @endif >
                                                <td>
                                                    <label>Costo después de descuentos</label>
                                                </td>
                                                <td></td>
                                                <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input id="costo_despues_descuento_{{ $variacion }}" type="number" step="0.01" class="form-control  text-center" readonly />
                                                </div>
                                                
                                                </td>
                                                <td></td>
                                            </tr>
                                            
                                            @if($lista_costo_defecto != 0 && $lista_costo_defecto != 1)
                                            <tr @if(!auth()->user()->can('ver costo defecto')) hidden @endif >
                                                <td>
                                                    <label>Costo</label>
                                                </td>
                                                <td></td>
                                                <td>
                                                  @foreach($lista_precios as $key => $lp)
                                                    @if($lista_costo_defecto == $lp->id)  
                                                         <input hidden id="porcentaje_regla_precio_{{ $lp->id }}_{{ $variacion }}" type="number" step="0.01" class="form-control  text-center" onchange="calcularPrecioEspecifico({{ $lp->id }},'{{ $variacion }}');"/>
                                                         <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} id="precio_lista_{{ $lp->id }}_{{ $variacion }}"  class="form-control  text-center" />
                                                         <input hidden id="nombre_regla_{{ $lp->id }}_{{ $variacion }}">
                                          
                                                    @endif
                                                    @endforeach
                                                </td>
                                                <td></td>
                                            </tr>
                                            @endif
                                            
                                        
                                            <tr {{ isset($mapeoListaMuestra[0]) && $mapeoListaMuestra[0] == 0 ? 'hidden' : '' }} id="lista_precio_1_{{ $variacion }}">
                                              
                                                <td>
                                                    <label>Precio de venta a sucursales</label>
                                                </td>
                                                <td>
                                                <div class="input-group">
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif  id="porcentaje_regla_precio_1_{{ $variacion }}" type="number" step="0.01" class="form-control  text-center" onchange="calcularPrecioEspecifico(1,'{{ $variacion }}');"/>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                 </td>
                                                <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} id="precio_lista_1_{{ $variacion }}"  onchange="calcularPrecioFijo(1,'{{ $variacion }}');" type="number" step="0.01" class="form-control  text-center" />
                                                </div>
                                                </td>
                                                <td>
                                                    <span hidden id="tipo_regla_precio_1_{{ $variacion }}">Regla: % Utilidad sobre el costo</span>
                                                    <input readonly style="border:none; background: white;" id="nombre_regla_1_{{ $variacion }}">
                                                </td>
                                            </tr>
                                            
                                            
                                            <tr id="lista_precio_0_{{ $variacion }}">
                                              
                                                <td>
                                                    <label>Lista de precios base </label>
                                                </td>
                                                <td>
                                                <div class="input-group">
                                                    <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }}  @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif  id="porcentaje_regla_precio_0_{{ $variacion }}" type="number" step="0.01" class="form-control  text-center" onchange="calcularPrecioEspecifico(0,'{{ $variacion }}');"/>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                </td>
                                                <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} id="precio_lista_0_{{ $variacion }}"  onchange="calcularPrecioFijo(0,'{{ $variacion }}');" type="number" step="0.01" class="form-control  text-center" />
                                                </div>
                                                </td>
                                                <td>
                                                    <span hidden id="tipo_regla_precio_0_{{ $variacion }}">Regla: % Utilidad sobre el costo</span>
                                                    <input readonly style="border:none; background: white;" id="nombre_regla_0_{{ $variacion }}">
                                                     
                                                </td>
                                            </tr>
                                            
                                            
                                            <!-- Listas de precios generadas dinámicamente -->
                                            @foreach($lista_precios as $key => $lp)
                                            
                                            @if($lista_costo_defecto != $lp->id)  
                                            <tr {{ isset($mapeoListaMuestra[$lp->id]) && $mapeoListaMuestra[$lp->id] == 0 ? 'hidden' : '' }} id="lista_precio_{{ $lp->id }}_{{ $variacion }}">
                                              
                                                <td>
                                                    <label>Lista de precios {{$lp->nombre}} </label>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif  id="porcentaje_regla_precio_{{ $lp->id }}_{{ $variacion }}" type="number" step="0.01" class="form-control  text-center" onchange="calcularPrecioEspecifico({{ $lp->id }},'{{ $variacion }}');"/>
                                                    
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                <input {{ $es_sucursal == 1 ? 'readonly' : '' }} {{ $forma_edit == 1 ? 'readonly' : '' }} id="precio_lista_{{ $lp->id }}_{{ $variacion }}"  onchange="calcularPrecioFijo({{ $lp->id }},'{{ $variacion }}');" type="number" step="0.01" class="form-control  text-center" />
                                                </div>
                                                </td>
                                                <td>
                                                    <span hidden id="tipo_regla_precio_{{ $lp->id }}_{{ $variacion }}">Regla: % Utilidad sobre el costo</span>
                                                    <input readonly style="border:none; background: white;" id="nombre_regla_{{ $lp->id }}_{{ $variacion }}">
                                                    
                                                </td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>