<?php
session_start();

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';
require __DIR__ . '/../includes/mailer.php';

$id = $_GET['id'] ?? null;

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    mostrarError('Acceso no autorizado');
}


if (!ctype_digit($id)) {
    mostrarError('Compra inválida');
}

// marcar como pagada
$stmt = $pdo->prepare("
    UPDATE compras
    SET estado = 'pagada'
    WHERE id = ?
");
$stmt->execute([$id]);

// obtener datos
$stmt = $pdo->prepare("
    SELECT c.*, u.email
    FROM compras c
    LEFT JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

// mail admin
enviarMail(
    'bautistaanunez0510@gmail.com',
    'Compra pagada',
    "<p>La compra #$id fue confirmada como pagada.</p>"
);

// mail cliente
if (!empty($compra['email'])) {
    enviarMail(
        $compra['email'],
        'Pago confirmado',
        "
        <h2>Pago confirmado</h2>
        <p>Gracias por tu compra.</p>
        <p>Número de compra: #$id</p>
        "
    );
}

setFlash('success', 'Pago confirmado correctamente');
header("Location: compra_detalle.php?id=$id");
exit;
