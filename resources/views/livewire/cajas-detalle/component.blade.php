<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>SISTEMA PDV - Flaminco </title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
        
    <style>
        table.dataTable > thead .sorting:before, table.dataTable > thead .sorting_asc:before {
            display:none !important;
        }
        table.dataTable > thead .sorting:after, table.dataTable > thead .sorting_asc:after {
            display:none !important;    
        }
        .buttons-excel{
            display: block !important;  
        }
        .customizer-links{
            display:none !important;
        }

    </style>
    
    <!-- Toastr CSS -->		
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toatr.css') }}">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    
    <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('assets/pos/assets/plugins/icons/feather/feather.css') }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/pos/css/bootstrap.min.css') }}" >
    
    <!-- Animation CSS -->
    <link rel="stylesheet" href="{{ asset('assets/pos/css/animate.css') }}" >
    
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/pos/plugins/fontawesome/css/fontawesome.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('assets/pos/plugins/fontawesome/css/all.min.css') }}" >
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/pos/css/style.css') }}" href="">
    
    @livewireStyles
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
</head>
<body>
    <!-- BEGIN LOADER -->
    <div wire:loading id="global-loader">
        <div class="whirly-loader"> </div>
    </div>
    <!-- END LOADER -->
    
    <!-- Main Wrapper -->
    <div class="main-wrapper">
    
        <!-- BEGIN NAVBAR  -->
        @include('layouts.theme-pos.header')
        <!-- END NAVBAR  -->
        
        <!-- BEGIN MAIN CONTAINER  -->
        
        <!-- BEGIN SIDEBAR  -->
        @include('layouts.theme-pos.sidebar')
        <!-- END SIDEBAR  -->
        
        <div class="page-wrapper" id="container">
            <div class="content" id="content">
                <!-- BEGIN CONTENT AREA  -->
                
                <!-- /product list -->
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="animateLineContent-4">
                            <div class="tab-pane fade show active" id="animated-underline-home" role="tabpanel" aria-labelledby="animated-underline-home-tab">
                                <div class="widget-heading">
                                    <h4 class="card-title"> DETALLE DE CAJA </h4>
                                </div>
                                <a class="btn btn-dark" href="{{ url('report/excel-cajas' . '/' . $caja_elegida  . '/'. uniqid() ) }}" target="_blank">Exportar a Excel</a>
                      
                                <div class="widget-content">
                                    <div class="row">
                                        <div class="container">
                                            <table id="miTabla" class="display">
                                                <thead>
                                                    <tr>
                                                        <th>OPERACION</th>
                                                        <th>NRO OPERACION</th>
                                                        <th>DETALLE</th>
                                                        <th>FECHA</th>
                                                        <th>CUENTA</th>
                                                        <th>FORMA DE PAGO</th>
                                                        <th>USUARIO</th>
                                                        <th>MONTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($data as $metodo)
                                                    <tr>
                                                        <td>
                                                            @if($metodo->id_factura != 0)
                                                                @foreach($ventas as $v)
                                                                    @if($v->id == $metodo->id_factura)
                                                                        VENTA
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                            
                                                            @if($metodo->id_gasto != 0)
                                                                GASTO
                                                            @endif
                                                            
                                                            @if($metodo->id_compra != 0)
                                                                COMPRA
                                                            @endif
                                                            
                                                            @if($metodo->id_ingresos_retiros != null)
                                                                @foreach($ingresos_retiros as $ir)
                                                                    @if($ir->id == $metodo->id_ingresos_retiros)
                                                                        {{ $ir->tipo }}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        
                                                        <td>
                                                            @if($metodo->id_factura != 0)
                                                                @foreach($ventas as $v)
                                                                    @if($v->id == $metodo->id_factura)
                                                                        {{ $v->nro_venta }}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                            
                                                            @if($metodo->id_gasto != 0)
                                                                {{ $metodo->id_gasto }}
                                                            @endif
                                                            
                                                            @if($metodo->id_compra != 0)
                                                                {{ $metodo->id_compra }}
                                                            @endif
                                                            
                                                            @if($metodo->id_ingresos_retiros != null)
                                                                @foreach($ingresos_retiros as $ir)
                                                                    @if($ir->id == $metodo->id_ingresos_retiros)
                                                                        {{ $metodo->id_ingresos_retiros }}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        
                                                        <td>
                                                            @if($metodo->id_factura != 0)
                                                                @foreach($ventas as $v)
                                                                    @if($v->id == $metodo->id_factura)
                                                                        {{ $v->nombre }}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                            
                                                            @if($metodo->id_compra != 0)
                                                                @foreach($compras as $c)
                                                                    @if($c->id == $metodo->id_compra)
                                                                        {{ $c->nombre }}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                            
                                                            @if($metodo->id_gasto != 0)
                                                                @foreach($gastos as $g)
                                                                    @if($g->id == $metodo->id_gasto)
                                                                        {{ $g->nombre }}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        
                                                        <td>
                                                            {{ \Carbon\Carbon::parse( $metodo->created_at)->format('d-m-Y H:i') }}
                                                        </td>
                                                        
                                                        <td>
                                                            {{ $metodo->nombre_banco }}
                                                        </td>
                                                        
                                                        <td>
                                                            {{ $metodo->metodo_pago }}
                                                        </td>
                                                        
                                                        <td>
                                                            {{ $metodo->name }}
                                                        </td>
                                                        
                                                        <td>
                                                            @if($metodo->id_factura != 0)
                                                                {{ number_format($metodo->monto + $metodo->recargo + $metodo->iva_pago + $metodo->iva_recargo, 2) }}
                                                            @endif
                                                            
                                                            @if($metodo->id_gasto != 0)
                                                                {{ number_format(-1 * $metodo->monto_gasto, 2) }}
                                                            @endif
                                                            
                                                            @if($metodo->id_compra != 0)
                                                                {{ number_format(-1 * $metodo->monto_compra, 2) }}
                                                            @endif
                                                            
                                                            @if($metodo->id_ingresos_retiros != null)
                                                                @foreach($ingresos_retiros as $ir)
                                                                    @if($ir->id == $metodo->id_ingresos_retiros)
                                                                        {{ number_format($ir->monto, 2) }}
                                                                    @endif
                                                                @endforeach
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
                    </div>
                </div>
                
                <!--  END CONTENT AREA  -->
            </div>
        </div>
        
        <!-- jQuery -->
        <script src="{{ asset('assets/pos/js/jquery-3.6.0.min.js') }}"></script>
        
        <!-- Feather Icon JS -->
        <script src="{{ asset('assets/pos/js/feather.min.js') }}"></script>
        
        <!-- Slimscroll JS -->
        <script src="{{ asset('assets/pos/js/jquery.slimscroll.min.js') }}"></script>
        
        <!-- Bootstrap Core JS -->
        <script src="{{ asset('assets/pos/js/bootstrap.bundle.min.js') }}"></script>
        
        <!-- Custom JS -->
        <script src="{{ asset('assets/pos/js/script.js') }}"></script>
        
        <!-- Toastr JS -->
        <script src="{{ asset('plugins/notification/snackbar/snackbar.min.js') }}"></script>
        
        
        <!-- DataTables y Buttons -->
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
        
        <!-- Script para inicializar DataTables y agregar botones de exportaciÃ³n -->
<script>
    $(document).ready(function() {
        var table = $('#miTabla').DataTable({
            "pageLength": -1,
            "paging": false,
            "info": false,
            "order": [[3, 'desc']], // Ordena la columna 3 (¨ªndice 2) de forma descendente
            "language": {
                "search": "Buscar:"
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });

        // Cambia el selector para apuntar al bot¨®n correcto
        $('#exportarExcel').on('click', function() {
            table.buttons('.buttons-excel').trigger();
        });
    });
</script>

        
        @livewireScripts
        
        <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
        <!-- Script adicional si es necesario -->
    </body>
</html>

