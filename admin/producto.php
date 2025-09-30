<?php
include 'db.php';

/* ========== CREAR PRODUCTO ========== */
if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $precio = floatval($_POST['precio']);
  $descripcion = $conn->real_escape_string($_POST['descripcion']);
  $categoria_id = intval($_POST['categoria_id']);
  $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
  $disponible = isset($_POST['disponible']) ? 1 : 0;

  $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria_id, cantidad, disponible, id_admin)
          VALUES ('$nombre','$descripcion',$precio,$categoria_id,$cantidad,$disponible,1)";
  $conn->query($sql);

  header('Location: index.php?tab=productos');
  exit;
}

/* ========== EDITAR PRODUCTO ========== */
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
  $id = intval($_POST['id_producto']);
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $precio = floatval($_POST['precio']);
  $descripcion = $conn->real_escape_string($_POST['descripcion']);
  $categoria_id = intval($_POST['categoria_id']);
  $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
  $disponible = isset($_POST['disponible']) ? 1 : 0;

  $sql = "UPDATE productos SET 
            nombre='$nombre',
            descripcion='$descripcion',
            precio=$precio,
            categoria_id=$categoria_id,
            cantidad=$cantidad,
            disponible=$disponible
          WHERE id_producto=$id";
  $conn->query($sql);

  header('Location: index.php?tab=productos');
  exit;
}

/* ========== ELIMINAR PRODUCTO ========== */
if (isset($_GET['del'])) {
  $id = intval($_GET['del']);
  $conn->query("DELETE FROM productos WHERE id_producto=$id");
  header('Location: index.php?tab=productos');
  exit;
}
?>
