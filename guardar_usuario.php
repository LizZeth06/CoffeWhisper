<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol)
            VALUES ('$nombre', '$correo', '$contraseña', '$rol')";

    if (mysqli_query($conn, $sql)) {
        header("Location: usuarios.php?status=guardado");
    } else {
        header("Location: usuarios.php?status=error");
    }
}
?>
