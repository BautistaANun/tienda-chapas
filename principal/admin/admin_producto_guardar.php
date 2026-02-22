

<?php

/*Este archivo va a:

Recibir POST

Validar todo nuevamente (no confiar en el formulario)

Insertar en base

Redireccionar*/

session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

/* =========================
   OBTENER Y LIMPIAR DATOS
   ========================= */

$nombre = trim($_POST['nombre'] ?? '');
$precio = (float) ($_POST['precio'] ?? 0);
$stock = (int) ($_POST['stock'] ?? 0);
$categoria = trim($_POST['categoria'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$activo = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;

/* =========================
   VALIDACIONES BACKEND
   ========================= */

if (!$nombre || !$categoria || !$descripcion) {
    die("Todos los campos son obligatorios.");
}

if ($precio < 0) {
    die("El precio no puede ser negativo.");
}

if ($stock < 0) {
    die("El stock no puede ser negativo.");
}

if ($activo !== 0 && $activo !== 1) {
    die("Estado inválido.");
}

/* =========================
   INSERTAR EN BASE
   ========================= */

$imagenNombre = null;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {

    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $imagenNombre = uniqid() . "." . $ext;

    $rutaDestino = __DIR__ . "/../../uploads/images/" . $imagenNombre;

    move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
}

$stmt = $pdo->prepare("
    INSERT INTO productos
    (nombre, precio, stock, categoria, imagen, descripcion, activo)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $nombre,
    $precio,
    $stock,
    $categoria,
    $imagenNombre,
    $descripcion,
    $activo
]);

/* =========================
   REDIRECCIÓN
   ========================= */

header('Location: admin_productos.php');
exit;