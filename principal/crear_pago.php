<?php
session_start();

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/mercadopago.php';
require __DIR__ . '/../includes/funciones.php';
require __DIR__ . '/../includes/mailer.php';
$id = $_GET['id'] ?? null;

if (!ctype_digit($id)) {
    mostrarError('Compra inválida');
}

// obtener compra
$stmt = $pdo->prepare("SELECT * FROM compras WHERE id = ?");
$stmt->execute([$id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    mostrarError('Compra no encontrada');
}

if ($compra['metodo_pago'] === 'efectivo') {
    // No se paga online
    header("Location: " . BASE_URL . "principal/pago_efectivo.php?id={$compra['id']}");
    exit;
}


// crear preferencia (REST)
$payload = [
    "items" => [
        [
            "title" => "Compra #{$compra['id']}",
            "quantity" => 1,
            "unit_price" => round((float)$compra['total'], 2)
        ]
    ],
    "external_reference" => (string)$compra['id'],
    "notification_url" => BASE_URL . "principal/webhook_mp.php",
    "back_urls" => [
        "success" => BASE_URL . "principal/pago_exitoso.php?id={$compra['id']}",
        "failure" => BASE_URL . "principal/pago_fallido.php?id={$compra['id']}",
        "pending" => BASE_URL . "principal/pago_pendiente.php?id={$compra['id']}"
    ],
    "auto_return" => "approved"
];

$ch = curl_init("https://api.mercadopago.com/checkout/preferences");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . MP_ACCESS_TOKEN,
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);

if ($response === false) {
    mostrarError('Error de conexión con Mercado Pago');
}

$data = json_decode($response, true);

if (!isset($data['init_point'])) {
    mostrarError('Error al crear el pago en Mercado Pago');
}

// ❌ NO curl_close()
// PHP limpia solo

// redirigir a MP
header("Location: " . $data['init_point']);
exit;
