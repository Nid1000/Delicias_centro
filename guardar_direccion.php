<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se envió la dirección
if (isset($_POST['direccion'])) {
    // Guardar la dirección en la sesión
    $_SESSION['direccion'] = $_POST['direccion'];

    // Redirigir al usuario a la página de entregas
    header("Location: entregas.php");
    exit();
} else {
    echo "Dirección no válida.";
    exit();
}
?>
