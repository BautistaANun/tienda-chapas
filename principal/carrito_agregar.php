<?php
session_start();
require __DIR__ . '/../config/database.php';

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

/* Buscar producto */
$stmt = $pdo->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: index.php');
    exit;
}

/* Inicializar carrito si no existe */
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

/* Agregar o incrementar */
if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]['cantidad']++;
} else {
    $_SESSION['carrito'][$id] = [
        'id' => $producto['id'],
        'nombre' => $producto['nombre'],
        'precio' => $producto['precio'],
        'imagen' => $producto['imagen'],
        'cantidad' => 1
    ];
}

header('Location: carrito.php');
exit;
