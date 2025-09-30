<?php
session_start();
include 'db.php';


// Ruta de la carpeta de imágenes locales
$ruta_local = "images/productos/";
$productos_locales = array_diff(scandir($ruta_local), array('.', '..'));
// Carpeta del admin (para funciones administrativas)
$ruta_admin = "admin/public/index.html";
$productos_admin = [];
if (is_dir($ruta_admin)) {
    $productos_admin = array_diff(scandir($ruta_admin), array('.', '..'));
}
// Obtener término de búsqueda si existe
$termino_busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Función para filtrar productos
function filtrar_productos_por_busqueda($productos, $termino) {
    if ($termino === '') return $productos;
    $productos_filtrados = [];
    foreach ($productos as $producto) {
        $nombre_producto = strtolower(pathinfo($producto, PATHINFO_FILENAME));
        $termino_minuscula = strtolower($termino);
        if (strpos($nombre_producto, $termino_minuscula) !== false) {
            $productos_filtrados[] = $producto;
        }
    }
    return $productos_filtrados;
}

$productos_filtrados = filtrar_productos_por_busqueda($productos_locales, $termino_busqueda);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DELICIAS DEL CENTRO</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    /* ======== ESTILO CLARO (por defecto) ======== */
    body {
      background-color: #ffd580; /* naranja claro */
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      color: #663300;
    }
    .navbar-dark.bg-dark {
      background-color: #cc9900 !important; /* naranja oscuro para navbar */
    }
    .btn-primary {
      background-color: #ffcc66 !important;
      border-color: #e6b800 !important;
      color: #663300 !important;
    }
    .btn-primary:hover {
      background-color: #e6b800 !important;
      border-color: #cc9900 !important;
      color: #fff !important;
    }
    .card {
      background-color: #fff3cc;
      border: 1px solid #e6b800;
    }
    .card-title { color: #663300; }
    .card-text { color: #996600; }
    footer {
      background-color: #663300;
      color: white;
    }

    /* ======== ESTILO OSCURO (más oscuro, no negro) ======== */
    .dark-mode {
      background-color: #e6b873 !important; /* naranja más oscuro */
      color: #3a1f00 !important;
    }
    .dark-mode .navbar-dark.bg-dark {
      background-color: #996600 !important; /* marrón más oscuro */
    }
    .dark-mode .card {
      background-color: #f2d98c !important; /* beige más oscuro */
      border: 1px solid #996600;
    }
    .dark-mode .card-title,
    .dark-mode .card-text {
      color: #3a1f00 !important;
    }
    .dark-mode footer {
      background-color: #4d2600 !important;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="images/logo/logo.jpg" alt="Logo" width="110" height="40" class="me-2 rounded-circle" />
      <strong>Delicias del Centro</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menuPrincipal">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item me-3">
          <form class="d-flex" role="search" action="index.php" method="GET">
            <input class="form-control form-control-sm me-2" type="search" name="buscar" placeholder="Buscar..." value="<?php echo htmlspecialchars($termino_busqueda); ?>" />
            <button class="btn btn-outline-light btn-sm" type="submit">
              <i class="bi bi-search" style="font-size: 1.5rem;"></i>
            </button>
          </form>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/index.php') ? 'active' : ''; ?>" href="index.php"><i class="bi bi-house-door-fill"></i> Inicio</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="menuProductos" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-basket-fill"></i> Productos
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="catalogo.php?categoria=panes">Panes</a></li>
            <li><a class="dropdown-item" href="catalogo.php?categoria=pasteles">Pasteles</a></li>
            <li><a class="dropdown-item" href="catalogo.php?categoria=bocaditos">Bocaditos</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://www.facebook.com/deliciashuancayoperu" target="_blank">
            <i class="bi bi-facebook"></i> Nuestras Redes
          </a>
        </li>
        <<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="menuCuenta" role="button" data-bs-toggle="dropdown">
    <i class="bi bi-person-circle"></i> Usuario
  </a>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="register.php">Registrarse</a></li>
    <li><a class="dropdown-item" href="login.php">Iniciar Sesión</a></li>
    <li><a class="dropdown-item" href="admin/public/login.html">Admin</a></li>
  </ul>
</li>

        
        <!-- Botón modo oscuro -->
        <li class="nav-item">
          <button id="btn-tema" class="btn btn-outline-light btn-sm ms-2">
            <i class="bi bi-moon-fill"></i> Oscuro/Claro
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Descripción -->
<section class="descripcion p-4">
  <div class="container">
    <div class="card shadow p-4">
      <p>
        La panadería <strong>“Delicias del Centro”</strong>, ubicada en el corazón de la ciudad 
        y conocida como <em>"El horno de tradición"</em>, celebra su centenario.  
        Desde sus inicios, se ha dedicado a la elaboración de pan artesano, 
        utilizando ingredientes naturales.
      </p>
    </div>
  </div>
</section>

<!-- Catálogo -->
<section class="productos-destacados p-4 bg-light">
  <div class="container">
    <h2 class="mb-4 text-center">
      <?php
        if ($termino_busqueda !== '') {
          echo 'Resultados para: "' . htmlspecialchars($termino_busqueda) . '"';
        } else {
          echo 'Productos Destacados';
        }
      ?>
    </h2>
    <div class="row">
      <?php if (count($productos_filtrados) === 0): ?>
        <p class="text-center">No se encontraron productos que coincidan con tu búsqueda.</p>
      <?php else: ?>
        <?php foreach ($productos_filtrados as $producto_local): ?>
          <?php $nombre_producto = htmlspecialchars(pathinfo($producto_local, PATHINFO_FILENAME)); ?>
          <?php $id_modal = "modal_" . md5($nombre_producto); ?>
          <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
              <img src="<?php echo $ruta_local . $producto_local; ?>" 
                   alt="<?php echo $nombre_producto; ?>" 
                   class="card-img-top"
                   style="cursor: pointer;"
                   data-bs-toggle="modal"
                   data-bs-target="#<?php echo $id_modal; ?>">
              <div class="card-body text-center">
                <h5 class="card-title"><?php echo $nombre_producto; ?></h5>
                <p class="card-text">A muy buen precio</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#<?php echo $id_modal; ?>">
                  Ver Detalles
                </button>
              </div>
            </div>
          </div>
          <!-- Modal -->
          <div class="modal fade" id="<?php echo $id_modal; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><?php echo $nombre_producto; ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                  <img src="<?php echo $ruta_local . $producto_local; ?>" 
                       alt="<?php echo $nombre_producto; ?>" 
                       class="img-fluid rounded mb-3" />
                  <p>Producto especial de nuestra panadería, elaborado con ingredientes de calidad.</p>
                  <p><strong>Precio:</strong> S/ 8.00</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  <a href="cart.php?producto=<?php echo urlencode($nombre_producto); ?>" class="btn btn-success">
                    <i class="bi bi-cart-plus"></i> Añadir al Carrito
                  </a>
                  <a href="catalogo.php" class="btn btn-primary ms-2">Ver Catálogo</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
  <div class="container text-center">
  <div class="row">
    <div class="col-md-4">
      <h5><i class="bi bi-telephone-fill"></i> Contacto</h5>
      <a 
        href="https://wa.me/51993560096?text=Hola%20quiero%20información" 
        target="_blank" 
        class="btn btn-success">
        <i class="bi bi-whatsapp"></i> Chatea por WhatsApp
      </a>
      <p>993560096</p>
      <p>964527852</p>
    </div>

    <div class="col-md-4">
      <h5><i class="bi bi-geo-alt-fill"></i> Dirección</h5>
      <div class="ratio ratio-16x9">
        <iframe src="catalogo.html" style="width:100%; height:800px; border:none;"></iframe>
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3901.5437631772567!2d-77.03687158478994!3d-12.04637309146362!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c8bbdb312fd7%3A0x31e6bdf58e7b8c1b!2sPlaza%20de%20Armas%20de%20Lima!5e0!3m2!1ses!2spe!4v1695820123456!5m2!1ses!2spe" 
          style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
      <p>Jr. Parra del Riego 164</p>
    </div>
     <div class="col-md-4">
        <h5><i class="bi bi-bag-fill"></i> Productos</h5>
        <p>Panes, Pasteles, Bocaditos</p>
          <a href="https://www.facebook.com/deliciashuancayoperu" target="_blank" class="btn btn-primary">
  <i class="bi bi-facebook"></i> Síguenos en Facebook
  <p>✨ ¡Mejoramos nuestra calidad! Ahora nuestros panes son aún más deliciosos gracias a la masa madre. 🍞💛</p>
</a>
      </div>
      <footer>
    <p>&copy; 2025 Delicias del Centro</p>
</footer>
    </div>
  </div>
  </div>
</div>

     
</footer>
<script src="admin/js/api.js"></script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
  const productos = await getProductos(); // del api.js
  const container = document.getElementById('lista-productos');

  if (!container) return;

  container.innerHTML = '';
  productos.forEach(prod => {
    const card = `
      <div class="col-md-3 mb-3">
        <div class="card h-100">
          <img src="${prod.imagenes?.[0] || 'https://via.placeholder.com/300x200?text=Imagen'}" class="card-img-top" />
          <div class="card-body">
            <h5 class="card-title">${prod.nombre}</h5>
            <p class="card-text">Precio: S/ ${prod.precio}</p>
          </div>
        </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', card);
  });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script modo oscuro -->
<script>
  const body = document.body;
  const btnTema = document.getElementById('btn-tema');

  // Cargar preferencia guardada
  if(localStorage.getItem('tema') === 'oscuro') {
    body.classList.add('dark-mode');
  }

  btnTema.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    if(body.classList.contains('dark-mode')) {
      localStorage.setItem('tema', 'oscuro');
    } else {
      localStorage.setItem('tema', 'claro');
    }
  });
  
</script>
</body>
</html>
