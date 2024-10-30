
<div class="main-wrapper">

			<div class="account-content">
				<div class="login-wrapper">
                    <div class="login-content">
                        <div class="login-userset">
                            <div class="login-logo logo-normal">
                                <img src="../../assets/pos/img/logo.png" alt="img">
                            </div>
							<a href="index.html" class="login-logo logo-white">
								<img src="../../assets/pos/img/logo.png"  alt="">
							</a>
							@if($paso == 1)
                            <div class="login-userheading">
                                <h3>Create una cuenta</h3>
                                <h4>Y empeza a gestionar tu negocio</h4>
                            </div>
                            @endif
                            
                            @if($paso == 2)
                            <div class="login-userheading">
                                <h3>Datos de tu empresa</h3>
                                <h4>Ayudanos a conocerte mejor</h4>
                            </div>
                            @endif
                            
                            
                            @if($paso == 3)
                            <div class="login-userheading">
                                <h3>Un plan hecho a tu medida</h3>
                                <h4>Ultimo paso</h4>
                            </div>
                            @endif
                            
                            <h4 class="mb-3">Paso {{$paso}} de 3</h4>
                            
                            
                            @include('auth.suscripcion-mercadopago-directa.paso1')    
                            
                            @include('auth.suscripcion-mercadopago-directa.paso2')
                            
                            @include('auth.suscripcion-mercadopago-directa.paso3')
                            

                            <input hidden value="{{$slug}}" wire:model="slug">
                            <input hidden value="{{$slug}}" wire:model="slug">

                            


                            <div hidden class="form-setlogin">
                                <h4>Or sign up with</h4>
                            </div>
                            <div hidden class="form-sociallink">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="../../assets/img/icons/google.png" class="me-2" alt="google">
                                            Sign Up using Google
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="../../assets/img/icons/facebook.png" class="me-2" alt="google">
                                            Sign Up using Facebook
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="login-img">
                        <img src="../../assets/pos/img/login.png" alt="img">
                    </div>
                </div>
			</div>
        </div>

<div @if($paso != 3) hidden @endif>
@php
 $PLAN_MONTO = $plan_suscripcion->monto;
 $auth_check = auth()->check();
 @endphp
 
@if( $auth_check)
<div class="card" style="padding:25px !important; text-align:center;">
    <form action="{{ route('confirmar.suscripcion') }}" method="POST" id="confirmar-suscripcion">
        @csrf
        <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
            <br>
            <br>
            <img style="width:200px !important;" src="/assets/pos/img/logo.png" alt="">
            <br>
            <br>
            <br>
            <h2 class="mb-3"><b>DETALLE DEL PAGO</b></h2>

            <div class="row justify-content-center">
                <div class="col-auto ">
                    <table class="table text-start table-bordered">
                        <tr>
                            <td class="px-3">
                                <h4>PLAN</h4>
                            </td>
                            <td class="text-start px-5">
                                {{ $plan_suscripcion->nombre }}
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
                                        <option value="{{$i}}" {{ ($quantity == $i)?'selected':''}}>{{$i}} usuarios extra</option>
                                        @else
                                        <option value="{{$i}}" {{ ($quantity == $i)?'selected':''}}>{{$i}} usuario extra</option>
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
                </div>
            </div>
            <br><br><br>
            <br>           

            <button type="submit" class="btn btn-primary" id="pagarMercadoPago">
                PAGAR EN MERCADO PAGO
            </button>
           
        </div>
    </form>
</div>
@else
@include('auth.inicio-sesion')
@endif

<script>
    const user_amount_value = {{ $user_amount_value   }};
    const plan_monto = {{ $PLAN_MONTO   }};

    function changeQuantity() {
        console.log("function changeQuantity change");
        console.log("function changeQuantity change -- user_amount_value -- " + user_amount_value);
        var users_quantity = document.getElementById("users_quantity").value;
        console.log("function changeQuantity change -- users_quantity -- " + users_quantity);

        var amount_users = users_quantity * user_amount_value;
        console.log("function changeQuantity change -- amount_users -- " + amount_users);

        var amount_new = '$';
        document.getElementById('monto-users-format').innerHTML = amount_new.concat( amount_users.toString() );

        var amount_total = (plan_monto + amount_users );
        var amount_total_new = '$';
        document.getElementById('monto-total-format').innerHTML = amount_new.concat( amount_total.toString() );

        //window.location.href = "{{ $url_redirect }}" + users_quantity;
    }

    /*
     $(document).ready(function() {

        $("#users_quantity").on("change", function() {
            console.log("users_quantity change");
            var users_quantity = $("#users_quantity").val();
            window.location.href = "{{ $url_redirect }}" + users_quantity;
        });

    });
    */
</script>

<script>

    document.addEventListener('DOMContentLoaded', function() {

        window.livewire.on('confirmado', () => {
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '946528043924786');
            fbq('track', 'InitiateCheckout');
            
            // Hacer submit al formulario
            document.getElementById('confirmar-suscripcion').submit();

        });
        
        window.livewire.on('enviar-face-lead', msg => {
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '946528043924786');
            fbq('track', 'Lead');
        });

    });
</script>


<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=946528043924786&ev=PageView&noscript=1" /></noscript>

</div>