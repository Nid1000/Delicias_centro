<?php
session_start();
include 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se recibió el id del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_carrito'])) {
    $id_carrito = $_POST['id_carrito'];
    $id_usuario = $_SESSION['id_usuario'];

    // Eliminar el producto del carrito, asegurando que pertenezca al usuario
    $sql = "DELETE FROM carrito WHERE id_carrito = ? AND id_usuario = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$id_carrito, $id_usuario])) {
        // Redirigir de vuelta al carrito
        header("Location: cart.php");
        exit();
    } else {
        echo "❌ Error al eliminar el producto del carrito.";
    }
} else {
    // Si se accede sin POST válido
    header("delicias: cart.php");
    exit();
}
?>
