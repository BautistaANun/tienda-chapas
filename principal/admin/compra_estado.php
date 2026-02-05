<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';
require dirname(__DIR__) . '/config/config.php';
// Debe estar logueado
if (!isset($_SESSION['usuario'])) {
    mostrarError('Acceso no autorizado');
}

$usuario = $_SESSION['usuario'];
$esAdmin = ($usuario['rol'] ?? '') === 'admin';

// SOLO admin puede actualizar estados
if (!$esAdmin) {
    mostrarError('Acción no autorizada');
}

$compra_id = $_POST['compra_id'] ?? null;
$estado = $_POST['estado'] ?? null;

$estadosValidos = ['pendiente', 'pagado', 'cancelado'];

if (!ctype_digit($compra_id) || !in_array($estado, $estadosValidos)) {
    mostrarError('Datos inválidos');
}

// Verificar que la compra exista
$stmt = $pdo->prepare("SELECT id FROM compras WHERE id = ?");
$stmt->execute([$compra_id]);

if (!$stmt->fetch()) {
    mostrarError('Compra no encontrada');
}

// Actualizar estado
$stmt = $pdo->prepare("
    UPDATE compras
    SET estado = ?
    WHERE id = ?
");
$stmt->execute([$estado, $compra_id]);

setFlash('success', 'Estado actualizado correctamente');
header('Location: compra_detalle.php?id=' . $compra_id);
exit;
