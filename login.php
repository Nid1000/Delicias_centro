<?php
session_start();
include 'db.php'; // Incluir conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $email = trim($_POST['email']);
    $contrasena = $_POST['contrasena'];

    // Buscar usuario por email
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar credenciales
    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        // Iniciar sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        header("Location: mi_cuenta.php");
        exit();
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar sesión - Delicias</title>

    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #4b3b00;
            margin: 0;
            padding: 0;
        }

        /* Header con menú - fondo naranja fuerte */
        header {
            background-color: #c9982d;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Logo */
        header .logo img {
            height: 40px;
        }

        /* Menú de navegación */
        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Sección debajo del header con texto informativo */
        .banner-info {
            background-color: #f9d777;
            padding: 20px 40px;
            color: #4b3b00;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 1200px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Encabezados */
        h1,
        h2 {
            color: #a06e00;
            text-align: center;
        }

        /* Formulario de login */
        #login-section {
            max-width: 400px;
            margin: 40px auto;
            background-color: #fff2cc;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: #4b3b00;
        }

        #login-section label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        #login-section input[type="email"],
        #login-section input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 15px;
            border: 1px solid #f9d777;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }

        #login-section button {
            width: 100%;
            padding: 10px;
            background-color: #f9d777;
            border: none;
            font-weight: bold;
            font-size: 1.1em;
            border-radius: 5px;
            cursor: pointer;
            color: #4b3b00;
        }

        #login-section button:hover {
            background-color: #c9982d;
            color: white;
        }

        /* Enlaces */
        a {
            color: #a06e00;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Mensajes de error */
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>

</head>

<body>
    <header>
        <div class="logo">
            <a href="index.php">
                <img src="images/logo/logo.jpg" alt="Logo de Delicias Del Centro" />
            </a>
        </div>

        <nav>
            <a href="index.php">Inicio</a>
            <a href="catalogo.php">Productos</a>
            <li class="nav-item">
            <a class="nav-link" href="https://www.facebook.com/deliciashuancayoperu" target="_blank">
            <i class="bi bi-facebook"></i> Nuestras Redes</a>
            </li>
        </nav>
    </header>

    <section class="banner-info">
        <p>La panadería <strong>Delicias del Centro</strong>, ubicada en el corazón de la ciudad y conocida como
            <em>El horno de tradición</em>, celebra su centenario. Desde sus inicios, se ha dedicado con ilusión a la
            elaboración de pan artesano, utilizando ingredientes puramente naturales.</p>
    </section>

    <h1>Delicias Del Centro</h1>

    <section id="login-section">
        <h2>🔐 Inicio de Sesión</h2>

        <?php if (!empty($error)) : ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" novalidate>
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required autocomplete="email" />

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password" />

            <button type="submit">🚀 Iniciar sesión</button>
        </form>

        <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </section>
</body>

</html>
