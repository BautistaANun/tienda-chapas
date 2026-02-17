<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';

$compraId = $_GET['id'] ?? null;

if (!ctype_digit((string)$compraId)) {
    mostrarError('Compra inválida');
}

/* Obtener compra */
$stmt = $pdo->prepare("
    SELECT estado, usuario_id
    FROM compras
    WHERE id = ?
");
$stmt->execute([$compraId]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    mostrarError('Compra no encontrada');
}

/* Seguridad: si tiene usuario, debe ser el mismo */
if ($compra['usuario_id'] !== null) {
    if (
        !isset($_SESSION['usuario']) ||
        $_SESSION['usuario']['id'] != $compra['usuario_id']
    ) {
        mostrarError('No autorizado');
    }
}

/* Solo cancelar si está pendiente */
if ($compra['estado'] === 'pendiente') {
    $stmt = $pdo->prepare("
        UPDATE compras
        SET estado = 'cancelado'
        WHERE id = ?
    ");
    $stmt->execute([$compraId]);
}

$stmt = $pdo->prepare("
    UPDATE compras
    SET estado = 'cancelado',
        cancelada_por = 'usuario'
    WHERE id = ?
");
$stmt->execute([$compraId]);


/* Limpiar última compra */
unset($_SESSION['ultima_compra_id']);

/* Mensaje */
setFlash('warning', 'Compra cancelada');

/* Volver a la tienda */
header('Location: index.php');
exit;

