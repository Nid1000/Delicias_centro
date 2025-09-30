<?php
session_start();
include 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Guardar la nueva dirección en la base de datos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_direccion = trim($_POST['direccion']);
    if (!empty($nueva_direccion)) {
        // Guardar en la base de datos
        $sql = "UPDATE usuarios SET direccion = ? WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nueva_direccion, $id_usuario]);

        // Guardar en la sesión
        $_SESSION['direccion'] = $nueva_direccion;

        // Redirigir a entregas.php o checkout
        header("Location: entregas.php");
        exit();
    } else {
        $error = "Por favor, ingresa una dirección válida.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agregar Nueva Dirección</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin:0; padding:0; }
header { background-color: #ff9900; padding: 15px; display:flex; align-items:center; color:white; }
header img { max-width:120px; }
main { max-width:500px; margin:40px auto; background:#fff; padding:30px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h1 { text-align:center; color:#333; margin-bottom:20px;}
label { font-weight:bold; }
input[type="text"] { width:100%; padding:10px; margin:10px 0 20px 0; border-radius:5px; border:1px solid #ccc; }
button { width:100%; padding:12px; background-color:#ff9900; color:white; border:none; border-radius:5px; font-size:1.1em; }
button:hover { background-color:#cc7a00; }
.error { color:red; text-align:center; margin-bottom:10px; }
</style>
</head>
<body>
<header>
    <img src="images/logo/logo.jpg" alt="Logo">
</header>
<main>
<h1>Agregar Nueva Dirección</h1>

<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<form action="" method="POST">
    <label for="direccion">Dirección de entrega:</label>
    <input type="text" id="direccion" name="direccion" placeholder="Ej: Av. Siempre Viva 123" required>
    <button type="submit">Guardar Dirección</button>
</form>
</main>
</body>
</html>
