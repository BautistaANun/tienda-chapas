<?php
session_start();
require __DIR__ . '/../includes/funciones.php';



$id = (int)($_POST['id'] ?? 0);
$cantidad = (int)($_POST['cantidad'] ?? 0);

if ($id <= 0 || !isset($_SESSION['carrito'][$id])) {
    setFlash('error', 'Producto inválido');
    header('Location: carrito.php');
    exit;
}

if ($cantidad > 0) {
    $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
    setFlash('success', '');
} else {
    unset($_SESSION['carrito'][$id]);
    setFlash('success', '');
}


header('Location: carrito.php');
exit;
?>


