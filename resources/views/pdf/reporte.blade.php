<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Reporte de Ventas</title>

	<!-- cargar a través de la url del sistema -->
	<!--
		<link rel="stylesheet" href="{{ asset('css/custom_pdf.css') }}">
		<link rel="stylesheet" href="{{ asset('css/custom_page.css') }}">
	-->
	<!-- ruta física relativa OS -->
	<link rel="stylesheet" href="{{ public_path('css/custom_pdf.css') }}">
	<link rel="stylesheet" href="{{ public_path('css/custom_page.css') }}">

</head>
<body>

	<section class="header" style="top: -287px;">
		<table cellpadding="0" cellspacing="0" width="100%">

			<tr>
				<td width="30%" style="vertical-align: top; padding-top: 10px; position: relative">
					<img width="150px" src="{{ asset('assets/img/livewire_logo.png') }}" alt="" class="invoice-logo">
				</td>

				<td width="70%" class="text-left text-company" style="vertical-align: top; padding-top: 10px">
					<span style="font-size: 16px"><strong>Reporte de Ventas</strong></span>
					<br>
					<span style="font-size: 16px"><strong>Fecha de Consulta: {{$dateFrom}} al {{$dateTo}}</strong></span>
					<br>
				</td>
			</tr>
		</table>
	</section>


	<section style="margin-top: -110px">
		<table cellpadding="0" cellspacing="0" class="table-items" width="100%">
			<thead>
				<tr>
					<th width="10%">FOLIO</th>
					<th width="12%">IMPORTE</th>
					<th width="10%">ITEMS</th>
					<th width="12%">METODO DE PAGO</th>
					<th>USUARIO</th>
					<th width="18%">FECHA</th>
				</tr>
			</thead>
			<tbody>
				@foreach($data as $item)
				<tr>
					<td align="center">{{$item->id}}</td>
					<td align="center">{{number_format($item->total,2)}}</td>
					<td align="center">{{$item->items}}</td>
					<td align="center">{{$item->metodo_pago}}</td>
					<td align="center">{{$item->user}}</td>
					<td align="center">{{$item->created_at}}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center">
						<span><b>TOTALES</b></span>
					</td>
					<td colspan="1" class="text-center">
						<span><strong>${{ number_format($data->sum('total'),2)}}</strong></span>
					</td>
					<td class="text-center">
						{{$data->sum('items')}}
					</td>
					<td colspan="3"></td>
				</tr>
			</tfoot>
		</table>
	</section>


	<section class="footer">

		<table cellpadding="0" cellspacing="0" class="table-items" width="100%">
			<tr>
				<td width="20%">
					<span>Sistema LWPOS v1</span>
				</td>
				<td width="60%" class="text-center">
					luisfax.com
				</td>
				<td class="text-center" width="20%">
					página <span class="pagenum"></span>
				</td>

			</tr>
		</table>
	</section>

</body>
</html>
