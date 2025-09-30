<?php
session_start();
if(!isset($_SESSION['admin'])){
  header('Location: index.php');
  exit;
}
include 'db.php';
$result = $conn->query("SELECT * FROM productos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Panel Admin</title>
</head>
<body>
<div class="container mt-5">
<h1>Panel de Productos</h1>
<a href="producto_nuevo.php" class="btn btn-success mb-3">+ Nuevo Producto</a>
<table class="table table-bordered">
<thead>
<tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Imagen</th><th>Acciones</th></tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['nombre'] ?></td>
<td>S/ <?= $row['precio'] ?></td>
<td><?= $row['stock'] ?></td>
<td><img src="../images/productos/<?= $row['imagen'] ?>" width="60"></td>
<td>
<a href="producto_editar.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
<a href="producto_eliminar.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>
