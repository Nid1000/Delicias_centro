<?php
session_start();
include 'db.php';

// Obtener categoría de la URL (para filtrar y pre-seleccionar en el buscador)
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : 'todos';  // 'todos' por defecto

// Ruta de la carpeta de imágenes locales
$ruta_local = "images/productos/";
if (!is_dir($ruta_local)) {
    die("Error: La carpeta '$ruta_local' no existe. Crea la carpeta y agrega imágenes (ej: pastel_chocolate.jpg).");
}

$productos_locales = array_diff(scandir($ruta_local), array('.', '..'));

// Manejo de errores desde add_to_cart.php
if (isset($_GET['error'])) {
    $error_msg = '';
    $nombre_err = isset($_GET['nombre']) ? " (Producto: " . htmlspecialchars($_GET['nombre']) . ")" : '';
    switch ($_GET['error']) {
        case 'cantidad_invalida': $error_msg = 'Cantidad inválida. Debe ser al menos 1.'; break;
        case 'producto_no_encontrado': $error_msg = 'Producto no encontrado en la base de datos.' . $nombre_err; break;
        case 'bd_fallo': $error_msg = 'Error en la base de datos. Intenta de nuevo o contacta al administrador.'; break;
        case 'datos_faltantes': $error_msg = 'Datos incompletos. Recarga la página.'; break;
        case 'no_logueado': $error_msg = 'Debes iniciar sesión para agregar al carrito.'; break;
        case 'error_desconocido': $error_msg = 'Error desconocido. Recarga la página.'; break;
        default: $error_msg = 'Error desconocido.';
    }
    echo '<div class="alert alert-danger text-center" style="max-width: 600px; margin: 20px auto;">' . $error_msg . ' <a href="javascript:history.back()">Volver</a></div>';
}
if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'agregado') {
    echo '<div class="alert alert-success text-center" style="max-width: 600px; margin: 20px auto;">
        Producto agregado al carrito correctamente.
    </div>';
}

// Función para filtrar por categoría
function filtrar_productos_por_categoria($productos, $categoria) {
    if ($categoria === 'todos' || $categoria === '') return $productos;

    $productos_filtrados = [];
    $keywords = [
        'panes' => ['pan'],
        'pasteles' => ['pastel'],
        'bocaditos' => ['bocado', 'empanada', 'sandwich', 'sándwich']
    ];

    $palabras_clave = $keywords[$categoria] ?? [$categoria];

    foreach ($productos as $producto) {
        $nombre_producto = strtolower(pathinfo($producto, PATHINFO_FILENAME));
        foreach ($palabras_clave as $kw) {
            if (strpos($nombre_producto, $kw) !== false) {
                $productos_filtrados[] = $producto;
                break;
            }
        }
    }

    if (empty($productos_filtrados)) {
        return $productos;
    }

    return $productos_filtrados;
}

$productos_filtrados = filtrar_productos_por_categoria($productos_locales, $categoria);

// Ordenar alfabéticamente
usort($productos_filtrados, function($a, $b) {
    $nombre_a = strtolower(pathinfo($a, PATHINFO_FILENAME));
    $nombre_b = strtolower(pathinfo($b, PATHINFO_FILENAME));
    return strcmp($nombre_a, $nombre_b);
});

// Obtener productos de BD
$productos_bd = [];
if (isset($pdo)) {
    $sql = "SELECT * FROM productos WHERE disponible = 1 ORDER BY nombre ASC";
    $stmt = $pdo->query($sql);
    $productos_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para datos producto
function obtener_datos_producto($nombre_archivo, $productos_bd) {
    $nombre_producto = pathinfo($nombre_archivo, PATHINFO_FILENAME);
    foreach ($productos_bd as $prod_bd) {
        if (pathinfo($prod_bd['imagen'], PATHINFO_FILENAME) === $nombre_producto) {
            return [
                'nombre' => $prod_bd['nombre'],
                'precio' => $prod_bd['precio'],
                'descripcion' => $prod_bd['descripcion'] ?? 'Producto especial de nuestra panadería.',
                'id_producto' => $prod_bd['id_producto'] ?? 0
            ];
        }
    }
    return [
        'nombre' => ucwords(str_replace('_', ' ', $nombre_producto)),
        'precio' => 8.00,
        'descripcion' => 'Producto especial de nuestra panadería, elaborado con ingredientes de calidad.',
        'id_producto' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Catálogo de Productos - Delicias del Centro</title>

    <!-- Bootstrap y Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
    body {
        background-color: #ffd580;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }
    h1 {
        text-align: center;
        color: #663300;
        padding: 20px;
    }
    .productos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
        justify-items: center;
    }
    .producto {
        border: 1px solid #e6b800;
        border-radius: 8px;
        text-align: center;
        background-color: #fff3cc;
        box-shadow: 0 4px 8px rgba(230, 184, 0, 0.3);
        padding: 15px;
        transition: transform 0.2s;
    }
    .producto:hover {
        transform: scale(1.05);
    }
    .producto img {
        max-width: 100%;
        border-radius: 8px;
        height: auto;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .producto img:hover {
        opacity: 0.8;
    }
    .producto h2 {
        font-size: 18px;
        color: #663300;
        margin: 10px 0;
    }
    .producto p {
        font-size: 14px;
        color: #996600;
    }
    .producto form {
        margin-top: 10px;
    }
    .producto button, .producto a.btn {
        padding: 10px 15px;
        background-color: #ffcc66;
        color: #663300;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: 2px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.2s;
    }
    .producto button:hover, .producto a.btn:hover {
        background-color: #e6b800;
        color: #fff;
    }
    .botones {
        margin-top: 20px;
        text-align: center;
    }
    .botones button {
        padding: 10px 20px;
        margin: 5px;
        font-size: 16px;
        cursor: pointer;
        background-color: #ffcc66;
        color: #663300;
        border: none;
        border-radius: 5px;
    }
    .botones button:hover {
        background-color: #e6b800;
    }
    .btn-primary {
        background-color: #ffcc66 !important;
        border-color: #e6b800 !important;
        color: #663300 !important;
    }
    .btn-primary:hover { background-color: #e6b800 !important; color: #fff !important; }
    .buscador-categoria {
        background: #fff3cc;
        border: 1px solid #e6b800;
        border-radius: 5px;
        padding: 10px;
        text-align: center;
    }
    </style>
</head>
<body>
<!-- CABECERA ORDENADA -->
<header class="d-flex justify-content-between align-items-center flex-wrap p-3" 
        style="background:#ffd580; border-bottom:2px solid #e6b800;">
    <!-- Logo -->
    <div>
        <img src="images/logo/logo.jpg" alt="Logo" style="max-width:120px; height:auto;" />
    </div>

    <!-- Buscador por Categoría -->
    <div class="buscador-categoria my-2">
        <form id="form-categoria" action="catalogo.php" method="GET" class="d-flex align-items-center flex-wrap gap-2">
            <label for="categoria-select" class="mb-0">Buscar por Categoría:</label>
            <select id="categoria-select" name="categoria" onchange="this.form.submit();" class="form-select form-select-sm" style="width:auto;">
                <option value="todos" <?php echo ($categoria === 'todos') ? 'selected' : ''; ?>>Todos los Productos</option>
                <option value="panes" <?php echo ($categoria === 'panes') ? 'selected' : ''; ?>>Panes</option>
                <option value="pasteles" <?php echo ($categoria === 'pasteles') ? 'selected' : ''; ?>>Pasteles</option>
                <option value="bocaditos" <?php echo ($categoria === 'bocaditos') ? 'selected' : ''; ?>>Bocaditos</option>
            </select>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='catalogo.php'">Limpiar Filtro</button>
        </form>
    </div>

    <!-- Mi Cuenta + Carrito -->
    <div class="d-flex align-items-center gap-3">
        <a href="mi_cuenta.php" class="btn btn-primary">
            <?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Mi Cuenta'; ?>
        </a>
        <a href="cart.php" class="position-relative d-flex align-items-center text-decoration-none" style="color:#663300;">
            <i class="fas fa-cart-arrow-down fs-4 me-1"></i>
            <span>Mi carrito</span>
            <?php
                $id_usuario = $_SESSION['id_usuario'] ?? 0;
                $sql = "SELECT SUM(cantidad) AS total_items FROM carrito WHERE id_usuario = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_usuario]);
                $carrito = $stmt->fetch(PDO::FETCH_ASSOC);
                $total_items = $carrito['total_items'] ?? 0;
            ?>
            <span class="badge bg-danger rounded-circle position-absolute top-0 start-100 translate-middle">
                <?php echo $total_items; ?>
            </span>
        </a>
    </div>
</header>
<a href="send_opinion.php" class="btn btn-primary">Nos interesa tu opinion</a>

<h1>
    <?php 
    $titulo_categoria = ($categoria === 'todos') ? 'Todos los Productos' : ucfirst($categoria) . '';
    echo $titulo_categoria ;
    ?>
</h1>

<?php if (empty($productos_filtrados)): ?>
    <div class="alert alert-warning text-center" style="max-width: 600px; margin: 20px auto;">
        <h4>No se encontraron productos en esta categoría.</h4>
        <p>Verifica que la carpeta <code>images/productos/</code> tenga imágenes con nombres que incluyan la categoría (ej: <code>pastel_chocolate.jpg</code> para "Pasteles").</p>
        <a href="catalogo.php" class="btn btn-primary">Ver Todos los Productos</a>
    </div>
<?php else: ?>
    <div class="productos">
        <?php foreach ($productos_filtrados as $producto_local): ?>
            <?php 
                $datos_producto = obtener_datos_producto($producto_local, $productos_bd);
                $nombre_producto = htmlspecialchars($datos_producto['nombre']);
                $nombre_archivo = urlencode(pathinfo($producto_local, PATHINFO_FILENAME));
                $id_producto_bd = $datos_producto['id_producto'];
                $url_detalle = "detalle.php?producto=" . $nombre_archivo;
                if ($id_producto_bd > 0) {
                    $url_detalle .= "&id=" . $id_producto_bd;
                }
            ?>
            <?php $id_modal = "modal_" . $datos_producto['id_producto'] . '_' . $nombre_archivo; ?>
<div class="producto">
    <img src="<?php echo htmlspecialchars($ruta_local . $producto_local); ?>" alt="Producto">
    <h2><?php echo $nombre_producto; ?></h2>
    <p>Precio: S/ <?php echo number_format($datos_producto['precio'], 2); ?></p>

    <!-- Formulario -->
    <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
        <input type="hidden" name="id_producto" value="<?php echo $datos_producto['id_producto']; ?>">
        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" min="1" value="1" required>
        <button type="submit">Agregar al carrito</button>
        <!-- Botón de detalle -->
        <button type="button" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#<?php echo $id_modal; ?>">
            Ver Detalles
        </button>
    </form>
</div>

<!-- Modal de Detalle -->
<div class="modal fade" id="<?php echo $id_modal; ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo htmlspecialchars($datos_producto['nombre']); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img src="<?php echo htmlspecialchars($ruta_local . $producto_local); ?>" 
             alt="<?php echo htmlspecialchars($datos_producto['nombre']); ?>" 
             class="img-fluid rounded mb-3">
        <p><?php echo htmlspecialchars($datos_producto['descripcion']); ?></p>
        <p><strong>Precio actual:</strong> S/ <?php echo number_format($datos_producto['precio'], 2); ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="botones">
    <form action="index.php" method="GET">
        <button type="submit">Volver al Inicio</button>
    </form>
    <form action="logout.php" method="POST">
        <button type="submit">Cerrar sesión</button>
    </form>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="bi bi-telephone-fill"></i> Contacto</h5>
                <p>933500069</p>
            </div>
            <div class="col-md-4">
                <h5><i class="bi bi-geo-alt-fill"></i> Dirección</h5>
                <p>Jr. Parra del Riego 164</p>
            </div>
            <div class="col-md-4">
                <h5><i class="bi bi-bag-fill"></i> Línea de Productos</h5>
                <p>Panes, Pasteles, Bocaditos</p>
            </div>
        </div>
        <p class="mt-3">&copy; 2025 Delicias del Centro</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
