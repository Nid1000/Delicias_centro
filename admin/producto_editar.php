<?php
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID no válido");
}

// Si se envió formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock  = $_POST['stock'];

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=?, stock=? WHERE id_producto=?");
    $stmt->bind_param("sdii", $nombre, $precio, $stock, $id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

// Cargar datos actuales
$stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
if (!$producto) {
    die("Producto no encontrado");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar producto</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Editar producto</h2>
<form method="POST">
  <div class="mb-3">
    <label>Nombre</label>
    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
  </div>
  <div class="mb-3">
    <label>Precio</label>
    <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $producto['precio']; ?>">
  </div>
  <div class="mb-3">
    <label>Stock</label>
    <input type="number" name="stock" class="form-control" value="<?php echo $producto['stock']; ?>">
  </div>
  <button type="submit" class="btn btn-primary">Guardar cambios</button>
  <a href="index.php" class="btn btn-secondary">Volver</a>
</form>
</body>
</html>
