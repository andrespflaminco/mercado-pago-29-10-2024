<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Flaminco app </title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    @include('layouts.theme-pos.styles')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '946528043924786');
    //fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=946528043924786&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->        
</head>
<body>
    

    <!-- BEGIN LOADER -->
   <div wire:loading id="global-loader" >
			<div class="whirly-loader"> </div>
	</div>
    <!--  END LOADER -->

    @php
    if(auth()->user()->url_origen != null){
    $url_origen = auth()->user()->url_origen;
    } else {
    $url_origen = "https://flaminco.com.ar/planes/";
    }
    @endphp
    
    <!-- Redirecci贸n si el usuario cumple las condiciones -->
    @if(auth()->check() && (auth()->user()->confirmed == 0) && (0 < auth()->user()->intencion_compra) && (now() > (auth()->user()->prueba_hasta)))
        <script>window.location.href = "https://app.flamincoapp.com.ar/suscribirse/1";</script>
    @endif
    
    <!-- Redirecci贸n si el usuario cumple las condiciones -->
    @if(auth()->check() && (auth()->user()->confirmed == 0) && (now() > (auth()->user()->prueba_hasta)))
        <script>window.location.href = "https://app.flamincoapp.com.ar/suscribirse/1";</script>
    @endif
    
    
	<!-- Main Wrapper -->
    <div class="main-wrapper">
    
    <!--  BEGIN NAVBAR  -->
     @include('layouts.theme-pos.header')
    <!--  END NAVBAR  -->
    
    <!--  BEGIN MAIN CONTAINER  -->
    
    <!--  BEGIN SIDEBAR  -->
    @include('layouts.theme-pos.sidebar')
    <!--  END SIDEBAR  -->
	
	<div class="page-wrapper" id="container">
	<div class="content" id="content" >
    <!--  BEGIN CONTENT AREA  -->

    @if(Auth::user()->confirmed != 1)
      <div style="margin-top: -25px; background: red; color: white; margin-left: -25px; margin-right: -25px; margin-bottom: 25px; padding: 5px 5px 5px 15px;">
        Modo prueba. Su suscripcion gratuita caduca en
        {{ now()->diffInDays( (Auth::user()->prueba_hasta) ) }}
          dias.

      <a class="text-light" href="{{$url_origen}}">Comprar ahora</a>

      </div>
    @endif
    
                @yield('content')

    
        </div>
        <!--  END CONTENT AREA  -->

    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    @include('layouts.theme-pos.scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    @include('layouts.theme-pos.chat-bot')


</body>
</html>
