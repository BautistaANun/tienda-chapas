<?php
session_start();

// Conexión a base de datos
require __DIR__ . '/../config/database.php';

// Obtenemos el ID enviado por POST y lo convertimos a entero
// Esto previene inyección o valores inesperados
$id = (int)($_POST['id'] ?? 0);

// Validación básica de ID
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

/* ===============================
   Buscar producto en base de datos
   =============================== */

// Consulta preparada para evitar SQL Injection
$stmt = $pdo->prepare("
    SELECT id, nombre, precio, imagen 
    FROM productos 
    WHERE id = ? AND activo = 1
");
$stmt->execute([$id]);
$producto = $stmt->fetch();

// Si el producto no existe, redirigimos
if (!$producto) {
    header('Location: index.php');
    exit;
}

/* ===============================
   Inicialización del carrito
   =============================== */

// El carrito se almacena en sesión
// Si no existe, lo inicializamos como array
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

/* ===============================
   Agregar o incrementar producto
   =============================== */

// Si el producto ya está en el carrito,
// incrementamos la cantidad
if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]['cantidad']++;
} else {

    // Si no existe, lo agregamos con cantidad inicial 1
    $_SESSION['carrito'][$id] = [
        'id' => $producto['id'],
        'nombre' => $producto['nombre'],
        'precio' => $producto['precio'],
        'imagen' => $producto['imagen'],
        'cantidad' => 1
    ];
}

// Redirección para mantener patrón PRG
header('Location: carrito.php');
exit;