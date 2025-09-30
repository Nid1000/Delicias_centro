<?php
$host = 'localhost';  // O tu host, si es un servidor remoto
$dbname = 'delicias_centro'; // Nombre de tu base de datos
$username = 'root';   // Tu usuario de la base de datos
$password = '';       // Tu contraseña de la base de datos

try {
    // Crear conexión
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configurar el PDO para que lance excepciones en caso de error
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    die();
}


?>
