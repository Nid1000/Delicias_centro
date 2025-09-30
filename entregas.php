<?php
session_start();
include 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si la dirección de entrega está en la sesión
if (!isset($_SESSION['direccion'])) {
    echo "Por favor, ingresa tu dirección de entrega.";
    exit();
}

// Obtener los productos del carrito del usuario
$sql = "SELECT carrito.id_carrito, productos.nombre, productos.precio, carrito.cantidad 
        FROM carrito 
        JOIN productos ON carrito.id_producto = productos.id_producto
        WHERE carrito.id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;

// Insertar los datos de entrega para cada pedido
foreach ($productos_carrito as $producto) {
    // Aquí podría ir la lógica para crear un pedido, si fuera necesario
    // También se puede guardar la información de la entrega en la base de datos
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <!-- Logo en la cabecera -->
        <div class="logo" style="position: relative; padding-left: 20px;">
        <img src="images/logo/logo.jpg" alt="Logo de Delicias Del Centro" style="max-width: 120px; height: auto;">
    </div>
    </header>

    <main>
        <h1>Detalles de Entrega</h1>

        <div class="carrito-section">
            <h2>Mi Carrito</h2>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos_carrito as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                            <td><?php echo $producto['cantidad']; ?></td>
                            <td>$<?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?></td>
                        </tr>
                        <?php $total += $producto['precio'] * $producto['cantidad']; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total: $<?php echo number_format($total, 2); ?></h3>
        </div>

        <p>Tu dirección de entrega es: <?php echo $_SESSION['direccion']; ?></p>

        <div class="acciones">
            <form action="checkout.php" method="POST">
                <button type="submit">Proceder al Pago</button>
            </form>

            <a href="cart.php" class="button">Volver al Carrito</a>
        </div>
    </main>
</body>
</html>
