<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $asunto = $_POST["asunto"];
    $mensaje = $_POST["mensaje"];

    $sql = "INSERT INTO contacto (nombre, email, telefono, asunto, mensaje)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $email, $telefono, $asunto, $mensaje);

    if ($stmt->execute()) {
        echo "<script>alert('Mensaje enviado correctamente. ¡Gracias por contactarnos!'); window.location='contacto.html';</script>";
    } else {
        echo "<script>alert('Error al enviar el mensaje.'); window.location='contacto.html';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
