<?php
session_start();

// Destruir la sesión
session_destroy();
// Destruir todas las variables de sesión
session_unset();

// Redirigir al usuario al inicio de sesión
header("Location: login.php");
exit();
?>
