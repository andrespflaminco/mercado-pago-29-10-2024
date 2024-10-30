<div>	                 
@if($ver_configuracion == 0)
<div hidden class="row mb-3">
    <div class="col-12">
    <div class="card mb-0" style="padding:15px;
    margin-top: -25px;
    margin-left: -25px;
    border: none;
    border-radius: 0px;
    border-bottom: 1px solid #e8ebed;">
    <div class="row">
 
    <div style="width: auto;"> 
    
    <div style="margin-left: 15px; width: 90%; !important" class="dropdown">
        <button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
           Filtro por Sucursales
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li>
                <label style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                    <input type="checkbox" style="margin-right: 10px;"> <text> Seleccionar todas </text>
                </label>
            </li>
            <div class="dropdown-divider"></div>
            <li>
                <label style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                    <input type="checkbox" wire:model.defer="selectedSucursalesCheckbox.{{auth()->user()->id}}" style="margin-right: 10px;">  <text> {{auth()->user()->name}} </text>
                </label>
            </li>
            @foreach($sucursales as $sucursal)
            <li>
                <label style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                    <input type="checkbox" wire:model.defer="selectedSucursalesCheckbox.{{$sucursal->sucursal_id}}" style="margin-right: 10px;"> <text> {{$sucursal->name}} </text>
                </label>
            </li>
            @endforeach
             <div class="dropdown-divider"></div>
             <div style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                 <button class="applyBtn btn btn-sm btn-primary" wire:click="AplicarElegirSucursal()">Aplicar</button>
             </div>
        </ul>
    </div>

	
    </div>        
    </div>

    

    </div>    
    </div>
</div>
	                <div class="page-header">
					<div class="page-title">
							<h4>Cuenta corriente Clientes</h4>
							<h6>Ver listado de deuda de los clientes</h6>
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
										<a style="font-size:14px !important; padding:5px !important; background: #FF9F43 !important; width: auto !important; color: white;" wire:click="Filtros('{{$MostrarOcultar}}')"  class="btn btn-filter" >
                                    	<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                    	<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
                                    	<div style="margin-left: 5px; margin-right: 5px; font-size: 14px !important;">
                                    	<b>Filtros</b> 
                                    	</div>
                                        </a>

									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar por nombre.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
									<ul>
									    @can('ver configuracion cuenta corriente')
									    <li>
									<a style="font-size:12px !important; padding:5px !important; background: #F8F9FA !important; color:#212B36 !important; border:solid 1px #212B36 !important; " class="btn btn-cancel" href="javascript:void(0)" wire:click="AbrirModalConfiguracion()">
									 <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
								    Configuracion</a>
								    </li>
								    @endcan
										<li>
										<a hidden  style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarReporte()"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
										Exportar </a>
										</li>
									</ul>
								</div>
							</div>
                            <div class="card mb-3"  style="border: solid 1px #eee; padding: 20px; display:{{$MostrarOcultar}};">
                            <div class="row">
                                <!-- Filtro por tipo de saldo -->
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Saldo de la cuenta corriente: </label>
                                        <select wire:model='tipo_saldo' class="form-control">
                                            <option value="all"> Todos </option>
                                            <option value="0"> Clientes con saldo 0 </option>
                                            <option value="1"> Saldo Deudor </option>
                                            <option value="2"> Saldo a favor </option>
                                        </select>
                                    </div>
                                </div>
                                
                                @if($tipo_saldo == 1)
                                <!-- Filtro por monto mínimo -->
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Monto desde: </label>
                                        <input type="number" wire:model="monto_minimo" class="form-control" placeholder="Monto desde">
                                    </div>
                                </div>
                            
                                <!-- Filtro por monto máximo -->
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Monto hasta: </label>
                                        <input type="number" wire:model="monto_maximo" class="form-control" placeholder="Monto hasta">
                                    </div>
                                </div>
                                @endif
                                
                            </div>
							</div>
                                    <!---------- FILTRO DE ESTADO -------->
                                	
                                	@can('eliminar productos')   
                                	<div id="accion-lote" class="col-2 mt-2 ">
                                	<div>
                                	<div style="padding-left: 0;" class="col-12 ml-0">
                                	<div  class="input-group">
                                	<a class="{{ $estado_filtro == 0 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(0)">Activos</a> | <a class="{{ $estado_filtro == 1 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(1)">Papelera</a>    
                                	</div>	
                                	</div>	    
                                	</div>    		    
                                
                                	</div>
                                	@endcan
                                			    
                                	<!----------------------------------->
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
 <tr>
                                            <th wire:click="OrdenarColumna('id_cliente')">
                                            Codigo
                                            @if ($columnaOrden == 'id_cliente')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                            </th>
                                            <th wire:click="OrdenarColumna('nombre_cliente')">
                                            Cliente
                                            @if ($columnaOrden == 'nombre_cliente')
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
                                                <td>{{$d->id_cliente}}</td>
                                                <td>
                                                <a style="margin-right: 10px;" href="{{ url('movimientos-clientes'. '/' . $d->id   ) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </a>
                                                <!---    <a style="color: #FF9F43 !important;" href="{{ url('movimientos-proveedores'. '/' . $d->id   ) }}">{{$d->nombre_proveedor}}</a> -->
                                                <a style="color: #FF9F43 !important;" href="{{ url('movimientos-clientes'. '/' . $d->id   ) }}">{{$d->nombre_cliente}}</a>
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

                    
					@include('common.ctas-ctes.agregar-editar-saldo-inicial')
					@include('common.ctas-ctes.listado-cajas')
					@include('common.ctas-ctes.saldo-inicial')
					@include('common.ctas-ctes.abrir-caja')
					
					</div>


@else

<div class="page-header">
					<div class="page-title">
							<h4>Configuracion de cuenta corriente Clientes</h4>
							<h6></h6>
						</div>
						<div class="page-btn">               											    
                		
						    
						</div>
					</div>
					
                    
<!-- /product list -->
<div class="card">
	<div class="card-body">
    
    						<div class="row mb-4"> 
    						   
    							<div class="col-lg-6 col-sm-12 col-12">
    								<div class="form-group">
    									<label>Forma de gestionar la cuenta corriente</label>
    									<select class="form-control" wire:model="configuracion_valor">
    									    <option value="por_sucursal">Cada sucursal tiene su cuenta corriente con el cliente</option>
    									    <option value="compartido">Cuenta corriente compartida para toda la cadena </option>
    									</select>
    								</div>
    							</div>
    							<div class="col-lg-6 col-sm-12 col-12">
    							</div>
    						    <div class="col-lg-6 col-sm-12 col-12">
    								    <input type="checkbox" wire:model="configuracion_sucursales_agregan_pago">
    									<label>Las sucursales pueden cobrar en ventas de otras sucursales</label>
    							</div>
    							<div class="col-lg-6 col-sm-12 col-12">
    							</div>
    						</div>
    						
    						<div class="col-lg-12">
                        		<a class="btn btn-cancel" wire:click.prevent="CerrarModalConfiguracion()"  data-bs-dismiss="modal">Cancelar</a>
    					     	<a class="btn btn-submit me-2" wire:click="UpdateConfiguracion()" >Guardar</a>
    						</div>
	</div>
</div>
@endif
</div>
					
<script>
    
    document.addEventListener('DOMContentLoaded', function(){
        
        window.livewire.on('abrir-caja-show', Msg =>{
            $('#AbrirCaja').modal('show')
        })
        
        window.livewire.on('abrir-caja-hide', Msg =>{
            $('#AbrirCaja').modal('hide')
        })
        
        window.livewire.on('listado-cajas-show', Msg =>{
            $('#ListadoCajas').modal('show')
        })
        
        window.livewire.on('listado-cajas-hide', Msg =>{
            $('#ListadoCajas').modal('hide')
        })
        
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