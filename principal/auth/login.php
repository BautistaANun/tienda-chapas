<?php
session_start();
require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

$errores = [];
// Procesamos el login únicamente si el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
// Consulta preparada para prevenir SQL Injection
    $stmt = $pdo->prepare("
        SELECT * FROM usuarios
        WHERE email = ? AND activo = 1
    ");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificación segura de contraseña hasheada
    if (!$usuario || !password_verify($password, $usuario['password'])) {
        $errores[] = 'Email o contraseña incorrectos';
    } else {
        // Guardamos solo los datos necesarios del usuario autenticado
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol']
        ];

        setFlash('success', 'Sesión iniciada correctamente');
        header('Location: ../index.php');
        exit;
    }
}

//Posibles mejoras: 
// Regeneramos el ID de sesión tras autenticación exitosa
// para prevenir ataques de session fixation
//session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="/tienda-chapas/assets/css/styles.css">

</head>
<body>

<main class="contenedor">
    <div class="auth-container">
        <h1>Iniciar sesión</h1>

        <?php if (!empty($errores)): ?>
            <?php foreach ($errores as $error): ?>
                <p class="auth-error"><?= e($error) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>

            <button type="submit" class="btn-principal">
                Ingresar
            </button>
        </form>

        <div class="auth-links">
            <a href="registro.php">Crear cuenta</a>
            <a href="../index.php">← Volver a la tienda</a>
        </div>
    </div>
</main>

</body>
</html>
