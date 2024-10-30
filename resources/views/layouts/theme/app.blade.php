<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>SISTEMA PDV - Flaminco </title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    @include('layouts.theme.styles')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

</head>
<body class="dashboard-analytics">

    <!-- BEGIN LOADER -->
      <div id="load_screen">
      <div class="container-fluid loader-wrap">
          <div class="row h-100">
              <div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto text-center align-self-center">
                  <div class="logo-wallet">
                      <img style="width:65px; margin-top:15px;" src="assets/img/favicon.ico" alt="">
                  </div>
                  <p class="mt-4"><span class="text-secondary">Flaminco</span><br><strong>Por favor espere...</strong></p>
              </div>
          </div>
      </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
     @include('layouts.theme.header')
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">


        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
         @include('layouts.theme.sidebar')
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
          @if(Auth::user()->confirmed != 1)
          <div class="time-left">
            Modo prueba. Su suscripción gratuita caduca en
            {{ now()->diffInDays((Auth::user()->email_verified_at)->addDays(8)) }}
              dias.

              <a class="text-light" href="{{url('regist')}}">Comprar ahora</a>

          </div>
          @endif
            <div class="layout-px-spacing">

                @yield('content')

            </div>


        </div>
        <!--  END CONTENT AREA  -->


    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    @include('layouts.theme.scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->



</body>
</html>
