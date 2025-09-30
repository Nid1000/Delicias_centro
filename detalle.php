<?php
session_start();
include 'db.php';

$nombre_archivo = isset($_GET['producto']) ? urldecode($_GET['producto']) : '';
if (empty($nombre_archivo)) { header('Location: catalogo.php'); exit; }

$ruta_local = "images/productos/";
$ext='';
if (file_exists($ruta_local.$nombre_archivo.".jpg")) $ext='.jpg';
elseif (file_exists($ruta_local.$nombre_archivo.".png")) $ext='.png';
else die("Error: Producto no encontrado.");

$productos_bd=[];
if (isset($pdo)) {
  $sql="SELECT * FROM productos WHERE disponible=1";
  $stmt=$pdo->query($sql);
  $productos_bd=$stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtener_datos_producto($nombre_archivo,$productos_bd){
  $nombre_producto=pathinfo($nombre_archivo,PATHINFO_FILENAME);
  foreach($productos_bd as $prod_bd){
    if(pathinfo($prod_bd['imagen'],PATHINFO_FILENAME)===$nombre_producto){
      return [
        'nombre'=>$prod_bd['nombre'],
        'precio'=>$prod_bd['precio'],
        'descripcion'=>$prod_bd['descripcion']??'Producto especial de nuestra panadería.',
        'id_producto'=>$prod_bd['id_producto']??0
      ];
    }
  }
  return [
    'nombre'=>ucwords(str_replace('_',' ',$nombre_producto)),
    'precio'=>8.00,
    'descripcion'=>'Producto especial de nuestra panadería, elaborado con ingredientes de calidad.',
    'id_producto'=>0
  ];
}

$datos=obtener_datos_producto($nombre_archivo,$productos_bd);
$nombre_producto=htmlspecialchars($datos['nombre']);
$id_producto_bd=$datos['id_producto'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo $nombre_producto;?> - Detalle</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<style>
body{background:#f9f5e7;font-family:Arial;}
.header-bar{background:#ffd580;border-bottom:2px solid #e6b800;}
.detalle-container{max-width:1100px;margin:40px auto;background:#fff;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);overflow:hidden;}
.detalle-row{display:flex;flex-wrap:wrap;}
.detalle-img{flex:1 1 45%;background:#fff;text-align:center;padding:20px;}
.detalle-img img{max-width:100%;border-radius:10px;}
.detalle-info{flex:1 1 55%;padding:30px;display:flex;flex-direction:column;justify-content:center;}
.detalle-info h1{font-size:28px;color:#663300;margin-bottom:10px;}
.detalle-info h3{color:#e6b800;margin-bottom:20px;}
.rating-stars i{font-size:25px;color:#ccc;cursor:pointer;margin-right:5px;}
.rating-stars i.selected,
.rating-stars i.hover{color:#f5c518;}
.descripcion{font-size:16px;color:#555;line-height:1.5;margin-top:15px;}
.cantidad-group{margin-top:20px;}
.btn-primary{background:#ffcc66;border-color:#e6b800;color:#663300;}
.btn-primary:hover{background:#e6b800;color:#fff;}
@media(max-width:768px){
  .detalle-row{flex-direction:column;}
}
</style>
</head>
<body>

<header class="header-bar d-flex justify-content-between align-items-center flex-wrap p-3">
  <div><img src="images/logo/logo.jpg" alt="Logo" style="max-width:120px; height:auto;" /></div>
  <div class="d-flex align-items-center gap-3">
    <a href="mi_cuenta.php" class="btn btn-primary"><?php echo isset($_SESSION['nombre'])?$_SESSION['nombre']:'Mi Cuenta';?></a>
    <a href="cart.php" class="btn btn-primary">Mi carrito</a>
  </div>
</header>

<div class="detalle-container">
  <div class="detalle-row">
    <div class="detalle-img">
      <img src="<?php echo $ruta_local.$nombre_archivo.$ext;?>" alt="<?php echo $nombre_producto;?>">
    </div>
    <div class="detalle-info">
      <h1><?php echo $nombre_producto;?></h1>
      <h3>Precio: S/ <?php echo number_format($datos['precio'],2);?></h3>

      <!-- Estrellas -->
      <div class="rating-stars mb-3" id="rating-stars">
        <i class="fa fa-star" data-value="1"></i>
        <i class="fa fa-star" data-value="2"></i>
        <i class="fa fa-star" data-value="3"></i>
        <i class="fa fa-star" data-value="4"></i>
        <i class="fa fa-star" data-value="5"></i>
      </div>

      <p class="descripcion"><?php echo htmlspecialchars($datos['descripcion']);?></p>

      <form action="add_to_cart.php" method="POST" class="cantidad-group">
        <input type="hidden" name="producto" value="<?php echo htmlspecialchars($nombre_archivo);?>">
        <?php if($id_producto_bd>0):?><input type="hidden" name="id_producto" value="<?php echo $id_producto_bd;?>"><?php endif;?>
        <label class="mb-2">Cantidad:</label>
        <input type="number" name="cantidad" min="1" value="1" required style="width:70px;" class="form-control d-inline-block">
        <button type="submit" class="btn btn-primary mt-3">Agregar al carrito</button>
      </form>

      <a href="catalogo.php" class="btn btn-secondary mt-3">Volver al Catálogo</a>
    </div>
  </div>
</div>

<script>
const stars=document.querySelectorAll('#rating-stars i');
let selectedRating=0;
stars.forEach(star=>{
  star.addEventListener('mouseover',()=>{
    resetStars();
    const val=parseInt(star.dataset.value);
    stars.forEach(s=>{if(parseInt(s.dataset.value)<=val)s.classList.add('hover');});
  });
  star.addEventListener('mouseout',()=>{resetStars();setStars(selectedRating);});
  star.addEventListener('click',()=>{
    selectedRating=parseInt(star.dataset.value);
    setStars(selectedRating);
    // Aquí podrías hacer un fetch() para guardar en la BD
    console.log("Rating seleccionado:",selectedRating);
  });
});
function resetStars(){stars.forEach(s=>s.classList.remove('hover','selected'));}
function setStars(val){stars.forEach(s=>{if(parseInt(s.dataset.value)<=val)s.classList.add('selected');});}
</script>

</body>
</html>
