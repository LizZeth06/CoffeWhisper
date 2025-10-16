<?php 
include("conexion.php"); // Asegúrate de tener la conexión a tu base de datos aquí

// Consultar productos de la base de datos
$sql = "SELECT p.id_producto, p.nombre_producto, p.descripcion, p.precio, p.imagen, c.nombre_categoria 
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Coffe Whisper - Productos</title>
  <link rel="stylesheet" href="estiloscoffe.css">
  <!-- Iconos de fontawesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">
     <!--  <i class=""></i> -->
      <h2>Coffe Whisper</h2>
    </div>
    <div class="profile">
      <img src="img/admin.png" alt="Admin" class="avatar">
      <p>Administrador</p>
    </div>
    <nav>
      <a href="principal.php" ><i class="fa fa-home"></i> Dashboard</a>
      <a href="productos.php"><i class="fa fa-box"></i> Productos</a>
      <a href="pedidos.php"><i class="fa fa-shopping-cart"></i> Pedidos</a>
      <a href="usuarios.php"><i class="fa fa-users"></i> Usuarios</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main-content">
    <!-- Topbar -->
    <header class="topbar">
     <!-- <h3>Dashboard</h3>-->
     <!-- <div class="search">-->
       <!-- <input type="text" placeholder="Buscar...">-->
       <!-- <i class="fa fa-search"></i>
      </div>-->
      <div class="actions">
        <i class="fa fa-bell"></i>
        <button class="logout">Log out</button>
      </div>
    </header>
    <!-- Contenido -->

      <!-- una especie de sub-barra de sección que muestre el título Productos debajo del topbar-->
        <section class="content-header">
          <h2>Productos</h2>
        </section>
    <!--Contenedor Blanco --> 
          <div class="content-box">
            <div class="content-actions">
                <button class="btn-nuevo">Nuevo</button>
                <div class="buscador">
                  <input type="text" id="buscar" placeholder="Buscar producto...">
                  <i class="fa fa-search"></i>
                </div>
             </div>
              <!-- Aquí puedes poner la tabla, cards, etc. -->
               <!-- 🛑Tabla de productos -->
                  <table class="tabla-productos">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nombre del Producto</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Imagen</th>
                        <th>Categoría</th>
						<th>Acciones</th> 
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($fila = mysqli_fetch_assoc($result)): ?>
                      <tr>
                        <td><?= $fila['id_producto'] ?></td>
                        <td><?= htmlspecialchars($fila['nombre_producto']) ?></td>
                        <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                        <td>$<?= number_format($fila['precio'], 2) ?></td>
                        <td>
                          <?php if (!empty($fila['imagen'])): ?>
                            <img src="<?= htmlspecialchars($fila['imagen']) ?>" alt="Imagen" class="img-tabla">
                          <?php else: ?>
                            <span class="sin-imagen">Sin imagen</span>
                          <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($fila['nombre_categoria'] ?? 'Sin categoría') ?></td>
						<td class="acciones">
						<button class="btn-editar" data-id="<?= $fila['id_producto'] ?>">
						<i class="fa fa-pen"></i>
						</button>
						<button class="btn-eliminar" data-id="<?= $fila['id_producto'] ?>">
						<i class="fa fa-trash"></i>
						</button>
						</td>
                      </tr>
                      <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
	 <!-- Modal Nuevo Producto -->
<div id="modalProducto" class="modal">
  <div class="modal-content">
    <span class="cerrar">&times;</span>
    <h2>Nuevo Producto</h2>
    <form id="formProducto" method="POST" enctype="multipart/form-data">
      <label>Nombre del producto:</label>
      <input type="text" id="nombre" name="nombre" required>

      <label>Descripción:</label>
      <textarea id="descripcion" name="descripcion" required></textarea>

      <label>Precio:</label>
      <input type="number" id="precio" name="precio" step="0.01" required>

      <label>URL de imagen:</label>
      <input type="text" id="imagen" name="imagen" placeholder="Ejemplo: img/latte.jpg">

      <label>Categoría:</label>
      <select id="categoria" name="categoria" required>
        <option value="">Selecciona categoría</option>
        <option value="1">Bebidas Calientes</option>
        <option value="2">Bebidas Frías</option>
        <option value="3">Postres y Panadería</option>
        <option value="4">Snacks Salados</option>
      </select>

      <div class="modal-buttons">
        <button type="submit" class="guardar">Guardar</button>
        <button type="button" class="cancelar">Cancelar</button>
      </div>
    </form>
  </div>
</div>

  </main>
<!-- SCRIPT-->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
const modal = document.getElementById("modalProducto");
const btnNuevo = document.querySelector(".btn-nuevo");
const spanCerrar = document.querySelector(".cerrar");
const btnCancelar = document.querySelector(".cancelar");
const form = document.getElementById("formProducto");
let editMode = false;
let editId = null;

// --- ABRIR MODAL NUEVO ---
btnNuevo.onclick = function() {
  editMode = false;
  editId = null;
  modal.querySelector("h2").textContent = "Nuevo Producto";
  form.reset();
  modal.querySelector(".guardar").textContent = "Guardar";
  modal.style.display = "block";
};

// --- CERRAR MODAL ---
spanCerrar.onclick = btnCancelar.onclick = function() {
  modal.style.display = "none";
};
window.onclick = function(event) {
  if (event.target === modal) modal.style.display = "none";
};

// --- GUARDAR O ACTUALIZAR ---
form.addEventListener("submit", function(e) {
  e.preventDefault();

  const datos = {
    nombre: document.getElementById("nombre").value,
    descripcion: document.getElementById("descripcion").value,
    precio: document.getElementById("precio").value,
    imagen: document.getElementById("imagen").value,
    categoria: document.getElementById("categoria").value
  };

  let url = "guardar_producto.php";
  let mensaje = "Producto guardado correctamente.";
  if (editMode) {
    url = "editar_producto.php";
    datos.id = editId;
    mensaje = "Producto actualizado correctamente.";
  }

  fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(datos)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: "success",
        title: "¡Éxito!",
        text: mensaje,
        confirmButtonText: "Aceptar",
        confirmButtonColor: "#3085d6"
      }).then(() => {
        modal.style.display = "none";
        location.reload();
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudo guardar el producto.",
        confirmButtonText: "Aceptar",
        confirmButtonColor: "#d33"
      });
    }
  })
  .catch(() => {
    Swal.fire("Error", "Ocurrió un problema al conectar con el servidor.", "error");
  });
});


// --- ELIMINAR PRODUCTO (con SweetAlert2) ---
document.querySelectorAll(".btn-eliminar").forEach(btn => {
  btn.addEventListener("click", function() {
    const id = this.dataset.id;

    Swal.fire({
      title: "¿Eliminar producto?",
      text: "Esta acción no se puede deshacer.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar"
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("eliminar_producto.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              title: "Eliminado",
              text: "El producto fue eliminado correctamente.",
              icon: "success",
              confirmButtonText: "Aceptar"
            }).then(() => location.reload());
          } else {
            Swal.fire({
              title: "Error",
              text: "No se pudo eliminar el producto.",
              icon: "error",
              confirmButtonText: "Aceptar"
            });
          }
        })
        .catch(() => {
          Swal.fire("Error", "Ocurrió un problema al conectar con el servidor.", "error");
        });
      }
    });
  });
});


// --- EDITAR PRODUCTO (abrir modal con datos) ---
document.querySelectorAll(".btn-editar").forEach(btn => {
  btn.addEventListener("click", function() {
    const fila = this.closest("tr");
    editMode = true;
    editId = this.dataset.id;

    // Obtener datos de la fila
    const nombre = fila.children[1].innerText;
    const descripcion = fila.children[2].innerText;
    const precio = fila.children[3].innerText.replace('$','');
    const imagen = fila.querySelector("img") ? fila.querySelector("img").src : "";
    const categoriaTexto = fila.children[5].innerText;

    // Llenar los campos del formulario
    document.getElementById("nombre").value = nombre;
    document.getElementById("descripcion").value = descripcion;
    document.getElementById("precio").value = precio;
    document.getElementById("imagen").value = imagen;

    const categoriaSelect = document.getElementById("categoria");
    for (let option of categoriaSelect.options) {
      option.selected = (option.text === categoriaTexto);
    }

    modal.querySelector("h2").textContent = "Editar Producto";
    modal.querySelector(".guardar").textContent = "Actualizar";
    modal.style.display = "block";
  });
});

// --- BUSCADOR DE PRODUCTOS ---
const inputBuscar = document.getElementById("buscar");
const filas = document.querySelectorAll(".tabla-productos tbody tr");

inputBuscar.addEventListener("keyup", () => {
  const texto = inputBuscar.value.toLowerCase();
  filas.forEach(fila => {
    const contenido = fila.textContent.toLowerCase();
    fila.style.display = contenido.includes(texto) ? "" : "none";
  });
});

</script>

    </body>
</html>