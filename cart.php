<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT carrito.id_carrito, productos.nombre, productos.precio, carrito.cantidad 
        FROM carrito 
        JOIN productos ON carrito.id_producto = productos.id_producto
        WHERE carrito.id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #ffd580;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #cc9900;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            height: 60px;
        }

        h1 {
            text-align: center;
            color: #663300;
            margin-top: 30px;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #fff3cc;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e6b800;
            color: #663300;
        }

        th {
            background-color: #ffcc66;
        }

        .total {
            text-align: right;
            width: 90%;
            margin: 10px auto 30px;
            font-size: 1.2em;
            color: #663300;
            font-weight: bold;
        }

        .button-container {
            width: 90%;
            margin: 0 auto 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .button-container form button,
        .btn-seguir {
            background-color: #ffcc66;
            border: 2px solid #e6b800;
            padding: 10px 20px;
            border-radius: 5px;
            color: #663300;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .button-container form button:hover,
        .btn-seguir:hover {
            background-color: #e6b800;
            color: white;
        }

        footer {
            background-color: #663300;
            color: white;
            text-align: center;
            padding: 15px;
        }

        .empty-cart {
            text-align: center;
            margin: 50px;
            font-size: 1.3em;
            color: #996600;
        }
    </style>
</head>
<body>

    <!-- Header con el logo -->
    <header>
        <div class="logo">
            <img src="images/logo/logo.jpg" alt="Logo de la tienda">
        </div>
        <div style="font-size: 24px; color: white;">
            <a href="cart.php" style="color: white; text-decoration: none;">
                🛒 Mi Carrito
            </a>
        </div>
    </header>

    <h1>Mi Carrito</h1>

    <?php if (count($productos_carrito) === 0): ?>
        <p class="empty-cart">Tu carrito está vacío. <a href="catalogo.php" class="btn-seguir">🛍️ Ir al catálogo</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio (S/.)</th>
                    <th>Cantidad</th>
                    <th>Subtotal (S/.)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos_carrito as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td>S/.<?php echo number_format($producto['precio'], 2); ?></td>
                        <td><?php echo $producto['cantidad']; ?></td>
                        <td>S/.<?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?></td>
                        <td>
                            <form action="eliminar_carrito.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id_carrito" value="<?php echo $producto['id_carrito']; ?>">
                                <button type="submit" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total += $producto['precio'] * $producto['cantidad']; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            Total: S/.<?php echo number_format($total, 2); ?>
        </div>

        <div class="button-container">
            <form action="direccion_entrega.php" method="GET">
                <button type="submit">ENTREGA</button>
            </form>
            <a href="catalogo.php" class="btn-seguir">SEGUIR COMPRANDO</a>
        </div>
    <?php endif; ?>

    <footer>
        &copy; <?php echo date('Y'); ?> Tu Tienda. Todos los derechos reservados.
    </footer>

</body>
</html>
