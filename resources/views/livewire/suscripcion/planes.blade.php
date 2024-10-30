    				<div class="row">
    				    
	                            @if($suscripcion == null)
                                    <div style="text-align: left !important;" class="col-11 mt-3">
                                    <img style="width:120px !important;" src="../assets/pos/img/logo.png"   alt="">    
                                    </div>
                                    
                                    <div class="col-1 mt-3">
									<a style="color: #3b3f5c !important; font-size: 14px !important;" href="https://wa.me/+5493518681453?text=Hola,%20necesito%20ayuda%20para%20suscribirme%20en%20Flaminco%20app%20.{{$name}}{{$email}}.%20"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> 
									<text style="margin-top:3px !important;">
									Ayuda    
									</text>
									</a>
									
									
                                    </div>
                                @else
                                
                                @if($suscripcion->suscripcion_status == "inactiva")
                                    <div style="text-align: left !important;"  class="col-11 mt-3">
                                    <img style="width:120px !important;" src="../assets/pos/img/logo.png"   alt="">    
                                    </div>
                                    
                                    <div class="col-1 mt-3">
									<a style="color: #3b3f5c !important; font-size: 14px !important;" href="https://wa.me/+5493518681453?text=Hola,%20necesito%20ayuda%20para%20suscribirme%20en%20Flaminco%20app%20.{{$name}}{{$email}}.%20"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> 
									<text style="margin-top:3px !important;">
									Ayuda    
									</text>
									</a>
									
									
                                    </div>
                                
                                @endif
                                @endif
                                
                                @foreach($planes_disponibles as $pd)
                                
                                @if($pd->plan_id == 1)
                                <!----- PLAN 1 ----->
									<div class="col-12 col-md-6 col-lg-3 d-flex mt-3 mb-0 mt-sm-0" style="margin-bottom: 0px !important;">
                                    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
	                                            <h3 class="text-center">EMPRENDEDOR</h3>
	                                            @if($pd->origen == 0)
	                                            
	                                            <div  class="pricing-plan-label billed-monthly-label"><strong>$8.999</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            
	                                            @else
	                                            
	                                            <div style="text-decoration: line-through !important; margin-bottom: 0px !important;" class="pricing-plan-label billed-monthly-label"><strong>$8.999</strong>/ mes</div>
	                                            <div style="margin-top: 0px !important;"   class="pricing-plan-label billed-monthly-label"><strong>{{  number_format($pd->monto,0,",",".")  }}</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            
	                                            @endif
	                                            <div class="pricing-plan-features mb-4">
	                                                <strong> 1 sucursal</strong>
	                                                <ul>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de usuarios: 1</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de sucursales: 1</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Costo usuario extra: $1.999</li>
													<li>													
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Integracion con AFIP</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Acceso a todo el sistema</li>
													<li>
    												<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db001b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
													Integracion con tiendas online</li>
	                                                </ul>
	                                            </div>

                                                
                                                <a href="javascript:void(0)" wire:click="IniciarSuscripcion({{$pd->id}})" style="width:100% !important;" class="button btn btn-light margin-top-20">Suscribirse</a><br>
                                                <a href="https://wa.me/+5493518681453?text=Hola,%20quiero%20suscribirme%20a%20Flaminco%20app%20en%20el%20plan%20emprendedor.{{$name}}{{$email}}.%20Gracias" hidden>Ayuda</a>
                                            
                                            </div>
									</div>
                                <!----- / PLAN 1 ----->
								@endif
								
								@if($pd->plan_id == 2)
								<!----- PLAN 2 ----->
									<div class="col-12 col-md-6 col-lg-3 d-flex mt-3 mb-0 mt-sm-0" style="margin-bottom: 0px !important;">
                                    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
	                                            <h3 class="text-center">PLAN PEQUEñAS EMPRESAS</h3>
	                                            
	                                            @if($pd->origen == 0)
	                                            
	                                            
	                                            <div class="pricing-plan-label billed-monthly-label"><strong>$41.999</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            
	                                            @else
	                                            
	                                            	                                            
	                                            <div style="text-decoration: line-through !important; margin-bottom: 0px !important;" class="pricing-plan-label billed-monthly-label"><strong>$41.999</strong>/ mes</div>
	                                            <div  style="margin-top: 0px !important;" class="pricing-plan-label billed-monthly-label"><strong>{{  number_format($pd->monto,0,",",".")  }}</strong>/ mes</div>
	                                            
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            
	                                            @endif
	                                            <div class="pricing-plan-features mb-4">
	                                                <strong> 4 sucursales + Casa central</strong>
	                                                <ul>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de usuarios: 10</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de sucursales: 4</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Costo usuario extra: $1.999</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Integracion con AFIP</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Acceso a todo el sistema</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Integracion con Tiendas online</li>
												</ul>
	                                            </div>
	                                    
                                        <a href="javascript:void(0)" wire:click="IniciarSuscripcion({{$pd->id}})"  style="width:100% !important;" class="button btn btn-light margin-top-20">Suscribirse</a><br>
	                                    <a href="https://wa.me/+5493518681453?text=Hola,%20quiero%20suscribirme%20a%20Flaminco%20app%20en%20el%20plan%20pequeñas%20empresas.{{$name}}{{$email}}.%20Gracias" hidden>Ayuda</a>
                                        </div>
									</div>
                                <!----- / PLAN 2 ----->
								@endif
								
								@if($pd->plan_id == 3)
								<!----- PLAN 3 ----->
									<div class="col-12 col-md-6 col-lg-3 d-flex mt-3 mb-0 mt-sm-0" style="margin-bottom: 0px !important;">
                                    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
	                                            <h3 class="text-center">PLAN MEDIANAS EMPRESAS</h3>
	                                            @if($pd->origen == 0)
	                                            
	                                            <div class="pricing-plan-label billed-monthly-label"><strong>$71.999</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            
	                                            @else 
	                                            
	                                            <div style="text-decoration: line-through !important; margin-bottom: 0px !important;" class="pricing-plan-label billed-monthly-label"><strong>$71.999</strong>/ mes</div>
	                                            <div  style="margin-top: 0px !important;" class="pricing-plan-label billed-monthly-label"><strong>{{  number_format($pd->monto,0,",",".")  }}</strong>/ mes</div>

	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            
	                                            @endif
	                                            <div class="pricing-plan-features mb-4">
	                                                <strong> 9 sucursales + Casa central</strong>
	                                                <ul>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de usuarios: 20</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de sucursales: 9</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Costo usuario extra: $1.999</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Integracion con AFIP</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Acceso a todo el sistema</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Integracion con Tiendas online</li>
												</ul>
	                                            </div>
	                                   
                                        <a href="javascript:void(0)" wire:click="IniciarSuscripcion({{$pd->id}})"  style="width:100% !important;" class="button btn btn-light margin-top-20">Suscribirse</a><br>
	                                    <a href="https://wa.me/+5493518681453?text=Hola,%20quiero%20suscribirme%20a%20Flaminco%20app%20en%20el%20plan%20medianas%20empresas.{{$name}}{{$email}}.%20Gracias" hidden>Ayuda</a>
	                                   
	                                    </div>
									</div>
								<!----- / PLAN 3 ----->
								@endif
								
								@if($pd->plan_id == 4)
								<!----- PLAN 4 ----->
									<div class="col-12 col-md-6 col-lg-3 d-flex mt-3 mb-0 mt-sm-0" style="margin-bottom: 0px !important;">
                                    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
	                                            <h3 class="text-center">PLAN GRANDES EMPRESAS</h3>
	                                            @if($pd->origen == 0)
	                                            <div class="pricing-plan-label billed-monthly-label"><strong>$149.999</strong>/ mes</div>
	                                            <div class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
	                                            @else
	                                            <div style="text-decoration: line-through !important; margin-bottom: 0px !important;" class="pricing-plan-label billed-monthly-label"><strong>$149.999</strong>/ mes</div>
	                                            <div style="margin-top: 0px !important;" class="pricing-plan-label billed-monthly-label"><strong>{{  number_format($pd->monto,0,",",".")  }}</strong>/ mes</div>

	                                            @endif
	                                            <div class="pricing-plan-features mb-4">
	                                                <strong> 24 sucursales + Casa central</strong>
	                                                <ul>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de usuarios: 50</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Cantidad de sucursales: 24</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Costo usuario extra: $1.999</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Integracion con AFIP</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Acceso a todo el sistema</li>
													<li>
													<svg xmlns="http://www.w3.org/2000/svg" style="margin-bottom:2px; margin-right:7px;"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#008331" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
													Integracion con Tiendas online</li>
												</ul>
	                                            </div>
	                                            
                                                <a href="javascript:void(0)" wire:click="IniciarSuscripcion({{$pd->id}})"  style="width:100% !important;" class="button btn btn-light margin-top-20">Suscribirse</a><br>
	                                      	    <a href="https://wa.me/+5493518681453?text=Hola,%20quiero%20suscribirme%20a%20Flaminco%20app%20en%20el%20plan%20grandes%20empresas.{{$name}}{{$email}}.%20Gracias" hidden>Ayuda</a>
                                              </div>
									</div>
								<!----- / PLAN 4 ----->	
                                @endif 
                                
                                @endforeach
						</div>