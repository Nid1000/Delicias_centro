<?php
session_start();
include 'db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: catalogo.php?error=no_logueado");
    exit;
}

// Validar datos recibidos
if (!isset($_POST['id_producto'], $_POST['cantidad'])) {
    header("Location: catalogo.php?error=datos_faltantes");
    exit;
}

$id_producto = (int)$_POST['id_producto'];
$cantidad = (int)$_POST['cantidad'];

if ($cantidad < 1) {
    header("Location: catalogo.php?error=cantidad_invalida");
    exit;
}

// Verificar que el producto exista y esté disponible
$sql = "SELECT * FROM productos WHERE id_producto = ? AND disponible = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    $nombre = isset($_POST['nombre']) ? urlencode($_POST['nombre']) : '';
    header("Location: catalogo.php?error=producto_no_encontrado&nombre=$nombre");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si el producto ya está en el carrito del usuario
$sql = "SELECT * FROM carrito WHERE id_usuario = ? AND id_producto = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario, $id_producto]);
$carrito_item = $stmt->fetch(PDO::FETCH_ASSOC);

try {
    if ($carrito_item) {
        // Actualizar cantidad sumando la nueva cantidad
        $nueva_cantidad = $carrito_item['cantidad'] + $cantidad;
        $sql = "UPDATE carrito SET cantidad = ? WHERE id_carrito = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nueva_cantidad, $carrito_item['id_carrito']]);
    } else {
        // Insertar nuevo producto en carrito
        $sql = "INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario, $id_producto, $cantidad]);
    }
    // Redirigir al catálogo con éxito
    header("Location: catalogo.php?mensaje=agregado");
    exit;
} catch (Exception $e) {
    header("Location: catalogo.php?error=bd_fallo");
    exit;
}
?>