<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM usuarios WHERE id_usuario=$id";
    if (mysqli_query($conn, $sql)) {
        header("Location: usuarios.php?status=eliminado");
    } else {
        header("Location: usuarios.php?status=error");
    }
}
?>

