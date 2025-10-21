<?php
$host = "localhost";
$usuario = "root"; // cambia si tienes otro usuario
$password = "12345678";    // coloca tu contraseña si tiene
$bd = "coffe";     // ✅ nombre correcto de tu base de datos

$conexion = new mysqli($host, $usuario, $password, $bd);

if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}
?>
