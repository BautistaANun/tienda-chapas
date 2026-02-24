<?php
// Conexión a base de datos y funciones auxiliares
session_start();

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

// Array para almacenar errores de validación
$errores = [];


// Procesamos el registro únicamente si el formulario fue enviado por POST
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

     // Sanitizamos los datos eliminando espacios innecesarios
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

     // Validación básica de campos obligatorios
    if ($nombre === '' || $email === '' || $password === '') {
        $errores[] = 'Todos los campos son obligatorios';
    }


    // Validación de formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Email inválido';
    }

    if (empty($errores)) {

        // Consulta preparada para verificar si el email ya existe
        // Previene SQL Injection
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errores[] = 'El email ya está registrado';
        } else {

            // Generamos un hash seguro de la contraseña
            // Nunca almacenamos contraseñas en texto plano
          
            $hash = password_hash($password, PASSWORD_DEFAULT);

             // Insertamos el nuevo usuario usando consulta preparada
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre, email, password)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$nombre, $email, $hash]);
            // Mensaje flash para mostrar en la siguiente petición
            setFlash('success', 'Cuenta creada correctamente. Ahora podés iniciar sesión.');
            //Redirigimos para evitar envío de formulario PRG Pattern: (Post-Redirect-Get Pattern)
            header('Location: login.php');
            exit;
        }
    }
// En producción podría agregarse:
// - Validación de fortaleza de contraseña
// - Confirmación por email
}
?>
<link rel="stylesheet" href="/tienda-chapas/assets/css/styles.css">

<main class="contenedor">
    <div class="auth-container">

        <h1>Crear cuenta</h1>

        <?php foreach ($errores as $error): ?>
            <p class="auth-error"><?= e($error) ?></p>
            
        <?php endforeach; ?>

        <form method="post" class="auth-form">
            <input
                type="text"
                name="nombre"
                placeholder="Nombre"
                value="<?= e($_POST['nombre'] ?? '') ?>"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="Email"
                value="<?= e($_POST['email'] ?? '') ?>"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Contraseña"
                required
            >

            <button type="submit" class="btn-principal">
                Registrarme
            </button>
        </form>

        <div class="auth-links">
            <a href="login.php">Ya tengo cuenta</a>
            <a href="../index.php">← Volver a la tienda</a>
        </div>

    </div>
</main>
