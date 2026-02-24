<?php
session_start();

if (
    !isset($_SESSION['usuario']) ||
    $_SESSION['usuario']['rol'] !== 'superadmin'
) {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

/* =========================
   DATOS
========================= */

$nombre   = trim($_POST['nombre'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$activo   = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;

$errores = [];

/* =========================
   VALIDACIONES
========================= */

if ($nombre === '' || $email === '' || $password === '') {
    $errores[] = 'Todos los campos son obligatorios';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'Email inválido';
}

if (strlen($password) < 6) {
    $errores[] = 'La contraseña debe tener al menos 6 caracteres';
}

if (!in_array($activo, [0,1], true)) {
    $errores[] = 'Valor de activo inválido';
}

if (!empty($errores)) {
    setFlash('error', implode(' | ', $errores));
    header('Location: nuevoAdmin.php');
    exit;
}

/* =========================
   EMAIL ÚNICO
========================= */

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    setFlash('error', 'El email ya está registrado');
    header('Location: nuevoAdmin.php');
    exit;
}

/* =========================
   CREAR ADMIN
========================= */

try {

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nombre, email, password, rol, activo)
        VALUES (?, ?, ?, 'admin', ?)
    ");

    $stmt->execute([$nombre, $email, $hash, $activo]);

    setFlash('success', 'Administrador creado correctamente');

} catch (PDOException $e) {

    setFlash('error', 'Error al crear el administrador');
}

header('Location: nuevoAdmin.php');
exit;