<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2><strong> Error de importacion </strong></h2>
    <p>Datos del usuario:</p>
    <ul>
        <li>ID: {{ $user->id }}</li>
        <li>Nombre: {{ $user->name }}</li>
        <li>Email: {{ $user->email }}</li>
        <li>Telefono: {{ $user->phone }}</li>
        <li>Fecha de creacion: {{ $created_at }}</li>
        
        
    </ul>
    
    <p>Error:</p>
    <ul>
        <li>ID: {{ $e }}</li>
        
    </ul>
</body>
</html>
