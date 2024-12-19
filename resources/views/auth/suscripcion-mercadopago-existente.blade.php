@php
$auth_check = auth()->check();
@endphp

@if( $auth_check)
<div class="card" style="padding:25px !important; text-align:center;">
    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
        <br>
        <br>
        <img style="width:200px !important;" src="/assets/pos/img/logo.png" alt="">
        <br>
        <br>
        <br>
        <h2 class="mb-3"><b>SUSCRIPCIÓN</b></h2>

        <div class="row justify-content-center">
            <div class="col-auto ">
                <label>Ya tienes una suscripción activa</label>
            </div>
        </div>
        <br><br><br>
        <br>

        <a href="{{ $url_redirect }}" class="btn btn-primary" id="configurarSuscripcion">
            CONFIGURAR SUSCRIPCIÓN
        </a>

    </div>
</div>
@else
@include('auth.inicio-sesion')
@endif

<script>
    document.addEventListener("livewire:load", function(event) {

        @if($auth_check)

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

        window.livewire.emit('IniciarProceso', '');
        @endif

    });

    document.addEventListener('DOMContentLoaded', function() {

        window.livewire.on('enviar-face', msg => {
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

            window.livewire.emit('IniciarProceso', '');
        });

    });
</script>

<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=946528043924786&ev=PageView&noscript=1" /></noscript>