<?php
session_start();

if (
    !isset($_SESSION['usuario']) ||
    !in_array($_SESSION['usuario']['rol'], ['admin','superadmin'], true)
) {
    header('Location: ../index.php');
    exit;
}

require dirname(__DIR__) . '/../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

/* =========================
   OBTENER Y LIMPIAR DATOS
   ========================= */

$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$precio = (float) ($_POST['precio'] ?? 0);
$stock = (int) ($_POST['stock'] ?? 0);
$categoria = trim($_POST['categoria'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$activo = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;

/* =========================
   VALIDACIONES
   ========================= */

if ($id <= 0) die("ID inválido.");
if (!$nombre || !$categoria || !$descripcion) die("Todos los campos son obligatorios.");
if ($precio < 0) die("El precio no puede ser negativo.");
if ($stock < 0) die("El stock no puede ser negativo.");
if ($activo !== 0 && $activo !== 1) die("Estado inválido.");

/* =========================
   OBTENER PRODUCTO ACTUAL
   ========================= */

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$productoActual = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$productoActual) {
    die("Producto no encontrado.");
}

/* =========================
   MANEJO DE IMAGEN
   ========================= */

$imagenNombre = $productoActual['imagen']; // mantener imagen actual

if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === 0) {

    $directorio = __DIR__ . "/../../uploads/images/";
    // Limitar tamaño a 2MB
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($_FILES['imagen']['size'] > $maxSize) {
    die("La imagen supera el tamaño máximo permitido (2MB).");
    }

    // Crear carpeta si no existe
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Borrar imagen anterior si existe
    if (!empty($productoActual['imagen'])) {
        $rutaVieja = $directorio . $productoActual['imagen'];
        if (file_exists($rutaVieja)) {
            unlink($rutaVieja);
        }
    }

    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $imagenNombre = uniqid() . "." . $ext;

    $rutaDestino = $directorio . $imagenNombre;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        die("Error al subir la imagen.");
    }
}

/* =========================
   ACTUALIZAR PRODUCTO
   ========================= */

$stmt = $pdo->prepare("
    UPDATE productos
    SET nombre = ?,
        precio = ?,
        stock = ?,
        categoria = ?,
        descripcion = ?,
        activo = ?,
        imagen = ?
    WHERE id = ?
");

$stmt->execute([
    $nombre,
    $precio,
    $stock,
    $categoria,
    $descripcion,
    $activo,
    $imagenNombre,
    $id
]);

/* =========================
   REDIRECCIÓN
   ========================= */

header('Location: admin_productos.php');
exit;