<?php
session_start();
include 'db.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Obtener datos del usuario desde la base de datos
$usuario_id = $_SESSION['id_usuario'];
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener los productos en el carrito del usuario
$sql_carrito = "SELECT carrito.id_carrito, productos.nombre, productos.precio, carrito.cantidad 
                FROM carrito 
                JOIN productos ON carrito.id_producto = productos.id_producto
                WHERE carrito.id_usuario = ?";
$stmt_carrito = $pdo->prepare($sql_carrito);
$stmt_carrito->execute([$usuario_id]);
$productos_carrito = $stmt_carrito->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Delicias</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logo/logo.jpg" alt="Logo de Delicias Del Centro">
        </div>
        <h1>MI CUENTA</h1>
        <a href="logout.php" class="cerrar-sesion">Cerrar sesión</a>
        <a href="index.php" class="index.php">Catalogo</a>
    </header>

    <main>
        <section class="perfil">
            <h2>Perfil</h2>
            <form>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" disabled>

                <label for="email">Correo electrónico:</label>
                <input type="text" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" disabled>

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>" disabled>
            </form>
        </section>

        <section class="mi-carrito">
            <h2>Mi Carrito</h2>
            <div class="carrito-lista">
                <?php if (count($productos_carrito) > 0): ?>
                    <?php foreach ($productos_carrito as $producto): ?>
                        <div class="carrito-item">
                            <p><strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></p>
                            <p>Cantidad: <?php echo $producto['cantidad']; ?></p>
                            <p>Precio Unitario: S/ <?php echo number_format($producto['precio'], 2); ?></p>
                            <p>Total: S/ <?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php
                        // Calcular el total del carrito
                        $total_carrito = 0;
                        foreach ($productos_carrito as $producto) {
                            $total_carrito += $producto['precio'] * $producto['cantidad'];
                        }
                    ?>
                    <h3>Total Carrito: S/ <?php echo number_format($total_carrito, 2); ?></h3>
                    <form action="checkout.php" method="POST">
                        <button type="submit">Proceder al Pago</button>
                    </form>
                <?php else: ?>
                    <p>No tienes productos en tu carrito.</p>
                <?php endif; ?>
            </div>

            <!-- Botones para realizar acciones adicionales -->
            <div class="acciones-carrito">
                <a href="catalogo.php" class="btn">Ir al Catálogo</a> <!-- Redirige al catálogo de productos -->
                <?php if (count($productos_carrito) > 0): ?>
                    <form action="vaciar_carrito.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas vaciar el carrito?');">
                        <button type="submit" class="btn vaciar">Vaciar Carrito</button> <!-- Vaciar carrito -->
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
