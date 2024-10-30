<div @if($paso != 3) hidden @endif>
@php
 $PLAN_MONTO = $plan_suscripcion->monto;
 $auth_check = auth()->check();
 @endphp




<div class="pricing-plan mb-sm-3 mt-sm-3" style="border: 1px solid #e0e6ed !important;
    background: white;
    padding: 25px; border-radius: 4px;">
    										<div>
												<h3 class="text-center">PLAN ELEGIDO:</h3>


    											@if($plan_suscripcion->id == 1)
    											<h3 class="text-center mb-3">EMPRENDEDOR</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>{{'$' . number_format($plan_suscripcion->monto, 0, ',', '.')  }}</strong>/ mes</div>
    											<div  hidden class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
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
    														Costo usuario extra: $5.000
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

    											@if($plan_suscripcion->id == 2)
    											<h3 class="text-center mb-3">PLAN PEQUENAS EMPRESAS</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>{{'$' . number_format($plan_suscripcion->monto, 0, ',', '.')  }}</strong>/ mes</div>
    											<div hidden  class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
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
    														Costo usuario extra: $5.000
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

    											@if($plan_suscripcion->id == 3)
    											<h3 class="text-center mb-3">PLAN MEDIANAS EMPRESAS</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>{{'$' . number_format($plan_suscripcion->monto, 0, ',', '.')  }}</strong>/ mes</div>
    											<div hidden  class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
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
    														Costo usuario extra: $5.000
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

    											@if($plan_suscripcion->id == 4)
    											<h3 class="text-center mb-3">PLAN GRANDES EMPRESAS</h3>
    											<div class="pricing-plan-label billed-monthly-label"><strong>{{'$' . number_format($plan_suscripcion->monto, 0, ',', '.')  }}</strong>/ mes</div>
    											<div hidden  class="pricing-plan-label billed-yearly-label"><strong>$290</strong>/ yearly</div>
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
    														Costo usuario extra: $5.000
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

												@if($plan_suscripcion->id == 5)

    											<h3 class="text-center mb-3">EMPRENDEDOR - INFLUENCER</h3>
    											@endif

												@if($plan_suscripcion->id == 6)

    											<h3 class="text-center mb-3">PEQUENAS EMPRESAS - INFLUENCER</h3>
    											@endif

												@if($plan_suscripcion->id == 7)

    											<h3 class="text-center mb-3">MEDIANAS EMPRESAS - INFLUENCER</h3>
    											@endif

												@if($plan_suscripcion->id == 8)

    											<h3 class="text-center mb-3">GRANDES EMPRESAS - INFLUENCER</h3>
    											@endif

    											<button hidden type="button" style="width:100% !important;" onclick="confirmarCancelacion()" class="button btn btn-danger margin-top-20" id="cancelarSuscripcion">Cancelar suscripcion</button>
    										</div>

                                        <h5>¡Empeza tu prueba gratis de 14 dias ahora!</h5>
    									</div>

<form  action="{{ route('confirmar.suscripcion') }}" method="POST" id="confirmar-suscripcion">
        @csrf
            <table hidden class="table text-start table-bordered">
                                    <tr>
                                        <td class="px-3">
                                            <h4>PLAN</h4>
                                        </td>
                                        <td class="text-start px-5">
                                            {{ $plan_suscripcion->nombre }}
                                            <input type="hidden" name="free_days" value="14">
                                            <input type="hidden" name="plan_suscripcion" value="{{ $plan_suscripcion->id }}">
                                        </td>
                                        <td class="text-end">{{'$' . number_format($plan_suscripcion->monto, 0, ',', '.')  }} </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">
                                            <h4>USUARIOS</h4>
                                        </td>
                                        <td class="text-start px-5">
                                            <select id="users_quantity" name="users_quantity" class="text-start col-9 form-select" onchange="changeQuantity()">
                                                @for($i=0; $i<=10; $i++) @if($i> 1)
                                                    <option value="{{$i}}" {{ ($quantity == $i)?'selected':''}}>{{$i}} usuarios</option>
                                                    @else
                                                    <option value="{{$i}}" {{ ($quantity == $i)?'selected':''}}>{{$i}} usuario</option>
                                                    @endif
                                                @endfor
                                            </select>
            
                                        </td>
                                        <td class="text-end" id="monto-users-format">{{'$' . number_format($users_amount, 0, ',', '.')  }} </td>
                                    </tr>
            
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3">
                                            <h4>MONTO</h4>
                                        </td>
                                        <td></td>
                                        <td>
                                            <h3 class="text-end" id="monto-total-format">{{'$' . number_format($monto, 0, ',', '.')  }}</h3>
                                        </td>
                                    </tr>
                                </table>
                    
            <button hidden type="submit" class="btn btn-primary" id="pagarMercadoPago">
                Siguiente
            </button>

    </form>

</div> 

@if($paso == 3)
    <div class="form-login" style="display: flex !important;">
    <button wire:click="IrPaso2" class="btn btn-login" style="width: 50% !important; background: #333 !important; border: solid #333 !important; margin-right:3px !important;">< Anterior </button>
    <button wire:click="ConfirmarRegistro" class="btn btn-login" style="width: 50% !important; margin-left:3px !important;">Empezar prueba gratis ></button>
    <br>
    </div>
    <p style="font-size: 14px;">Te redirigiremos a mercado pago</p>

@endif

                            