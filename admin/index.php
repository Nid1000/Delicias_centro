<?php
include 'db.php';

// Tab seleccionado
$tab = $_GET['tab'] ?? 'productos';
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre");
$productos = $conn->query("SELECT p.*, c.nombre AS categoria 
                           FROM productos p LEFT JOIN categorias c 
                           ON p.categoria_id=c.id_categoria ORDER BY p.id_producto DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin Delicias</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container my-4">
  <h2>Panel Administrador</h2>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $tab=='categorias'?'active':''?>" href="?tab=categorias">Categorías</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab=='productos'?'active':''?>" href="?tab=productos">Productos</a></li>
  </ul>

<?php if ($tab=='categorias'): ?>
  <!-- NUEVA CATEGORÍA -->
  <h4>Nueva Categoría</h4>
  <form method="post" action="categorias.php" class="row g-2 mb-3">
    <input type="hidden" name="accion" value="crear">
    <div class="col-auto">
      <input name="nombre" class="form-control" placeholder="Nombre categoría" required>
    </div>
    <div class="col-auto"><button class="btn btn-primary">Agregar</button></div>
  </form>

  <!-- LISTA DE CATEGORÍAS -->
  <h4>Lista de Categorías</h4>
  <table class="table table-bordered">
    <tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr>
    <?php while($c=$categorias->fetch_assoc()): ?>
    <tr>
      <td><?=$c['id_categoria']?></td>
      <td><?=$c['nombre']?></td>
      <td>
        <a class="btn btn-sm btn-warning" href="index.php?tab=categorias&edit=<?=$c['id_categoria']?>">Editar</a>
        <a class="btn btn-sm btn-danger" href="categorias.php?del=<?=$c['id_categoria']?>" onclick="return confirm('¿Eliminar categoría?')">Eliminar</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

  <!-- FORMULARIO DE EDICIÓN -->
  <?php if (isset($_GET['edit'])): 
    $idEditar=intval($_GET['edit']);
    $catEditar=$conn->query("SELECT * FROM categorias WHERE id_categoria=$idEditar")->fetch_assoc();
  ?>
    <h4>Editar Categoría</h4>
    <form method="post" action="categorias.php" class="row g-2 mb-3">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id_categoria" value="<?=$catEditar['id_categoria']?>">
      <div class="col-auto">
        <input name="nombre" class="form-control" value="<?=$catEditar['nombre']?>" required>
      </div>
      <div class="col-auto"><button class="btn btn-success">Guardar cambios</button></div>
      <div class="col-auto"><a href="index.php?tab=categorias" class="btn btn-secondary">Cancelar</a></div>
    </form>
  <?php endif; ?>
<?php endif; ?>



<?php if ($tab=='productos'): ?>
  <h4>Nuevo Producto</h4>
  <form method="post" action="productos.php" class="row g-2 mb-3">
    <input type="hidden" name="accion" value="crear">
    <div class="col-md-3"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
    <div class="col-md-2"><input name="precio" type="number" step="0.01" class="form-control" placeholder="Precio" required></div>
    <div class="col-md-3">
      <select name="categoria_id" class="form-select" required>
        <?php
        $cats=$conn->query("SELECT * FROM categorias");
        while($cat=$cats->fetch_assoc()){
          echo "<option value='{$cat['id_categoria']}'>{$cat['nombre']}</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-3"><input name="descripcion" class="form-control" placeholder="Descripción"></div>
    <div class="col-md-1 form-check mt-2">
      <input type="checkbox" name="disponible" class="form-check-input" checked> Disponible
    </div>
    <div class="col-12"><button class="btn btn-primary mt-2">Agregar</button></div>
  </form>

  <h4>Lista de Productos</h4>
  <table class="table table-bordered">
    <tr>
      <th>ID</th><th>Nombre</th><th>Precio</th><th>Categoría</th><th>Disponible</th><th>Acciones</th>
    </tr>
    <?php 
    $productos=$conn->query("SELECT p.*, c.nombre as catNombre 
                              FROM productos p 
                              LEFT JOIN categorias c ON p.categoria_id=c.id_categoria");
    while($p=$productos->fetch_assoc()): ?>
    <tr>
      <td><?=$p['id_producto']?></td>
      <td><?=$p['nombre']?></td>
      <td><?=$p['precio']?></td>
      <td><?=$p['catNombre']?></td>
      <td><?=$p['disponible']?'Sí':'No'?></td>
      <td>
        <a class="btn btn-sm btn-warning" href="index.php?tab=productos&edit=<?=$p['id_producto']?>">Editar</a>
        <a class="btn btn-sm btn-danger" href="productos.php?del=<?=$p['id_producto']?>" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

  <?php if (isset($_GET['edit'])): 
    $idEditar=intval($_GET['edit']);
    $prodEditar=$conn->query("SELECT * FROM productos WHERE id_producto=$idEditar")->fetch_assoc();
  ?>
    <h4>Editar Producto</h4>
    <form method="post" action="productos.php" class="row g-2 mb-3">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id_producto" value="<?=$prodEditar['id_producto']?>">
      <div class="col-md-3"><input name="nombre" class="form-control" value="<?=$prodEditar['nombre']?>" required></div>
      <div class="col-md-2"><input name="precio" type="number" step="0.01" class="form-control" value="<?=$prodEditar['precio']?>" required></div>
      <div class="col-md-3">
        <select name="categoria_id" class="form-select" required>
          <?php
          $cats=$conn->query("SELECT * FROM categorias");
          while($cat=$cats->fetch_assoc()){
            $sel = $cat['id_categoria']==$prodEditar['categoria_id']?'selected':'';
            echo "<option value='{$cat['id_categoria']}' $sel>{$cat['nombre']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-3"><input name="descripcion" class="form-control" value="<?=$prodEditar['descripcion']?>"></div>
      <div class="col-md-1 form-check mt-2">
        <input type="checkbox" name="disponible" class="form-check-input" <?=$prodEditar['disponible']?'checked':''?>> Disponible
      </div>
      <div class="col-12 mt-2">
        <button class="btn btn-success">Guardar cambios</button>
        <a href="index.php?tab=productos" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
<?php endif; ?>


</body>
</html>
