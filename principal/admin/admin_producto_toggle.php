

<?php

/*Recibir id por GET

Validar que exista

Invertir el valor de activo

Redireccionar*/

session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

/* =========================
   VALIDAR ID
   ========================= */

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID inválido.");
}

/* =========================
   OBTENER PRODUCTO
   ========================= */

$stmt = $pdo->prepare("SELECT activo FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    die("Producto no encontrado.");
}

/* =========================
   INVERTIR ESTADO
   ========================= */

$nuevoEstado = $producto['activo'] ? 0 : 1;

$stmtUpdate = $pdo->prepare("UPDATE productos SET activo = ? WHERE id = ?");
$stmtUpdate->execute([$nuevoEstado, $id]);

/* =========================
   REDIRECCIÓN
   ========================= */

header('Location: admin_productos.php');
exit;