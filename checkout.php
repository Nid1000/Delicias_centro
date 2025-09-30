<?php
session_start();
include 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener dirección del usuario
$sql = "SELECT direccion FROM usuarios WHERE id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("No se ha encontrado la dirección del usuario.");
}
$direccion_entrega = $usuario['direccion'];

// Obtener productos del carrito
$sql = "SELECT c.id_carrito, p.id_producto, p.precio, c.cantidad 
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($productos_carrito)) {
    die("Tu carrito está vacío.");
}

// Calcular total
$total = 0;
foreach ($productos_carrito as $producto) {
    $total += $producto['precio'] * $producto['cantidad'];
}

// Variables para mensajes
$errores = [];
$exito = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $tipo_comprobante = $_POST['tipo_comprobante'] ?? '';
    $correo = trim($_POST['correo'] ?? '');
    $ruc = trim($_POST['ruc'] ?? '');
    $razon_social = trim($_POST['razon_social'] ?? '');

    // Validar comprobante
    if ($tipo_comprobante === 'boleta') {
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Debe ingresar un correo válido para la boleta.";
        }
    } elseif ($tipo_comprobante === 'factura') {
        if (empty($razon_social)) {
            $errores[] = "Debe ingresar la razón social.";
        }
        if (empty($ruc) || strlen($ruc) != 11 || !ctype_digit($ruc)) {
            $errores[] = "Debe ingresar un RUC válido de 11 dígitos.";
        }
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Debe ingresar un correo válido para la factura.";
        }
    } else {
        $errores[] = "Debe seleccionar un tipo de comprobante.";
    }

    // Validar método de pago
    $nro_operacion = $banco = $num_tarjeta = $fecha_exp = $cvv = '';
    $ultimos_digitos = null;

    if ($metodo_pago === 'Tarjeta de Crédito') {
        $num_tarjeta = trim($_POST['num_tarjeta'] ?? '');
        $fecha_exp   = trim($_POST['fecha_exp'] ?? '');
        $cvv         = trim($_POST['cvv'] ?? '');

        if (strlen($num_tarjeta) < 13) $errores[] = "Número de tarjeta inválido.";
        if (empty($fecha_exp)) $errores[] = "Debe ingresar la fecha de expiración.";
        if (strlen($cvv) < 3) $errores[] = "CVV inválido.";

        $ultimos_digitos = substr($num_tarjeta, -4);
    } elseif ($metodo_pago === 'Pago por Depósito') {
        $nro_operacion = trim($_POST['nro_operacion'] ?? '');
        $banco = trim($_POST['banco'] ?? '');

        if (empty($nro_operacion)) $errores[] = "Debe ingresar el número de operación.";
        if (empty($banco)) $errores[] = "Debe indicar el banco.";
    } else {
        $errores[] = "Debe seleccionar un método de pago.";
    }

    // Si no hay errores
    if (empty($errores)) {
        // Insertar pedido
        $sql = "INSERT INTO pedidos (id_usuario, total, direccion_entrega, tipo_comprobante, correo, ruc, razon_social) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario, $total, $direccion_entrega, $tipo_comprobante, $correo, $ruc, $razon_social]);
        $id_pedido = $pdo->lastInsertId();

        // Insertar detalles del pedido
        foreach ($productos_carrito as $producto) {
            $sql = "INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_pedido, $producto['id_producto'], $producto['cantidad'], $producto['precio']]);
        }

        // Insertar método de pago
        $detalles_pago = ($metodo_pago === 'Tarjeta de Crédito') 
            ? "Tarjeta terminada en $ultimos_digitos"
            : "Depósito en $banco, N° operación: $nro_operacion";

        $sql = "INSERT INTO metodos_pago (id_pedido, metodo_pago, estado_pago, detalles) 
                VALUES (?, ?, 'Pendiente', ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_pedido, $metodo_pago, $detalles_pago]);

        // Vaciar carrito
        $sql = "DELETE FROM carrito WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);

        // Registrar correo simulado en logs
        $asunto = "Confirmación de compra - Pedido #$id_pedido";
        $mensaje = "¡Gracias por tu compra!\n\nDetalles del pedido:\n";
        foreach ($productos_carrito as $p) {
            $mensaje .= "- Producto ID: {$p['id_producto']}, Cantidad: {$p['cantidad']}, Precio: S/ {$p['precio']}\n";
        }
        $mensaje .= "\nTotal: S/ $total\n";
        $mensaje .= "Dirección de entrega: $direccion_entrega\n";
        $mensaje .= "Método de pago: $metodo_pago\n";

        if ($tipo_comprobante === 'boleta') {
            $mensaje .= "Boleta enviada a: $correo\n";
        } elseif ($tipo_comprobante === 'factura') {
            $mensaje .= "Factura a: $razon_social - RUC: $ruc\nEnviada a: $correo\n";
        }

        if (!is_dir('logs')) mkdir('logs', 0777, true);
        file_put_contents("logs/correos.log", "Para: $correo\nAsunto: $asunto\n$mensaje\n\n", FILE_APPEND);

        $exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Checkout - Método de Pago</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<header>
    <img src="images/logo/logo.jpg" alt="Logo" style="max-width:120px;">
</header>

<main>
    <h1>Checkout</h1>

    <?php if ($exito): ?>
        <h2 style="color:green;">✅ ¡Gracias por tu compra!</h2>
        <p>Se ha enviado un correo de confirmación con los detalles de tu pedido.</p>
        <a href="index.php">🏪 Volver a la tienda</a> | 
        <a href="send_opinion.php">📝 Déjanos tu opinión</a>
    <?php else: ?>
        <?php if (!empty($errores)): ?>
            <ul style="color:red;">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="checkout.php" method="POST">
            <!-- Métodos de pago -->
            <label><input type="radio" name="metodo_pago" value="Tarjeta de Crédito" required> Tarjeta de Crédito</label><br>
            <label><input type="radio" name="metodo_pago" value="Pago por Depósito" required> Pago por Depósito</label><br><br>

            <!-- Tarjeta -->
            <div id="tarjeta_container" style="display:none;">
                <label>Número de Tarjeta:</label>
                <input type="text" name="num_tarjeta" maxlength="16"><br>
                <label>Fecha Expiración:</label>
                <input type="month" name="fecha_exp"><br>
                <label>CVV:</label>
                <input type="password" name="cvv" maxlength="4"><br>
            </div>

            <!-- Depósito -->
            <div id="deposito_container" style="display:none;">
                <label>Número de Operación:</label>
                <input type="text" name="nro_operacion"><br>
                <label>Banco:</label>
                <input type="text" name="banco"><br>
            </div>

            <!-- Comprobante -->
            <label>Tipo de comprobante:</label>
            <select name="tipo_comprobante" id="tipo_comprobante" required>
                <option value="">Seleccione</option>
                <option value="boleta">Boleta electrónica</option>
                <option value="factura">Factura</option>
            </select><br><br>

            <!-- Boleta -->
            <div id="email_container" style="display:none;">
                <label>Correo electrónico:</label>
                <input type="email" name="correo">
            </div>

            <!-- Factura -->
            <div id="factura_container" style="display:none;">
                <label>Razón Social:</label>
                <input type="text" name="razon_social"><br>
                <label>RUC:</label>
                <input type="text" name="ruc" maxlength="11"><br>
                <label>Correo electrónico:</label>
                <input type="email" name="correo">
            </div>

            <button type="submit">Confirmar Compra</button>
        </form>
    <?php endif; ?>
</main>

<script>
    // Mostrar campos según método de pago
    document.querySelectorAll('input[name="metodo_pago"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.getElementById('tarjeta_container').style.display = 
                (radio.value === 'Tarjeta de Crédito') ? 'block' : 'none';
            document.getElementById('deposito_container').style.display = 
                (radio.value === 'Pago por Depósito') ? 'block' : 'none';
        });
    });

    // Mostrar campos según comprobante
    const tipoSelect = document.getElementById('tipo_comprobante');
    tipoSelect.addEventListener('change', () => {
        document.getElementById('email_container').style.display = (tipoSelect.value === 'boleta') ? 'block' : 'none';
        document.getElementById('factura_container').style.display = (tipoSelect.value === 'factura') ? 'block' : 'none';
    });
</script>

<footer>
    <p>© 2025 Delicias - Todos los derechos reservados.</p>
</footer>
</body>
</html>
