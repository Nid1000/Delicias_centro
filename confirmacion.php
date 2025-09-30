<?php
$success = isset($_GET['success']) ? $_GET['success'] : 'false';
$total_items = isset($_GET['total_items']) ? (int)$_GET['total_items'] : 0;
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Mensaje de éxito o error -->
<?php if ($success == 'true'): ?>
    <div class="alert alert-success"> <!-- Cambia a verde para éxito -->
        ¡Producto agregado al carrito con éxito! Total de items: <?php echo $total_items; ?>.
    </div>
<?php else: ?>
    <div class="alert alert-danger"> <!-- Rojo para error -->
        Hubo un error: <?php echo htmlspecialchars($msg ?: 'Intenta nuevamente.'); ?> Asegúrate de estar logueado.
    </div>
<?php endif; ?>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
    <style>
    body {
        background-color: #f9f3d2; /* Fondo color suave */
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    .container {
        margin-top: 50px;
        text-align: center;
    }

    .card {
        background-color: #ffd580; /* naranja claro */
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .card h2 {
        color: #663300; /* marrón oscuro */
    }

    .btn-custom {
        background-color: #ffcc66; /* color naranja */
        color: #663300;
        border-radius: 8px;
        font-weight: bold;
        padding: 10px 20px;
        border: none;
    }

    .btn-custom:hover {
        background-color: #e6b800; /* naranja más oscuro */
        color: #fff;
    }

    .alert {
        background-color: #cc3300; /* rojo */
        color: white;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 20px;
    }

    </style>
</head>
<body>

    <div class="container">
        <!-- Mensaje de éxito o error -->
        <?php if ($success == 'true'): ?>
            <div class="alert">
                Producto agregado al carrito con éxito.
            </div>
        <?php elseif ($success == 'false'): ?>
            <div class="alert">
                Hubo un error al agregar el producto al carrito. Intenta nuevamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>¡El producto se ha agregado al carrito!</h2>
            <p>Ahora puedes continuar con tu compra o ver el carrito.</p>
            <a href="catalogo.php" class="btn btn-custom">Seguir comprando</a>
            <a href="cart.php" class="btn btn-custom">Ver mi carrito</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
