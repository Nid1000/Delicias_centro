<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "delicias_centro";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Error conexión BD: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
