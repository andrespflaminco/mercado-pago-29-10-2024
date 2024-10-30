<div>	                

	                <div class="page-header">
					<div class="page-title">
							<h4>Cuenta corriente Proveedores</h4>
							<h6>Ver listado de deuda con los proveedores</h6>
						</div>
						<div class="page-btn">               											    
                		
						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										<a class="btn btn-filter" >
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
										</a>
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar por nombre.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
									<ul>
									       	<a  hidden style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarReporte()"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
											Exportar </a>
									</ul>
								</div>
							</div>

							<div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="border-bottom: solid 1px #E9ECEF;"></th>
                                            <th class="text-center" style="border-bottom: solid 1px #E9ECEF;">DEUDA</th>
                                            <th style="border-bottom: solid 1px #E9ECEF;"></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th wire:click="OrdenarColumna('id_proveedor')">
                                            Codigo
                                            @if ($columnaOrden == 'id_proveedor')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            </th>
                                            <th wire:click="OrdenarColumna('nombre_proveedor')">
                                            Proveedor
                                            @if ($columnaOrden == 'nombre_proveedor')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            </th>
                                            <th wire:click="OrdenarColumna('saldo_inicial_cuenta_corriente')">
                                            Saldo inicial
                                            @if ($columnaOrden == 'saldo_inicial_cuenta_corriente')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            
                                            </th>
                                            <th wire:click="OrdenarColumna('deuda_30_dias')" class="text-center">
                                            30 dias
                                            @if ($columnaOrden == 'deuda_30_dias')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            </th>
                                            <th wire:click="OrdenarColumna('deuda_60_dias')" class="text-center">
                                            60 dias
                                            @if ($columnaOrden == 'deuda_60_dias')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            </th>
                                            <th wire:click="OrdenarColumna('deuda_90_dias')" class="text-center">
                                            90 dias
                                            @if ($columnaOrden == 'deuda_90_dias')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            </th>
                                            <th wire:click="OrdenarColumna('total')" class="text-center">
                                            Total
                                            @if ($columnaOrden == 'total')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            </th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_saldo_inicial = 0;
                                            $total_deuda_30_dias = 0;
                                            $total_deuda_60_dias = 0;
                                            $total_deuda_90_dias = 0;
                                            $total_general = 0;
                                        @endphp
                                        @foreach($data as $d)
                                            <tr>
                                                <td>{{$d->id_proveedor}}</td>
                                                <td>
                                                
                                                <!---    <a style="color: #FF9F43 !important;" href="{{ url('movimientos-proveedores'. '/' . $d->id   ) }}">{{$d->nombre_proveedor}}</a> -->
                                                <a style="color: #FF9F43 !important;" href="{{ url('movimientos-proveedores'. '/' . $d->id   ) }}">{{$d->nombre_proveedor}}</a>
                                                </td>
                                                <td>
                                                <a style="color:#637381 !important;" href="javascript:void(0)" wire:click="RenderSaldoInicial({{$d->id}})" >$ {{number_format($d->saldo_inicial_cuenta_corriente,2)}}  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg> </a>
                                                </td>
                                                <td class="text-center">$ {{number_format($d->deuda_30_dias,2)}}</td>
                                                <td class="text-center">$ {{number_format($d->deuda_60_dias,2)}}</td>
                                                <td class="text-center">$ {{number_format($d->deuda_90_dias,2)}}</td>
                                                <td class="text-center">$ {{number_format($d->total,2)}}</td>
                                            </tr>
                                            @php
                                                $total_saldo_inicial += $d->saldo_inicial_cuenta_corriente;
                                                $total_deuda_30_dias += $d->deuda_30_dias;
                                                $total_deuda_60_dias += $d->deuda_60_dias;
                                                $total_deuda_90_dias += $d->deuda_90_dias;
                                                $total_general += $d->total;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td><strong>Total</strong></td>
                                            <td>$ {{number_format($total_saldo_inicial,2)}}</td>
                                            <td class="text-center">$ {{number_format($total_deuda_30_dias,2)}}</td>
                                            <td class="text-center">$ {{number_format($total_deuda_60_dias,2)}}</td>
                                            <td class="text-center">$ {{number_format($total_deuda_90_dias,2)}}</td>
                                            <td class="text-center">$ {{number_format($total_general,2)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

						</div>

                    
					@include('common.ctas-ctes.listado-cajas')
					@include('common.ctas-ctes.agregar-editar-saldo-inicial')
					@include('common.ctas-ctes.saldo-inicial')
					@include('common.ctas-ctes.abrir-caja')

					
					</div>
					</div>
					
<script>
    
    document.addEventListener('DOMContentLoaded', function(){
        
        window.livewire.on('show-modal-saldos-iniciales', Msg =>{
            $('#SaldoInicial').modal('show')
        })
        
        window.livewire.on('hide-modal-saldos-iniciales', Msg =>{
            $('#SaldoInicial').modal('hide')
        })
        
        window.livewire.on('show-agregar-editar', Msg =>{
            $('#SaldoInicial').modal('hide')
            $('#AgregarEditarSaldoInicial').modal('show')
        })
        
        window.livewire.on('hide-agregar-editar', Msg =>{
            $('#SaldoInicial').modal('show')
            $('#AgregarEditarSaldoInicial').modal('hide')
        })
        
        
    });
    
</script>		