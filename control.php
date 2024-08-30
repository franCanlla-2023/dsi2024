<?php
// archivo: registro_usuario.php

// Configuración de la base de datos
// Configuración de la base de datos utilizando las variables proporcionadas
$host = "127.0.0.1";
$port = "3306";
$dbname = "gimnasio";
$username = "root";
$password = ""; // Deja en blanco si no tienes contraseña

// Crear una nueva conexión a la base de datos
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$tipo_membresia = $_POST['tipo_membresia'];
$fecha_inicio = $_POST['fecha_inicio'];

// Determinar la duración de la membresía y aplicar descuento si es necesario
$duracion_meses = 0;
$descuento_aplicado = 0.00;

switch ($tipo_membresia) {
    case 'diario':
        $duracion_meses = 0; // Membresía diaria no tiene duración en meses
        break;
    case 'mensual':
        $duracion_meses = 1;
        break;
    case 'trimestral':
        $duracion_meses = 3;
        break;
    case 'semestral':
        $duracion_meses = 6;
        break;
}

// Aplicar descuento para membresías de más de 2 meses
if ($duracion_meses > 2) {
    // Aquí puedes definir un porcentaje de descuento, por ejemplo 10%
    $descuento_aplicado = 10.00;
}

// Calcular la fecha de finalización de la membresía
$fecha_fin = date('Y-m-d', strtotime($fecha_inicio . " + $duracion_meses months"));

// Insertar los datos del usuario en la base de datos
$sql_usuario = "INSERT INTO usuarios (nombre, email, telefono, fecha_registro) VALUES (?, ?, ?, NOW())";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("sss", $nombre, $email, $telefono);
$stmt_usuario->execute();

// Obtener el ID del usuario recién registrado
$id_usuario = $stmt_usuario->insert_id;

// Insertar los datos de la membresía en la base de datos
$sql_membresia = "INSERT INTO membresias (id_usuario, tipo_membresia, fecha_inicio, fecha_fin, descuento_aplicado) VALUES (?, ?, ?, ?, ?)";
$stmt_membresia = $conn->prepare($sql_membresia);
$stmt_membresia->bind_param("isssd", $id_usuario, $tipo_membresia, $fecha_inicio, $fecha_fin, $descuento_aplicado);
$stmt_membresia->execute();

// Cerrar la conexión
$stmt_usuario->close();
$stmt_membresia->close();
$conn->close();

// Redirigir al usuario a una página de éxito
header("Location: exito.html");
exit();
?>
