<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $disponible = 1;

    // Subida de imagen
    $imagen = $_FILES['imagen']['name'];
    $ruta = "images/productos/" . basename($imagen);
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

    $sql = "INSERT INTO productos (nombre, precio, imagen, disponible) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $precio, $imagen, $disponible]);

    echo "✅ Producto agregado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
</head>
<body>
    <h1>Agregar Producto</h1>
    <form action="agregar_producto.php" method="POST" enctype="multipart/form-data">
        <label>Nombre:</label>
        <input type="text" name="nombre" required><br><br>
        
        <label>Precio:</label>
        <input type="number" step="0.01" name="precio" required><br><br>
        
        <label>Imagen:</label>
        <input type="file" name="imagen" accept="image/*" required><br><br>
        
        <button type="submit">Agregar Producto</button>
    </form>
</body>
</html>
