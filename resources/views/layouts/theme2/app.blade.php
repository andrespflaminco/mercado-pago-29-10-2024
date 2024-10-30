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
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">


        <div class="overlay"></div>
        <div class="search-overlay"></div>


        <!--  BEGIN CONTENT AREA  -->

            <div class="layout-px-spacing">

                @yield('content')

            </div>


        </div>
        <!--  END CONTENT AREA  -->


    </div>
    <!-- END MAIN CONTAINER -->



</body>
</html>
