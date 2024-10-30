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
        .consolidado-table {
            width: 100%;
            border-collapse: collapse;
            color: #666878;
            font-size: 11px;
            margin-bottom: 20px;
        }
        .consolidado-table th, .consolidado-table td {
            border: solid 1px #666878;
            padding: 5px 10px;
            width: 33%;
        }
        .consolidado-table th[colspan="3"] {
            background: black;
            color: white;
        }
        .fixed-bottom-table {
            width: 100%;
            background-color: white;
            margin-top: 20px;
        }
        .fixed-bottom-table th, .fixed-bottom-table td {
            border: solid 1px #666878;
            padding: 5px 10px;
            color: #666878;
            font-size: 11px;
            border-collapse: collapse;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body style="background-color: white !important; width:100%;">

@foreach ($consolidado as $cliente_data)
    <table class="table">
        <tbody class="">
            <tr>
                <td class="text-left" style="width: 50%; border:none;">
                    @if($datos_comercio->image != null)
                        <img width="100" class="rounded" src="{{ asset('storage/users/'.$datos_comercio->image) }}">
                    @else
                        <h5 class="inv-brand-name">{{ $datos_comercio->name }}</h5>
                    @endif
                </td>
                <td class="text-center" style="width: 50%; border:none;">
                    <table style="border:none;">
                        <tr style="border:none;">
                            <td class="text-left" style="width:55%;">
                                <p style="font-size:11px; margin-top:2px;"><b>FECHA: </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>TURNO: </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>REPARTIDOR: </b></p>
                            </td>
                            <td class="text-right" style="width:45%;">
                                <p style="font-size:11px;">{{ $datos_hoja_ruta->fecha }}</p>
                                <p style="font-size:11px;">{{ $datos_hoja_ruta->turno }}</p>
                                <p style="font-size:11px;">{{ $datos_hoja_ruta->nombre }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    
    <table style="border:none;">
        <tr style="border:none;">
            <td class="text-left" style="width:55%;">
                <p style="font-size:11px; margin-top:2px;"><b>CLIENTE: </b>{{ $cliente_data['cliente']->id_cliente }} - {{ $cliente_data['cliente']->nombre }}</p>
                <p style="font-size:11px; margin-top:2px;"><b>TELEFONO: </b>{{ $cliente_data['cliente']->telefono }}</p>
                <p style="font-size:11px; margin-top:2px;"><b>DIRECCION: </b>{{ $cliente_data['cliente']->direccion}}  {{$cliente_data['cliente']->altura}} {{$cliente_data['cliente']->piso}} {{$cliente_data['cliente']->depto }}</p>
                <p style="font-size:11px; margin-top:2px;"><b>CIUDAD: </b>{{ $cliente_data['cliente']->localidad }} {{ $cliente_data['cliente']->provincia }}</p>
            </td>
            <td class="text-right" style="width:45%;">
                <p style="font-size:11px; margin-top:2px;">&nbsp;</p>
            </td>
        </tr>
    </table> 

    @php
        $totalCantidad = 0;
    @endphp

    @foreach ($cliente_data['ventas'] as $venta_data)
        @php
            $subtotalCantidad = 0;
        @endphp

        <table class="consolidado-table">
            <thead>
                <tr>
                    <th colspan="3" style="background: black; color:white;"> Venta # {{ $venta_data['venta']->nro_venta }}</th>
                </tr>
                <tr>
                    <th>CODIGO</th>
                    <th>PRODUCTO</th>
                    <th>CANTIDAD</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta_data['detalles'] as $detalle)
                    <tr>
                        <td>{{ $detalle->product_barcode }}</td>
                        <td>{{ $detalle->product_name }}</td>
                        <td>{{ number_format($detalle->quantity, 0) }}</td>
                    </tr>
                    @php
                        $subtotalCantidad += $detalle->quantity;
                    @endphp
                @endforeach
            </tbody>
        </table>

        @php
            $totalCantidad += $subtotalCantidad;
        @endphp
    @endforeach

    <table class="consolidado-table">
        <tbody>
            <tr>
                <td colspan="2"><b>Total Cantidad del cliente</b></td>
                <td><b>{{ number_format($totalCantidad, 0) }}</b></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Firma -->
    <table class="fixed-bottom-table">
        <thead>
            <tr>
                <th>Firma de quien recibe</th>
                <th>Aclaracion</th>
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

    <div class="page-break"></div>
@endforeach

</body>
</html>
