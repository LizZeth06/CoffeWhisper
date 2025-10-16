<?php
include("conexion.php");
session_start();

// --- Paginación dinámica ---
$registros_por_pagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 0;
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

// Si el usuario pone 0 o 'todos', no limitamos resultados
if ($registros_por_pagina === 0) {
  $limit_sql = "";
} else {
  $offset = ($pagina_actual - 1) * $registros_por_pagina;
  $limit_sql = "LIMIT $registros_por_pagina OFFSET $offset";
}

// Contar total de registros
$total_registros_row = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM usuarios"));
$total_registros = $total_registros_row ? (int)$total_registros_row[0] : 0;

// Calcular total de páginas (solo si hay límite)
$total_paginas = ($registros_por_pagina === 0) ? 1 : max(1, (int)ceil($total_registros / max(1, $registros_por_pagina)));

// Si la página actual excede el total, ajustarla
if ($pagina_actual > $total_paginas) {                            
  $pagina_actual = $total_paginas;
  if ($registros_por_pagina !== 0) {
    $offset = ($pagina_actual - 1) * $registros_por_pagina;
    $limit_sql = "LIMIT $registros_por_pagina OFFSET $offset";
  }
}

// Consulta con LIMIT dinámico (esta será la única consulta que debes usar)
$sql = "SELECT * FROM usuarios $limit_sql";
$resultado = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Coffee Whisper - Usuarios</title>
  <link rel="stylesheet" href="estiloscoffe.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>

//Mensajes con sweetAlert
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get('status');

  if (status === 'guardado') {
    Swal.fire('¡Éxito!', 'Usuario guardado correctamente.', 'success');
  } else if (status === 'actualizado') {
    Swal.fire('¡Éxito!', 'Usuario actualizado correctamente.', 'success');
  } else if (status === 'eliminado') {
    Swal.fire('¡Éxito!', 'Usuario eliminado correctamente.', 'success');
  } else if (status === 'error') {
    Swal.fire('Error', 'Ocurrió un problema al procesar la solicitud.', 'error');
  }
});
</script>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo"><h2>Coffee Whisper</h2></div>
    <div class="profile">
      <img src="img/admin.png" alt="Admin" class="avatar">
      <p>Administrador</p>
    </div>
    <nav>
      <a href="principal.php"><i class="fa fa-home"></i> Dashboard</a>
      <a href="productos.php"><i class="fa fa-box"></i> Productos</a>
      <a href="pedidos.php"><i class="fa fa-shopping-cart"></i> Pedidos</a>
      <a href="usuarios.php" class="active"><i class="fa fa-users"></i> Usuarios</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main-content">
    <header class="topbar">
      <div class="actions"><i class="fa fa-bell"></i> <button class="logout">Log out</button></div>
    </header>

    <section class="content-header"><h2>Usuarios</h2></section>
    <!-- ⚪Contenedor blanco -->
    <div class="content-box">
      <div class="content-actions">
        <button class="btn-nuevo" id="btnAbrirModal">Nuevo</button>
        <div class="buscador">
          <input type="text" id="buscar" placeholder="Buscar usuario...">
          <i class="fa fa-search"></i>
        </div>
      </div>

      <!--Agrega un selector para elegir cuántos registros mostrar | Selector de límite -->
      <div class="selector-limite">
        <label>Mostrar:</label>
        <select id="limiteSelect" onchange="cambiarLimite(this.value)">
          <option value="3" <?= $registros_por_pagina===3?'selected':''; ?>>3</option>
          <option value="5" <?= $registros_por_pagina===5?'selected':''; ?>>5</option>
          <option value="7" <?= $registros_por_pagina===7?'selected':''; ?>>7</option>
          <option value="10" <?= $registros_por_pagina===10?'selected':''; ?>>10</option>
          <option value="0" <?= $registros_por_pagina===0?'selected':''; ?>>Todos</option>
        </select>
        <span>registros por página</span>
      </div>

      <table class="tabla-productos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Fecha de Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Usa el resultado ya obtenido arriba (con LIMIT)
          while ($fila = mysqli_fetch_assoc($resultado)) {
            // escapar para seguridad básica en salida
            $id = (int)$fila['id_usuario'];
            $nombre = htmlspecialchars($fila['nombre'], ENT_QUOTES);
            $correo = htmlspecialchars($fila['correo'], ENT_QUOTES);
            $rol = htmlspecialchars($fila['rol'], ENT_QUOTES);
            $fecha = htmlspecialchars($fila['fecha_registro'], ENT_QUOTES);

            echo "<tr data-id='{$id}' data-nombre='{$nombre}' data-correo='{$correo}' data-rol='{$rol}'>
                    <td>{$id}</td>
                    <td>{$nombre}</td>
                    <td>{$correo}</td>
                    <td>{$rol}</td>
                    <td>{$fecha}</td>
                    <td class='acciones'>
                      <button class='btn-editar'><i class='fa fa-edit'></i></button>
                      <button class='btn-eliminar'><i class='fa fa-trash'></i></button>
                    </td>
                  </tr>";
          }
          ?>
        </tbody>
      </table>

      <!-- Agregación la paginación visual (botones) --> 
      <?php if ($registros_por_pagina !== 0): /* no mostrar paginación si "Todos" */ ?>
      <div class="paginacion">
        <?php if ($pagina_actual > 1): ?>
          <a href="?pagina=<?= $pagina_actual - 1 ?>&limite=<?= $registros_por_pagina ?>">Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <a href="?pagina=<?= $i ?>&limite=<?= $registros_por_pagina ?>" class="<?= $i == $pagina_actual ? 'activo' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($pagina_actual < $total_paginas): ?>
          <a href="?pagina=<?= $pagina_actual + 1 ?>&limite=<?= $registros_por_pagina ?>">Siguiente</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>

    </div>
  </main>

  <!-- MODAL NUEVO / EDITAR -->
  <div id="modalUsuario" class="modal">
    <div class="modal-content">
      <span class="cerrar">&times;</span>
      <h2 id="modalTitulo">Nuevo Usuario</h2>
      <form id="formUsuario" action="guardar_usuario.php" method="POST">
        <input type="hidden" name="id_usuario" id="id_usuario">
        <label>Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        <label>Correo:</label>
        <input type="email" name="correo" id="correo" required>
        <label>Contraseña:</label>
        <input type="password" name="contraseña" id="contraseña" placeholder="(Solo si es nuevo o se desea cambiar)">
        <label>Rol:</label>
        <select name="rol" id="rol" required>
          <option value="">Selecciona un rol</option>
          <option value="cliente">Cliente</option>
          <option value="admin">Administrador</option>
        </select>
        <div class="modal-buttons">
          <button type="submit" class="guardar">Guardar</button>
          <button type="button" class="cancelar" id="cerrarModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
<!-- SCRIPTS -->
<script>
  const modal = document.getElementById("modalUsuario");
  const btnAbrir = document.getElementById("btnAbrirModal");
  const btnCerrar = document.getElementById("cerrarModal");
  const spanCerrar = document.querySelector(".cerrar");
  const form = document.getElementById("formUsuario");
  const titulo = document.getElementById("modalTitulo");

  // Abrir modal para nuevo usuario
  btnAbrir.onclick = () => {
    form.reset();
    document.getElementById("id_usuario").value = "";
    titulo.textContent = "Nuevo Usuario";
    form.action = "guardar_usuario.php";
    modal.style.display = "block";
  };

  // Cerrar modal
  [btnCerrar, spanCerrar].forEach(btn => btn.onclick = () => modal.style.display = "none");
  window.onclick = e => { if (e.target == modal) modal.style.display = "none"; };

  // Buscador | Buscador (filtra filas visibles)
  const inputBuscar = document.getElementById("buscar");
  const filas = document.querySelectorAll(".tabla-productos tbody tr");
  inputBuscar.addEventListener("keyup", () => {
    const texto = inputBuscar.value.toLowerCase();
    filas.forEach(fila => {
      fila.style.display = fila.textContent.toLowerCase().includes(texto) ? "" : "none";
    });
  });

  // Editar usuario
  document.querySelectorAll(".btn-editar").forEach(btn => {
    btn.addEventListener("click", () => {
      const fila = btn.closest("tr");
      document.getElementById("id_usuario").value = fila.dataset.id;
      document.getElementById("nombre").value = fila.dataset.nombre;
      document.getElementById("correo").value = fila.dataset.correo;
      document.getElementById("rol").value = fila.dataset.rol;
      document.getElementById("contraseña").value = "";
      titulo.textContent = "Editar Usuario";
      form.action = "actualizar_usuario.php";
      modal.style.display = "block";
    });
  });

  // Eliminar usuario con SweetAlert
  document.querySelectorAll(".btn-eliminar").forEach(btn => {
    btn.addEventListener("click", () => {
      const fila = btn.closest("tr");
      const id = fila.dataset.id;
      Swal.fire({
        title: "¿Eliminar usuario?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#e04b4b",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "eliminar_usuario.php?id=" + id;
        }
      });
    });
  });

  //Función para recargar la página | Cambiar límite (recarga con parámetros)
  function cambiarLimite(valor) {
    const url = new URL(window.location.href);
    url.searchParams.set('limite', valor);
    url.searchParams.set('pagina', 1);
    window.location.href = url.toString();
  }
</script>

</body>
</html>
