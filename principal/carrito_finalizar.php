<?php
session_start();

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';
require __DIR__ . '/../includes/mailer.php';

if (empty($_SESSION['carrito'])) {
    setFlash('error', 'El carrito está vacío');
    header('Location: index.php');
    exit;
}

/* =========================
   DATOS FORM
   ========================= */
$nombre     = trim($_POST['nombre'] ?? '');
$apellido   = trim($_POST['apellido'] ?? '');
$dni        = trim($_POST['dni'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$direccion  = trim($_POST['direccion'] ?? '');
$comentario = trim($_POST['comentario'] ?? '');
$metodoPago = $_POST['metodo_pago'] ?? null;

/* =========================
   VALIDACIONES
   ========================= */
if (!$nombre || !$apellido || !$dni || !$telefono || !$direccion || !$metodoPago) {
    setFlash('error', 'Faltan datos del comprador');
    header('Location: carrito.php');
    exit;
}

if (!preg_match('/^\d{8}$/', $dni)) {
    setFlash('error', 'El DNI debe tener exactamente 8 dígitos');
    header('Location: carrito.php');
    exit;
}

if (!preg_match('/^\d{8,15}$/', $telefono)) {
    setFlash('error', 'El teléfono debe tener entre 8 y 15 dígitos');
    header('Location: carrito.php');
    exit;
}

if (strlen($direccion) < 5 || strlen($direccion) > 100) {
    setFlash('error', 'La dirección debe tener entre 5 y 100 caracteres');
    header('Location: carrito.php');
    exit;
}

try {
    $pdo->beginTransaction();

    /* =========================
       USUARIO (OPCIONAL)
       ========================= */
    $usuarioId = $_SESSION['usuario']['id'] ?? null;

    /* =========================
       TOTAL
       ========================= */
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    /* =========================
       COMPRA
       ========================= */
    $estado = ($metodoPago === 'efectivo')
        ? 'pendiente'
        : 'pendiente';

    $stmt = $pdo->prepare("
        INSERT INTO compras
        (usuario_id, nombre, apellido, dni, telefono, direccion, total, metodo_pago, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $usuarioId,
        $nombre,
        $apellido,
        $dni,
        $telefono,
        $direccion,
        $total,
        $metodoPago,
        $estado
    ]);

    $compraId = $pdo->lastInsertId();

    /* =========================
       DATOS COMPRADOR
       ========================= */
    $stmtDatos = $pdo->prepare("
        INSERT INTO datos_comprador
        (usuario_id, compra_id, nombre, apellido, dni, direccion, comentario)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmtDatos->execute([
        $usuarioId,
        $compraId,
        $nombre,
        $apellido,
        $dni,
        $direccion,
        $comentario
    ]);

    /* =========================
       ITEMS
       ========================= */
    $stmtItem = $pdo->prepare("
        INSERT INTO compra_items
        (compra_id, producto_id, cantidad, precio)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($_SESSION['carrito'] as $item) {
        $stmtItem->execute([
            $compraId,
            $item['id'],
            $item['cantidad'],
            $item['precio']
        ]);
    }

    $pdo->commit();

    unset($_SESSION['carrito']);

    /* =========================
       REDIRECCIÓN
       ========================= */
   
    if ($metodoPago === 'efectivo') {
    header("Location: pago_efectivo.php?id=$compraId");
    exit;
}


    // transferencia / Mercado Pago
    header("Location: crear_pago.php?id=$compraId");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    setFlash('error', 'Error al finalizar la compra');
    header('Location: carrito.php');
    exit;
}
