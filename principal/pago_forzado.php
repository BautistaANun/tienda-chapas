<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/mailer.php';
require __DIR__ . '/../config/config.php';

if (APP_ENV !== 'dev' || empty($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado');
}

$compraId = $_GET['id'] ?? null;

if (!ctype_digit($compraId)) {
    die('Compra inválida');
}

// marcar como pagada
$stmt = $pdo->prepare("UPDATE compras SET estado = 'pagada' WHERE id = ?");
$stmt->execute([$compraId]);

// traer datos
$stmt = $pdo->prepare("
    SELECT c.id, c.total, u.email
    FROM compras c
    LEFT JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.id = ?
");
$stmt->execute([$compraId]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

// mails
enviarMail(
    'bautistaanunez0510@gmail.com',
    'Pago aprobado (FORZADO)',
    "<p>Compra #{$compra['id']} aprobada en modo desarrollo.</p>"
);

if (!empty($compra['email'])) {
    enviarMail(
        $compra['email'],
        'Pago confirmado',
        "<p>Tu pago fue confirmado. Compra #{$compra['id']}.</p>"
    );
}

echo "Pago aprobado forzado OK";
