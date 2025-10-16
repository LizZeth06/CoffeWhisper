<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $rol = $_POST['rol'];

    if (!empty($contraseña)) {
        $sql = "UPDATE usuarios 
                SET nombre='$nombre', correo='$correo', contraseña='$contraseña', rol='$rol' 
                WHERE id_usuario=$id";
    } else {
        $sql = "UPDATE usuarios 
                SET nombre='$nombre', correo='$correo', rol='$rol' 
                WHERE id_usuario=$id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: usuarios.php?status=actualizado");
    } else {
        header("Location: usuarios.php?status=error");
    }
}
?>
