<?php
session_start();
require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || $email === '' || $password === '') {
        $errores[] = 'Todos los campos son obligatorios';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Email inválido';
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errores[] = 'El email ya está registrado';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre, email, password)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$nombre, $email, $hash]);

            setFlash('success', 'Cuenta creada correctamente. Ahora podés iniciar sesión.');
            header('Location: login.php');
            exit;
        }
    }
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
