<?php
session_start();
include 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$errores = [];
$exito = '';
$producto = null;
$id_producto = $_GET['id_producto'] ?? null; // Obtener ID del producto desde URL

if (!$id_producto) {
    die("ID de producto no proporcionado.");
}

// Obtener detalles del producto
try {
    $sql = "SELECT * FROM productos WHERE id_producto = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Producto no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al obtener producto: " . $e->getMessage());
}

// Obtener opiniones existentes del producto (para mostrarlas)
$opiniones = [];
try {
    $sql_opiniones = "SELECT o.*, u.nombre AS nombre_usuario 
                      FROM opiniones o 
                      JOIN usuarios u ON o.id_usuario = u.id_usuario 
                      WHERE o.id_producto = ? 
                      ORDER BY o.fecha_opinion DESC 
                      LIMIT 10";
    $stmt_opiniones = $pdo->prepare($sql_opiniones);
    $stmt_opiniones->execute([$id_producto]);
    $opiniones = $stmt_opiniones->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errores[] = "Error al cargar opiniones: " . $e->getMessage();
}

// Verificar si el usuario ya dejó una opinión para este producto
$ya_opino = false;
try {
    $sql_check = "SELECT id_opinion FROM opiniones WHERE id_usuario = ? AND id_producto = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$_SESSION['id_usuario'], $id_producto]);
    $ya_opino = $stmt_check->fetch() !== false;
} catch (PDOException $e) {
    $errores[] = "Error al verificar opinión: " . $e->getMessage();
}

// Procesar envío de opinión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$ya_opino) {
    $mensaje = trim($_POST['mensaje'] ?? '');
    $calificacion = intval($_POST['calificacion'] ?? 0); // 1-5

    if (empty($mensaje)) {
        $errores[] = "Por favor, escribe una opinión.";
    } elseif (strlen($mensaje) > 1000) {
        $errores[] = "La opinión es demasiado larga. Máximo 1000 caracteres.";
    } elseif ($calificacion < 1 || $calificacion > 5) {
        $calificacion = null; // Si es inválida, no guardar
    }

    if (empty($errores)) {
        try {
            $sql = "INSERT INTO opiniones (id_usuario, id_producto, mensaje, calificacion) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['id_usuario'], $id_producto, $mensaje, $calificacion]);
            $exito = "¡Tu opinión ha sido enviada!";
            
            // Recargar opiniones después del insert
            header("Location: opinion.php?id_producto=" . $id_producto);
            exit();
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errores[] = "Ya has dejado una opinión para este producto.";
            } else {
                $errores[] = "Error al guardar opinión: " . $e->getMessage();
            }
        }
    }
} elseif ($ya_opino && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errores[] = "Ya has dejado una opinión para este producto. Solo se permite una por usuario.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Deja tu Opinión - Delicias</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f8f8f8; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .error { color: red; background: #ffe6e6; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .success { color: green; background: #e6ffe6; padding: 10px; border-radius: 4px; margin: 10px 0; }
        textarea { width: 100%; height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .estrellas { color: #ffc107; font-size: 20px; }
        .opinion { border-bottom: 1px solid #eee; padding: 10px 0; margin: 10px 0; }
        .opinion:last-child { border-bottom: none; }
        .ya_opino { color: orange; font-weight: bold; }
        a { text-decoration: none; color: #007bff; }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        input[type="radio"] { margin-right: 5px; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Deja tu Opinión</h1>
        <p><a href="catalogo.php">← Volver al catálogo</a></p>
    </header>

    <section>
        <h2>Producto: <?php echo htmlspecialchars($producto['nombre']); ?></h2>
        <p><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 2); ?></p>

        <?php if (!empty($errores)): ?>
            <div class="error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="success"><?php echo htmlspecialchars($exito); ?></div>
        <?php endif; ?>

        <?php if ($ya_opino): ?>
            <p class="ya_opino">Ya has dejado una opinión para este producto.</p>
        <?php else: ?>
            <form action="opinion.php?id_producto=<?php echo $id_producto; ?>" method="POST">
                <label for="calificacion">Calificación (opcional, 1-5 estrellas):</label>
                <div class="estrellas">
                    <label><input type="radio" name="calificacion" value="5" required> ★★★★★</label><br>
                    <label><input type="radio" name="calificacion" value="4"> ★★★★☆</label><br>
                    <label><input type="radio" name="calificacion" value="3"> ★★★☆☆</label><br>
                    <label><input type="radio" name="calificacion" value="2"> ★★☆☆☆</label><br>
                    <label><input type="radio" name="calificacion" value="1"> ★☆☆☆☆</label><br>
                    <label><input type="radio" name="calificacion" value="0" checked> Sin calificación</label>
                </div><br>
                
                <label for="mensaje">Tu Opinión:</label><br>
                <textarea id="mensaje" name="mensaje" required placeholder="Comparte tu experiencia con este producto..."><?php echo htmlspecialchars($_POST['mensaje'] ?? ''); ?></textarea><br><br>
                <button type="submit">Enviar Opinión</button>
            </form>
        <?php endif; ?>

        <h3>Opiniones de otros usuarios (<?php echo count($opiniones); ?>)</h3>
        <?php if (empty($opiniones)): ?>
            <p>Aún no hay opiniones para este producto.</p>
        <?php else: ?>
            <?php foreach ($opiniones as $op): ?>
                <div class="opinion">
                    <strong><?php echo htmlspecialchars($op['nombre_usuario']); ?></strong> 
                    <?php if ($op['calificacion']): ?>
                        <span class="estrellas"><?php echo str_repeat('★', $op['calificacion']) . str_repeat('☆', 5 - $op['calificacion']); ?></span>
                    <?php endif; ?><br>
                    <em>Fecha: <?php echo date('d/m/Y H:i', strtotime($op['fecha_opinion'])); ?></em><br>
                    <p><?php echo nl2br(htmlspecialchars($op['mensaje'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
