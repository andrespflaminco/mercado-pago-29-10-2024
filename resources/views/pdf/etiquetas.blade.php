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
    <link href="{{ asset('plugins/select2/select2.min.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/apps/invoice.css') }} " rel="stylesheet" type="text/css" />
</head>
<body style="background-color: white !important; width:100%;">

@php
    $c = 1;
@endphp

<table style="width: 100%; border-collapse: collapse;">
    @foreach($products as $p)
        @foreach($prod_elegidos as $de)
            @if($de->producto_id == $p->producto_id && $de->referencia_variacion == $p->referencia_variacion)
                <?php
                    $cantidad = ($productos_elegidos == '2') ? $de->cantidad : 1;
                ?>
                @for($i = 1; $i <= $cantidad; $i++)
                    @php
                        $nro_etiqueta = $c++;
                        $nro_etiqueta1 = $nro_etiqueta - 1;
                        $nro_etiqueta2 = $nro_etiqueta;
                        $barcode = $p->codigo_variacion ? $p->barcode . "/" . $p->codigo_variacion : $p->barcode;
                    @endphp

                    @if($nro_etiqueta1 % 3 == 0)
                        <tr>
                    @endif

                    <!--- ETIQUETA ---->
                    <td style="width: 33.3%; padding: 4px;" align="center" valign="top">
                        <div style="border: 1px solid black; border-radius: 5px; text-align: center; width: 6cm; height: 3.2cm; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
                            
                            <!-- Precio -->
                            @if($precio != 0)
                                <h1 style="margin: 0; margin-top: 5px; color: black; font-size: 20px;">${{$p->price}}</h1>
                            @endif

                            <!-- Nombre del producto ajustado -->
                            @if($nombre_producto != 0)
                            <div style="height: 1.4cm; display: flex; justify-content: center; align-items: center; margin-top:3px; padding: 0 6px;">
                                <h1 style="margin: 0; color: black; font-size: 19px; text-align: center; width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: normal; word-wrap: break-word; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    {{$p->name}} {{$p->variaciones}}
                                </h1>
                            </div>
                            @endif
                            
                            <!-- Código de barras y franja negra como footer fijo -->
                            @if($codigo_barra != 0 || $codigo != 0)
                                <div style="margin-top: auto; width: 100%; text-align: center; bottom: 0;">
                                    <!-- Código de barras -->
                                    @if($codigo_barra != 0)
                                        <div style="width: 100%; text-align: center;">
                                            <div style="margin: 0 auto; display: inline-block;">
                                                {!! DNS1D::getBarcodeHTML("$barcode", 'C128', 1, 15) !!}
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Código en la franja negra -->
                                    @if($codigo != 0)
                                        <div style="background: black; color: white; padding: 2px 0; font-size: 8px; width: 100%; text-align: center;">
                                            {{$barcode}}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </td>

                    @if($nro_etiqueta2 % 3 == 0)
                        </tr>
                    @endif

                @endfor 
            @endif
        @endforeach
    @endforeach
</table>

</body>
</html>
