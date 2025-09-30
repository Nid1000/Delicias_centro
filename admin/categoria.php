<?php
include 'db.php';

// Crear categoría
if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $conn->query("INSERT INTO categorias (nombre) VALUES ('$nombre')");
  header('Location: index.php?tab=categorias');
  exit;
}

// Actualizar categoría
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
  $id = intval($_POST['id_categoria']);
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $conn->query("UPDATE categorias SET nombre='$nombre' WHERE id_categoria=$id");
  header('Location: index.php?tab=categorias');
  exit;
}

// Eliminar categoría
if (isset($_GET['del'])) {
  $id = intval($_GET['del']);
  $conn->query("DELETE FROM categorias WHERE id_categoria=$id");
  header('Location: index.php?tab=categorias');
  exit;
}
header('Location: index.php?tab=categorias');
exit;
?>
