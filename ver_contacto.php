<?php
include("conexion.php");

$resultado = $conexion->query("SELECT * FROM contacto ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mensajes Recibidos</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; margin: 40px; }
    table { border-collapse: collapse; width: 100%; background: #fff; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #4b2e2a; color: #fff; }
  </style>
</head>
<body>
  <h2>Mensajes Recibidos</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Email</th>
      <th>Teléfono</th>
      <th>Asunto</th>
      <th>Mensaje</th>
      <th>Fecha</th>
    </tr>
    <?php while($fila = $resultado->fetch_assoc()) { ?>
      <tr>
        <td><?= $fila["id"] ?></td>
        <td><?= $fila["nombre"] ?></td>
        <td><?= $fila["email"] ?></td>
        <td><?= $fila["telefono"] ?></td>
        <td><?= $fila["asunto"] ?></td>
        <td><?= $fila["mensaje"] ?></td>
        <td><?= $fila["fecha"] ?></td>
      </tr>
    <?php } ?>
  </table>
</body>
</html>
