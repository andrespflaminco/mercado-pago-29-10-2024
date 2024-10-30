    <div wire:ignore.self class="layout-px-spacing">

    	<div class="page-header">
    		<div class="page-title">
    			<h4>Datos de mi suscripcion a flaminco</h4>
    			<h6>Configure la suscripcion a flaminco</h6>
    		</div>
    	</div>

    	@if(session('status'))
    	<div class="alert alert-success alert-dismissible fade show" role="alert">
    		<strong>{{ session('status') }} </strong>
    		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    	</div>
    	@endif

    	<!-- /add -->
    	<div class="card">
    		<div class="card-body">
    		    @if(1072 < auth()->user()->id)
    			<div class="row">
    				<div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
    					<div class="section general-info">
    						<div class="info">

    							@if($suscripcion != null)

    							@if($suscripcion->suscripcion_status == "activa")
    							<div class="row" style="margin:0 auto !important;">

    								<div class="col-12 col-md-6 col-lg-4 d-flex mt-3 mb-0 mt-sm-0" style="margin-bottom: 0px !important; text-align:center !important;">

    									<div class="pricing-plan mb-lg-6 mb-sm-3 mt-sm-3" style="border:1px solid #e0e6ed !important;">
    										<div>
												<h3 class="text-center">PLAN ELEGIDO:</h3>


    											@if($suscripcion->plan_id_flaminco == 1)
    											<h3 class="text-center">EMPRENDEDOR</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>$8.999</strong>/ mes</div>
    											<div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
    											<div class="pricing-plan-features mb-4">
    												<strong> 1 sucursal</strong>
    												<ul>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de usuarios: 1
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de sucursales: 1
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Costo usuario extra: $1.999
    													</li>
    													<li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Integracion con AFIP
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Acceso a todo el sistema
    													</li>
    													<li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
    															<line x1="18" y1="6" x2="6" y2="18"></line>
    															<line x1="6" y1="6" x2="18" y2="18"></line>
    														</svg>
    														Integracion con tiendas online
    													</li>
    												</ul>
    											</div>
    											@endif

    											@if($suscripcion->plan_id_flaminco == 2)
    											<h3 class="text-center">PLAN PEQUENAS EMPRESAS</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>$41.999</strong>/ mes</div>
    											<div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
    											<div class="pricing-plan-features mb-4">
    												<strong> 4 sucursales + Casa central</strong>
    												<ul>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de usuarios: 10
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de sucursales: 4
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Costo usuario extra: $1.999
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Integracion con AFIP
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Acceso a todo el sistema
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Integracion con Tiendas online
    													</li>
    												</ul>
    											</div>
    											@endif

    											@if($suscripcion->plan_id_flaminco == 3)
    											<h3 class="text-center">PLAN MEDIANAS EMPRESAS</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>$71.999</strong>/ mes</div>
    											<div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
    											<div class="pricing-plan-features mb-4">
    												<strong> 9 sucursales + Casa central</strong>
    												<ul>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de usuarios: 20
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de sucursales: 9
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Costo usuario extra: $1.999
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Integracion con AFIP
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Acceso a todo el sistema
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Integracion con Tiendas online
    													</li>
    												</ul>
    											</div>
    											@endif

    											@if($suscripcion->plan_id_flaminco == 4)
    											<h3 class="text-center">PLAN GRANDES EMPRESAS</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>$149.999</strong>/ mes</div>
    											<div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
    											<div class="pricing-plan-features mb-4">
    												<strong> 24 sucursales + Casa central</strong>
    												<ul>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de usuarios: 50
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Cantidad de sucursales: 24
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Costo usuario extra: $1.999
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Integracion con AFIP
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Acceso a todo el sistema
    													</li>
    													<li>
    														<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
    															<polyline points="20 6 9 17 4 12"></polyline>
    														</svg>
    														Integracion con Tiendas online
    													</li>
    												</ul>
    											</div>
    											@endif

												@if($suscripcion->plan_id_flaminco == 5)

    											<h3 class="text-center">EMPRENDEDOR - INFLUENCER</h3>
    											@endif

												@if($suscripcion->plan_id_flaminco == 6)

    											<h3 class="text-center">PEQUENAS EMPRESAS - INFLUENCER</h3>
    											@endif

												@if($suscripcion->plan_id_flaminco == 7)

    											<h3 class="text-center">MEDIANAS EMPRESAS - INFLUENCER</h3>
    											@endif

												@if($suscripcion->plan_id_flaminco == 8)

    											<h3 class="text-center">GRANDES EMPRESAS - INFLUENCER</h3>
    											@endif

    											<button type="button" style="width:100% !important;" onclick="confirmarCancelacion()" class="button btn btn-danger margin-top-20" id="cancelarSuscripcion">Cancelar suscripcion</button>
    										</div>


    									</div>
    								</div>





    								<div class="col-12 col-md-6 col-lg-8 d-flex mt-3 mb-0 mt-sm-0" style="margin-bottom: 0px !important; text-align:center !important;">
    									<div class="pricing-plan mb-lg-6 mb-sm-3 mt-sm-3" style="border:1px solid #e0e6ed !important;">
    										<h3 class="text-center">DATOS DE TUS PAGOS</h3>
    										<br>
    										<div style="text-align: left !important;">
    											<div class="row mb-5">
    												<div class="col-6">
    													<h6>Proximo pago: {{\Carbon\Carbon::parse($suscripcion->proximo_cobro)->format('d-m-Y')}} </h6>
    													<h6>Estado actual: <text style="text-transform: uppercase;"> {{ $suscripcion->cobro_status }} </text></h6>

    												</div>
    												<div class="col-6">
    													<h6>Cantidad de Usuarios extra: {{ $suscripcion->users_count }} </h6>
    													<h6>Monto por Usuarios: ${{ number_format($suscripcion->users_amount, 0, ',', '.') }} </text></h6>
    													<h6>Monto por Módulos: ${{ number_format($suscripcion->modulos_amount, 0, ',', '.') }} </text></h6>
    												</div>
    												
    											</div>
    											<div class="col-12">
    												<p style="font-weight:700;">Ultimos pagos </p>
    												<div class="table-responsive">
    													<table class="table">
    														<thead>
    															<tr>
    																<th>Fecha ultimo <br> intento de cobro</th>
    																<th>Suscripción</th>
    																<th>Monto</th>
    																<th>Estado</th>
    															</tr>
    														</thead>
    														<tbody>
    															@foreach($suscripciones_cobros as $sc)
    															<tr>
    																<td>{{\Carbon\Carbon::parse($sc->date_last_updated)->format('d-m-Y')}} </td>
    																<td>
    																	{{ $sc->suscripcion_id }}
    																</td>
    																<td>
    																	${{ number_format($sc->monto_mensual, 0, ',', '.') }}
    																</td>
    																<td>
    																	@if($sc->status == "approved")
    																	PAGO
    																	@else
    																	{{$sc->status}}
    																	@endif
    																</td>
    															</tr>
    															@endforeach
    														</tbody>
    													</table>
    												</div>
    											</div>

    										</div>
    									</div>
    								</div>

    								<div class="col-12 col-md-6 col-lg-8 d-flex mt-3 mb-0 mt-sm-0" style="margin-bottom: 0px !important; text-align:center !important;">
    									<div class="pricing-plan mb-lg-6 mb-sm-3 mt-sm-3" style="border:1px solid #e0e6ed !important;">
    										<h3 class="text-center">CAMBIAR SUSCRIPCIÓN</h3>
    										<br>
    										<div style="text-align: left !important;">
    											<div class="row">
    												<div class="col-12">
    													<table>
    														<tr>
    															<td class="px-5">PLAN </td>
    															<td>
    																<select class="form-select" id="plan_suscripcion_id" name="plan_suscripcion_id">
    																	@foreach($planes as $planSuscripcion)
    																	<option value="{{ $planSuscripcion->id}}" {{ ($suscripcion->plan_id_flaminco == $planSuscripcion->id)?'selected':'' }}>{{ $planSuscripcion->nombre}}</option>
    																	@endforeach
    																</select>
    															</td>
    														</tr>
    														<tr>
    															<td class="px-5">CONTRATAR USUARIOS EXTRA </td>
    															<td>
    																<select class="form-select" id="users_count_id" name="users_count_id">
    																	<option value="">---</option>
    																	@for($i = 0; $i <= $user_count_max_value; $i++) 
																		@if($i==1) 
																		<option value="{{ $i}}" {{ ($suscripcion->users_count == $i)?'selected':'' }}>{{ $i}} usuario: ${{ number_format(($i * $user_amount_value), 0, ',', '.') }}</option>
    																	@else
    																	<option value="{{ $i}}" {{ ($suscripcion->users_count == $i)?'selected':'' }}>{{ $i}} usuarios: ${{ number_format(($i * $user_amount_value), 0, ',', '.') }}</option>
    																	@endif

    																	@endfor
    																</select>
    															</td>
    														</tr>
    														<tr>
    															<td class="px-5">MÓDULOS </td>
    															<td>
    																<select class="form-select basic" id="modulos_id" name="modulos_id" multiple="multiple" aria-label="multiple select example">
    																	<option value="">---</option>
    																	@foreach($modulos as $modulo)
    																	<option value="{{ $modulo->id}}" {{ (in_array($modulo->id, $modulos_selected))?'selected':'' }}>{{ $modulo->nombre}}: ${{ number_format($modulo->monto, 0, ',', '.') }}</option>
    																	@endforeach
    																</select>
    															</td>
    														</tr>
    													</table>
    												</div>
    											</div>

    											<div class="row mt-5">
    												<div class="col-12 text-center">
    													<button type="button" onclick="confirmarActualizacion()" class="button btn btn-primary margin-top-20" id="actualizarSuscripcion">Actualizar suscripcion</button>
    												    <button hidden type="button" wire:click="CrearCheckout()" class="button btn btn-primary margin-top-20">Actualizar suscripcion 2</button>
    												    
    												    
    												</div>
    											</div>

    										</div>
    									</div>
    								</div>
    							</div>
    							
    							<div hidden class="row">
                                <div class="col-12" style="border:1px solid #e0e6ed !important;">
                                    <h3 class="text-center">MODULOS</h3>
                                    <br>
                                    <div class="row">
                                        @foreach($modulos as $modulo)
                                        <div class="col-md-4 mb-4">
                                            <div class="card">
                                                @if($modulo->image != null)
                                                <img style="max-width: 250px; margin: 0 auto;" src="{{ asset('storage/products/' . $modulo->image ) }}" alt="{{$modulo->name}}" class="card-img-top">
                                                @else
                                                <img style="max-width: 250px; margin: 0 auto;" src="{{ asset('storage/products/noimg.png') }}" alt="{{$modulo->name}}" class="card-img-top">
                                                @endif
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">{{$modulo->nombre}}</h6>
                                                    <h5 class="card-title">$ {{$modulo->monto}}</h5>
                                                    <p class="card-text">{{$modulo->descripcion}}</p>
                                                    <button type="button" wire:click="ContratarModulo({{$modulo->id}})" class="btn btn-secondary margin-top-20">COMPRAR</button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

    							@else
    							<div class="row text-center">
    								<div style="border:none !important;" class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
    									<br>
    									<br>
    									<img style="width:200px !important;" src="assets/pos/img/logo.png" alt="">
    									<br>
    									<br>
    									<br>
    									<h2><b>SUSCRIBITE Y EMPEZA A GOZAR DE LOS BENEFICIOS DE FLAMINCO</b></h2>
    									<b>Elegi el plan que mas se ajuste a vos</b>
    									<br><br><br>
    									<br>
    									<a class="btn btn-primary" href="https://flaminco.com.ar/planes/">
    										ELEGIR PLAN >
    									</a>
    								</div>

    							</div>
    							@endif

    							@else
    							<div class="row text-center">
    								<div style="border:none !important;" class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
    									<br>
    									<br>
    									<img style="width:200px !important;" src="assets/pos/img/logo.png" alt="">
    									<br>
    									<br>
    									<br>
    									<h2><b>SUSCRIBITE Y EMPEZA A GOZAR DE LOS BENEFICIOS DE FLAMINCO - </b></h2>
    									<b>Elegi el plan que mas se ajuste a vos</b>
    									<br><br><br>
    									<br>
    									<a class="btn btn-primary" href="https://flaminco.com.ar/planes/">
    										ELEGIR PLAN >
    									</a>
    								</div>

    							</div>

    							@endif

    						</div>
    						<br>
    						<br>
    					</div>
    					<div class="row">
    						<div class="col-lg-12">
    						</div>
    					</div>
    				</div>

    			</div>
    			@else
    			<div class="row">
    							<div class="row text-center">
    								<div style="border:none !important;" class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
    									<br>
    									<br>
    									<img style="width:200px !important;" src="assets/pos/img/logo.png" alt="">
    									<br>
    									<br>
    									<br>
    									<h2><b>CONTACTATE CON UN ASESOR PARA MODIFICAR TU SUSCRIPCIÓN</b></h2>
    									<br><br><br>
    									<br>
    									<a class="btn btn-primary" href="https://wa.me/+5493518105763?text=Quiero%20asesoramiento%20con%20mi%20suscripcion.." target="_blank" >
    									CONTACTAR >
    									</a>
    								</div>

    							</div>
    			    
    			</div>
    			@endif
    		</div>

    		<div hidden>
    			<button wire:click="ClickFacebook">Click</button>
    			<!-- Vista de Blade -->
    			<input type="text" name="fbclid" id="fbclid" value="">
    		</div>


    	</div>

    	<script>
    // Obtener el valor del parámetro fbclid de la URL
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    var fbclid = getParameterByName('fbclid');

    //alert(fbclid);

    function confirmarCancelacion() {
        console.log('confirmarCancelacion - ');
        @if ($suscripcion)
            if (confirm("¿Esta seguro que quiere cancelar su suscripción?") == true) {
                console.log('confirmarCancelacion - true');
                window.location.href = "/cancelarSuscripcion/{{ $suscripcion->suscripcion_id }}"
            } else {
                console.log('confirmarCancelacion - false');
            }
        @else
            console.log('No hay suscripción para cancelar.');
        @endif
    }

    function confirmarActualizacion() {
        console.log('confirmarActualizacion - ');
        @if ($suscripcion)
            var plan_suscripcion_id = document.getElementById("plan_suscripcion_id").value;
            var users_count_id = document.getElementById("users_count_id").value;
            var modulos = document.getElementById("modulos_id").selectedOptions;
            var modulos_id = '';

            for (let i = 0; i < modulos.length; i++) {
                modulos_id += modulos[i].value + ',';
            }

            modulos_id = modulos_id.slice(0, -1);

            console.log(modulos_id);

            if (confirm("¿Esta seguro que quiere actualizar su suscripción?") == true) {
                console.log('confirmarActualizacion - true');
                window.location.href = "/actualizarSuscripcion/{{ $suscripcion->suscripcion_id }}/" + plan_suscripcion_id + '/' + users_count_id + '/' + modulos_id;
            } else {
                console.log('confirmarActualizacion - false');
            }
        @else
            console.log('No hay suscripción para actualizar.');
        @endif
    }
</script>
