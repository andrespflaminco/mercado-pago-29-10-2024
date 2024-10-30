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
<body style="background-color: white !important;">
    <div style="background-color: white !important; width:100%;">
        <div style="background-color: white !important;" class="doc-container">
            <div style="background-color: white !important;" class="invoice-container">
                <div style="background-color: white !important;" class="invoice-inbox">
                    <div style="background-color: white !important;" id="seleccion" class="invoice">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr style="border-bottom:none;">
                                            <td style="width: 40%;" class="text-left">
                                                <div class="company-info">
                                                    @foreach ($usuario as $u)
                                                        @if($u->image != null)
                                                            <img width="100" class="rounded" src="{{ asset('storage/users/'.$u->image) }}">
                                                        @else
                                                            <h5 class="inv-brand-name">{{ $u->name }}</h5>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td style="border:solid 1px #c8c8c8; padding: 0px 20px 0px 25px;">
                                                <h1><b>X</b></h1>
                                            </td>
                                            <td class="text-right" style="width: 45%;">
                                                <h5 class="in-heading">REMITO MOVIMIENTO STOCK</h5>
                                                <p>Realizado por: {{$usuario_realizador->name}}</p>
                                                <p>{{ \Carbon\Carbon::parse($movimiento->created_at)->format('d-m-Y H:i:s') }}</p>
                                                
                                            </td>
                                        </tr>
                                        <tr style="margin-top: 15%; border:none;">
                                            <td style="width: 45%; border:none;" class="text-left">
                                            </td>
                                            <td style="padding: 0px 20px 0px 25px;"></td>
                                            <td class="text-right" style="width: 45%; border:none;">
                                                
                                            </td>
                                        </tr>
                                        <br><br>
                                        <tr style="border:none; margin-top:25px;">
                                            <td style="width: 45%;" class="text-left">
                                                    <p style="font-size:12px;"> <b>Sucursal Origen.</b> </p>
                                                    <p style="font-size:11px;"> <b>{{$detalle_origen->name}}</b> </p>
                                                    <p style="font-size:11px;">Email: {{$detalle_origen->email}}</p>
                                                    <p style="font-size:11px;"> Tel: {{$detalle_origen->phone}} </p>
                                            </td>
                                            <td style="padding: 0px 20px 0px 20px;"></td>
                                            <td style="width: 45%;" class="text-left">
                                                    <p style="font-size:12px;"> <b>Sucursal Destino.</b> </p>
                                                    <p style="font-size:11px;"> <b>{{$detalle_destino->name}}</b> </p>
                                                    <p style="font-size:11px;">Email: {{$detalle_destino->email}} </p>
                                                    <p style="font-size:11px;"> Tel: {{$detalle_destino->phone}}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row inv--product-table-section">
                            <div style="width:100%;">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Fila</th>
                                                <th scope="col">Codigo</th>
                                                <th scope="col">Producto</th>
                                                <th class="text-right" scope="col">Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1; $total_cantidad = 0; ?>
                                            @foreach($detalle_venta as $item)
                                                <?php $total_cantidad += $item->cantidad; ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td>{{ $item->product_barcode }}</td>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td class="text-right">{{ $item->cantidad }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row inv--detail-section">
							<div class="col-sm-7 align-self-center">
								<div class="col-sm-12 col-12">
									<h6 class=" inv-title">Informacion adicional:</h6>
								</div>
									@if ($movimiento->observaciones != '')
										<div class="col-sm-4 col-12">
											<p style="font-size:11px;">Observaciones: </p>
										</div>
										<div class="col-sm-8 col-12">
										<p style="font-size:11px;">{{$movimiento->observaciones}}</p>
									    </div>
									@else
									<div class="col-sm-4 col-12">
									<p style="font-size:11px;"> </p>
									</div>
                            		@endif
																																
							</div>
                            
                            <div style="float: right !important; text-align: right !important;" class="col-sm-5 text-right order-2">
                            	<div class="col-sm-11 col-11">
                                    <h5>Cantidad total: {{$total_cantidad}}</h5>
                                </div>
        					</div>
         				</div>
						
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabla fija en la parte inferior para firma, aclaraci贸n y DNI -->
    <table class="fixed-bottom-table">
        <thead>
            <tr>
                <th>Firma de quien controla</th>
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
</body>
</html>


