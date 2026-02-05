<?php
session_start();

require dirname(__DIR__) . '/../config/database.php';
require dirname(__DIR__) . '/../includes/funciones.php';

/* 🔐 Seguridad */
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    mostrarError('Acción no autorizada');
}

$compraId = $_POST['compra_id'] ?? null;
$nuevoEstado = $_POST['estado'] ?? null;

$estadosValidos = ['pendiente', 'pagado', 'cancelado'];

if (!ctype_digit((string)$compraId) || !in_array($nuevoEstado, $estadosValidos, true)) {
    mostrarError('Datos inválidos');
}

/* Obtener estado actual */
$stmt = $pdo->prepare("SELECT estado FROM compras WHERE id = ?");
$stmt->execute([$compraId]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    mostrarError('Compra no encontrada');
}

$estadoAnterior = $compra['estado'];

/* Si no cambió el estado, no registramos nada */
if ($estadoAnterior !== $nuevoEstado) {

    /* Guardar historial */
    $stmt = $pdo->prepare("
        INSERT INTO compras_estado_log
        (compra_id, estado_anterior, estado_nuevo, admin_id)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $compraId,
        $estadoAnterior,
        $nuevoEstado,
        $_SESSION['usuario']['id']
    ]);

    /* Actualizar compra */
    $stmt = $pdo->prepare("
        UPDATE compras
        SET estado = ?
        WHERE id = ?
    ");
    $stmt->execute([$nuevoEstado, $compraId]);
}

/* Redirección segura */
header('Location: compras.php');
exit;
