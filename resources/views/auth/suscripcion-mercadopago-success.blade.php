@extends('layouts.theme-pos-especial.app-base')

@section('content')
<div style="padding:25px !important; text-align:center;">
    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
        <br>
        <br>
        <img style="width:200px !important;" src="/assets/pos/img/logo.png" alt="">
        <br>
        <br>
        <br>
        <h2 class="mb-3"><b>DETALLE DE LA SUSCRIPCIÓN</b></h2>

        <div class="row justify-content-center mb-5">
            <div class="col-12 ">
            
                
                @if($result)
                <h3 class="text-success">El pago de su suscripción se ha procesado correctamente</h3>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
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
                            fbq('track', 'Compra');
                            
                    });
                </script>
                    <br><br><br>
        <br>    
                <a href="{{ url('pos') }}">VOLVER AL INICIO</a>
                
                @else
                <h3 class="text-danger">Ha ocurrido un error al procesar el pago de su suscripción en el sistema</h3>
                <p class="text-danger">Por favor, contactese con Atención al Cliente</p>
                
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
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
                            fbq('track', 'AddPaymentInfo');
                            
                    });
                </script>      
                <br><br><br>
        <br>  
                <a href="/">VOLVER AL INICIO</a>
                @endif
                      
                
            </div>

        </div>


    </div>
</div>





@endsection