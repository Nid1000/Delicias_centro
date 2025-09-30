<?php
session_start();
include 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dirección de Entrega</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos generales */
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #ff9900;
            padding: 15px 20px;
            display: flex;
            align-items: center;
        }
        header .logo img {
            max-width: 120px;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 2em;
            color: #333;
        }
        .direccion-existente, .boton-agregar {
            text-align: center;
            margin-top: 30px;
        }
        button {
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
        }
        button:hover {
            background-color: #0056b3;
        }
        .button {
            text-decoration: none;
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-align: center;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header con el logo -->
    <header>
        <div class="logo">
            <img src="images/logo/logo.jpg" alt="Logo de Delicias Del Centro">
        </div>
    </header>

    <h1>Dirección de Entrega</h1>

    <?php if (isset($_SESSION['direccion'])): ?>
        <div class="direccion-existente">
            <p>Tu dirección de entrega actual es:</p>
            <p><strong><?php echo htmlspecialchars($_SESSION['direccion']); ?></strong></p>
            <a href="entregas.php" class="button">Continuar con la entrega</a>
        </div>
        <div class="boton-agregar">
            <form action="nueva_direccion.php" method="GET">
                <button type="submit">Agregar Nueva Dirección</button>
            </form>
        </div>
    <?php else: ?>
        <form action="guardar_direccion.php" method="POST">
            <label for="direccion">Dirección de entrega:</label>
            <input type="text" id="direccion" name="direccion" required>
            <br><br>
            <button type="submit">Guardar Dirección</button>
        </form>
    <?php endif; ?>

    <footer>
        <p>&copy; 2025 Delicias Del Centro</p>
    </footer>
</body>
</html>
