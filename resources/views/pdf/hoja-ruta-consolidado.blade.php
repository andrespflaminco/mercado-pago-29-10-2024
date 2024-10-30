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
                                <p style="font-size:11px; margin-top:2px;"><b>CONSOLIDADO DE CARGA </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>NRO HOJA RUTA: </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>FECHA: </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>TURNO: </b></p>
                                <p style="font-size:11px; margin-top:2px;"><b>REPARTIDOR: </b></p>
                            </td>
                            <td class="text-right" style="width:45%;">
                                <p style="font-size:11px; margin-top:2px;">-</p>
                                <p style="font-size:11px;">{{ $datos_hoja_ruta->nro_hoja }}</p>
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

    <table style="width: 100%; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">CODIGO</th>
                <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">PRODUCTO</th>
                <th style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">CANTIDAD</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($consolidado as $c)
                @php $total += $c->cantidad; @endphp
                <tr>
                    <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">{{ $c->product_barcode }}</td>
                    <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">{{ $c->product_name }}</td>
                    <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">{{ number_format($c->cantidad, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">Total</td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;"></td>
                <td style="padding: 5px 10px; border:solid 1px; color:#666878; font-size: 11px; border-collapse: collapse;">{{ $total }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tabla fija en la parte inferior para firma, aclaración y DNI -->
    <table class="fixed-bottom-table">
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

