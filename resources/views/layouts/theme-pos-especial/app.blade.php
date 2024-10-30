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
    @include('layouts.theme-pos.styles')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    
    <link href="{{ asset('plugins/pricing-table/css/component.css') }}" rel="stylesheet" type="text/css" />

<meta charset="utf-8">

</head>
<body>
    

    <!-- BEGIN LOADER -->
   <div wire:loading id="global-loader" >
			<div class="whirly-loader"> </div>
	</div>
    <!--  END LOADER -->
	<!-- Main Wrapper -->
    <div class="main-wrapper">
    

    <!--  BEGIN MAIN CONTAINER  -->

	<div class="content" id="content" >
    <!--  BEGIN CONTENT AREA  -->
        
                @yield('content')

    
        </div>
        <!--  END CONTENT AREA  -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    @include('layouts.theme-pos.scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->



</body>
</html>
