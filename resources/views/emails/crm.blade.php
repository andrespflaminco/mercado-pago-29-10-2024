
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body>
    <ul>
        <li>ID FLAMINCO: {{ $user->id }}</li>
        <li>NOMBRE: {{ $user->nombre_usuario }}</li>
        <li>APELLIDO: {{ $user->apellido_usuario }}</li>
        <li>EMAIL: {{ $user->email }}</li>
        <li>TELEFONO: {{ $user->prefijo_pais . $user->phone }}</li>
        <li>PROVINCIA: </li>
        <li>CUIDAD: </li>
        <li>NOMBRE DE LA EMPRESA: {{ $user->name }}</li>
        <li>RUBRO: {{ $user->rubro }}</li>
        <li>CANTIDAD SUCURSALES: {{ $user->cantidad_sucursales }}</li>
        <li>CANTIDAD EMPLEADOS: {{ $user->cantidad_empleados }}</li>
        <li>PLAN: 
        @php
            $plan = '';
            if ($user->cantidad_sucursales == '1') {
                $plan = 'Emprendedor';
            } elseif ($user->cantidad_sucursales == '2 - 4' || $user->cantidad_empleados == '2 - 6') {
                $plan = 'Pequeñas Empresas';
            } elseif ($user->cantidad_sucursales == '5 - 9' || $user->cantidad_empleados == '7 - 11') {
                $plan = 'Medianas Empresas';
            } elseif ($user->cantidad_sucursales == '10 - 24' || $user->cantidad_empleados == '+ 30' || $user->cantidad_empleados == '12 - 30' || $user->cantidad_sucursales == '+ 25' ) {
                $plan = 'Grandes Empresas';
            }
        @endphp
        {{ $plan }}
        </li>
        <li>FECHA DE CREACION: {{ $created_at }}</li>
        <li>FECHA DE VENCIMIENTO PRUEBA: {{ $user->prueba_hasta }}</li>
        <li>CANTIDAD DE LOGUEOS: {{ $user->cantidad_login }}</li>
        <li>ULTIMO LOGUEO: {{ $user->last_login }}</li>
        <li> VALIDA MAIL: @if($user->email_verified_at != null) "SI" @else "NO" @endif</li>
        <li>FECHA VALIDA MAIL: {{ $user->email_verified_at }}</li>
        <li>ESTADO PAGO: @if($user->email_verified_at != null) "SI" @else "NO" @endif  </li>
        <li> FECHA PAGO: {{ $user->confirmed_at }} </li>

    </ul>
</body>
</html>
