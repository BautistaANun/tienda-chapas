<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';

if (!isset($_SESSION['usuario'])) {
    mostrarError('Acceso no autorizado');
}

$id = $_SESSION['usuario']['id'];

$stmt = $pdo->prepare("
    UPDATE usuarios
    SET activo = 0
    WHERE id = ?
");
$stmt->execute([$id]);

// Cerrar sesión
session_destroy();

header('Location: index.php?baja=ok');
exit;
