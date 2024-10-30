<!DOCTYPE html>
<html lang="es">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="{{ asset('assets/js/loader.js') }}"></script> 
    <link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/structure.css') }}" rel="stylesheet" type="text/css" class="structure" />

    <link href="{{ asset('assets/css/elements/avatar.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/widgets/modules-widgets.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">

    <link href="{{ asset('assets/css/apps/scrumboard.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/apps/notes.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/scrollspyNav.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/components/tabs-accordian/custom-accordions.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/select2/select2.min.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/apps/invoice.css') }} " rel="stylesheet" type="text/css" />

    <style>
        .fixed-bottom-table {
            position: fixed;
            bottom: 55px;
            width: 100%;
            background-color: white;
        }
        .fixed-bottom-table th, .fixed-bottom-table td {
            border: solid 1px #666878;
            padding: 5px 10px;
            color: #666878;
            font-size: 11px;
            border-collapse: collapse;
        }
    </style>
</head>
<body style="background-color: white !important; width:100%;">

    <table class="table">
        <tbody class="">
            <tr>
                <td class="text-center" style="width: 100%; border:none;">
                    <table style="border:none;">
                        <tr style="border:none;">
                            <td class="text-left" style="width:55%;">
                                <p style="font-size:11px; margin-top:2px;"><b>NRO CLIENTE: </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>CLIENTE: </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>FECHAS: </b></p>
                            </td>
                            <td class="text-right" style="width:45%;">
                                <p style="font-size:11px; margin-top:2px;">{{$datos_cliente->id_cliente}}</p>
                                <p style="font-size:11px; margin-top:2px;">{{$datos_cliente->nombre}}</p>
                                <p style="font-size:11px;"> DESDE {{\Carbon\Carbon::parse($from)->format('d/m/Y')}} HASTA {{\Carbon\Carbon::parse($to)->format('d/m/Y')}}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

<table style="width: 100%; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">TIPO DE MOVIMIENTO</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">FECHA</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse; min-width: 90px !important;">VENTA</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse; min-width: 90px !important;">PAGO</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">ASOCIADO A</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">MEDIO DE PAGO</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">SUCURSAL</th>
        </tr>
    </thead>
    <tbody>
        @php
        $total_ventas = 0;
        $total_pagos = 0;
        @endphp

        @foreach($compras_clientes as $compra)
            @if($compra->monto > 0 || $compra->monto_pago > 0 || $compra->monto_saldo)
            <tr>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
                    @if($compra->monto > 0)
                        Venta # {{$compra->nro_venta}}
                    @endif

                    @if($compra->monto_pago > 0)
                        Pago
                    @endif

                    @if($compra->monto_saldo > 0)
                        Saldo inicial
                    @endif

                    @if($compra->monto_saldo < 0)
                        Pago de saldo inicial
                    @endif
                </td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
                    {{\Carbon\Carbon::parse($compra->created_at)->format('d/m/Y')}}
                </td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
                    @if($compra->monto > 0)
                        $ {{number_format($compra->monto,2,",",".") }}
                        @php
                        $total_ventas += $compra->monto;
                        @endphp
                    @endif

                    @if($compra->monto_saldo > 0)
                        $ {{number_format($compra->monto_saldo,2,",",".") }}
                        @php
                        $total_ventas += $compra->monto_saldo;
                        @endphp
                    @endif
                </td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
                    @if($compra->monto_pago > 0)
                        $ {{number_format($compra->monto_pago,2,",",".") }}
                        @php
                        $total_pagos += $compra->monto_pago;
                        @endphp
                    @endif
                    @if($compra->monto_saldo < 0)
                        $ {{number_format($compra->monto_saldo*-1,2,",",".") }}
                        @php
                        $total_pagos += $compra->monto_saldo*-1;
                        @endphp
                    @endif
                </td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
                    @if($compra->id_pago > 0)
                        Venta # {{$compra->nro_venta}}
                    @endif
                </td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
                    {{$compra->nombre_banco}}
                </td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
                    @if($compra->nombre_sucursal != null) {{ $compra->nombre_sucursal }} @else Casa central @endif
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">TOTALES</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">$ {{ number_format($total_ventas,2,",",".") }}</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">$ {{ number_format($total_pagos,2,",",".") }}</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
        </tr>
        <tr>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">@if($total_ventas < $total_pagos) Saldo acreedor @else Saldo deudor @endif</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
            <th colspan="2" style="text-align: center; padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">$ {{ number_format($total_ventas - $total_pagos,2,",",".") }}</th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
            <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></th>
        </tr>
    </tfoot>
</table>


    <!-- Tabla fija en la parte inferior para firma, aclaración y DNI -->
    <table hidden class="fixed-bottom-table">
        <thead>
            <tr>
                <th>Firma de quien controla</th>
                <th>Aclaración</th>
                <th>DNI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="height: 50px;"></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>

