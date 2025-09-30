<?php
include 'db.php'; // Incluir la conexión a la base de datos
$registro_exitoso = false; // Variable para controlar si el registro fue exitoso
$error = ""; // Variable para errores

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $contrasena = $_POST['contrasena'];

    // Verificar que los campos obligatorios estén completos
    if (!empty($nombre) && !empty($email) && !empty($direccion) && !empty($contrasena)) {

        // Validar teléfono (solo números y longitud 9)
        if (!empty($telefono)) { // Si se ingresó teléfono, validar
            if (!preg_match('/^\d{9}$/', $telefono)) {
                $error = "El teléfono debe contener exactamente 9 números.";
            }
        }

        if (empty($error)) {
            // Encriptar la contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            $sql = "INSERT INTO usuarios (nombre, email, telefono, direccion, contrasena) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $email, $telefono, $direccion, $contrasena_hash]);

            // Cambiar a true si el registro fue exitoso
            $registro_exitoso = true;
        }

    } else {
        $error = "Por favor, completa todos los campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <title>Registro - Delicias</title>

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
            gap: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Logo */
        header .logo img {
            height: 40px;
        }

        header h1 {
            color: white;
            margin: 0;
            font-weight: normal;
        }

        /* Sección debajo del header con margen */
        #registro-section {
            max-width: 400px;
            margin: 40px auto;
            background-color: #fff2cc;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: #4b3b00;
        }

        #registro-section h2 {
            text-align: center;
            color: #a06e00;
            margin-bottom: 20px;
        }

        #registro-section label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        #registro-section input[type="text"],
        #registro-section input[type="email"],
        #registro-section input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 15px;
            border: 1px solid #f9d777;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }

        /* Aquí agregamos validación visual para teléfono si es inválido */
        #telefono:invalid {
            border-color: red;
        }

        #registro-section button {
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

        #registro-section button:hover {
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

        /* Notificación emergente */
        .notificacion-exitosa {
            display: none; /* Oculta la notificación por defecto */
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4caf50;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            z-index: 1000;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            text-align: center;
        }

        .notificacion-exitosa button {
            background-color: #ffffff;
            color: #4caf50;
            border: none;
            padding: 5px 12px;
            cursor: pointer;
            border-radius: 3px;
            margin-top: 10px;
            font-weight: bold;
        }

        .notificacion-exitosa button:hover {
            background-color: #f1f1f1;
        }

        /* Texto debajo del formulario */
        #registro-section p {
            text-align: center;
            margin-top: 15px;
        }
        #h1{
            font-family: 'Roboto', sans-serif;
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
        <h1>Creación de cuenta</h1>
    </header>

    <section id="registro-section">
        <h2>¡Bienvenido! </h2>
        <h2>Crea tu cuenta y empieza a disfrutar de nuestros productos</h2>
        <h2>__________________________</h2>

        <?php if (!empty($error)) : ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST" novalidate>
            <label for="nombre">¿Cómo te llamas?</label>
            <label for="nombre">Escribe tu nombre completo :</label>
            <input type="text" id="nombre" name="nombre" required />

            <label for="email">¿Cuál es tu correo electrónico?:</label>
            <input type="email" id="email" name="email" required />

            <label for="telefono">¿Nos das tu número de teléfono?:</label>
            <input
                type="text"
                id="telefono"
                name="telefono"
                pattern="\d{9}"
                maxlength="9"
                title="Por favor ingresa exactamente 9 números"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,9);"
            />

            <label for="direccion">¿Dónde te encontramos? Ingresa tu dirección de envío:</label>
            <input type="text" id="direccion" name="direccion" required />

            <label for="contrasena">Crea una contraseña segura:</label>
            <input type="password" id="contrasena" name="contrasena" required />
            <p>¡Queremos conocerte por completo!</p>

            <button type="submit">¡Crear mi cuenta y empezar a comprar!</button>
        </form>
        <p>¿Ya eres miembro? <a href="login.php">¡Inicia sesión y empieza a comprar!</a></p>
    </section>

    <!-- Notificación emergente -->
    <?php if ($registro_exitoso) : ?>
        <div class="notificacion-exitosa" id="notificacion-exitosa">
            <p>¡Registro exitoso! Ahora puedes iniciar sesión.</p>
            <button onclick="window.location.href='login.php'">Ir a Iniciar sesión</button>
        </div>
    <?php endif; ?>

    <script>
        // Mostrar la notificación si el registro fue exitoso
        <?php if ($registro_exitoso) : ?>
            document.getElementById('notificacion-exitosa').style.display = 'block';
        <?php endif; ?>
    </script>
</body>

</html>
