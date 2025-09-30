<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave   = $_POST['clave'];
    // Clave fija de ejemplo:
    if ($usuario === 'admin' && $clave === '1234') {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Usuario o clave incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<title>Login Admin</title>
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">
<h3>Acceso Administrador</h3>
<?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
<div class="mb-3"><label>Usuario</label><input name="usuario" class="form-control"></div>
<div class="mb-3"><label>Clave</label><input type="password" name="clave" class="form-control"></div>
<button class="btn btn-primary">Entrar</button>
</form>
</div>
</div>
</div>
</body>
</html>
