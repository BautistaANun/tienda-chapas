<?php

/*
|--------------------------------------------------------------------------
| ACTUALIZACIÓN DE CARRITO
|--------------------------------------------------------------------------
|
| Este archivo permite modificar la cantidad de un producto ya agregado
| al carrito o eliminarlo si la cantidad enviada es 0.
|
| Decisiones técnicas:
| - Se convierte id y cantidad a entero para evitar manipulación básica.
| - Se valida que el producto exista previamente en la sesión.
| - No se confía en el frontend para validar estructura.
| - Se utiliza sistema de mensajes flash para feedback.
|
| Es importante:
| - Validar stock real contra base de datos antes de actualizar.
| - Limitar cantidad máxima permitida por producto.
|
*/
session_start();

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';

$id = (int)($_POST['id'] ?? 0);
$cantidad = (int)($_POST['cantidad'] ?? 0);

if ($id <= 0 || !isset($_SESSION['carrito'][$id])) {
    setFlash('error', 'Producto inválido');
    header('Location: carrito.php');
    exit;
}

/* ===============================================
   VALIDACIÓN DE STOCK REAL DESDE LA BASE DE DATOS
   =============================================== */

$stmt = $pdo->prepare("SELECT stock, activo FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto || $producto['activo'] == 0) {
    setFlash('error', 'Producto no disponible');
    header('Location: carrito.php');
    exit;
}

if ($cantidad > $producto['stock']) {
    setFlash('error', 'Stock insuficiente');
    header('Location: carrito.php');
    exit;
}

if ($cantidad > 0) {
    $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
    setFlash('success', 'Cantidad actualizada');
} else {
    unset($_SESSION['carrito'][$id]);
    setFlash('success', 'Producto eliminado del carrito');
}

header('Location: carrito.php');
exit;


