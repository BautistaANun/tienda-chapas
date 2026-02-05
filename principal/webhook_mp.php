<?php

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';
require __DIR__ . '/../includes/mailer.php';
require __DIR__ . '/../config/mercadopago.php';

/* =========================
   1️⃣ Body crudo
   ========================= */
$body = file_get_contents('php://input');

/* =========================
   2️⃣ Headers MP
   ========================= */
$signatureHeader = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$requestId       = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';

if (!$signatureHeader || !$requestId) {
    http_response_code(401);
    exit('Headers faltantes');
}

/* =========================
   3️⃣ Parse firma MP
   x-signature: ts=123456,v1=abcdef
   ========================= */
parse_str(str_replace(',', '&', $signatureHeader), $signatureParts);

$ts = $signatureParts['ts'] ?? null;
$v1 = $signatureParts['v1'] ?? null;

if (!$ts || !$v1) {
    http_response_code(401);
    exit('Firma mal formada');
}

/* =========================
   4️⃣ Recalcular firma
   ========================= */
$manifest = "id:$requestId;ts:$ts;";
$expectedSignature = hash_hmac('sha256', $manifest, MP_WEBHOOK_SECRET);

if (!hash_equals($expectedSignature, $v1)) {
    http_response_code(401);
    exit('Firma inválida');
}

/* =========================
   5️⃣ JSON
   ========================= */
$data = json_decode($body, true);

if (empty($data['data']['id'])) {
    http_response_code(400);
    exit('Payload inválido');
}

$paymentId = $data['data']['id'];

/* =========================
   6️⃣ Consultar pago
   ========================= */
$ch = curl_init("https://api.mercadopago.com/v1/payments/$paymentId");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . MP_ACCESS_TOKEN
    ]
]);

$response = curl_exec($ch);

$payment = json_decode($response, true);

if (empty($payment['status'])) {
    http_response_code(400);
    exit('Pago inválido');
}

/* =========================
   7️⃣
   ========================= */
$estadoMP = $payment['status']; // approved | pending | rejected


/* =========================
   8️⃣ Compra
   ========================= */
$compraId = $payment['external_reference'] ?? null;

if (!$compraId) {
    http_response_code(400);
    exit('Referencia faltante');
}

/* =========================
   9️⃣ Evitar duplicados
   ========================= */
$stmt = $pdo->prepare("SELECT estado FROM compras WHERE id = ?");
$stmt->execute([$compraId]);
$estadoActual = $stmt->fetchColumn();



$estadoCompra = match ($estadoMP) {
    'approved' => 'pagada',
    'pending'  => 'pendiente_pago',
    default    => 'rechazada'
};


if ($estadoActual === $estadoCompra) {
    http_response_code(200);
    exit('Estado ya aplicado');
}


$stmt = $pdo->prepare("
    UPDATE compras
    SET estado = ?
    WHERE id = ?
");
$stmt->execute([$estadoCompra, $compraId]);

/* =========================
   1️⃣1️⃣ Datos compra
   ========================= */
$stmt = $pdo->prepare("
    SELECT c.total, u.email
    FROM compras c
    LEFT JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.id = ?
");
$stmt->execute([$compraId]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   1️⃣2️⃣ Mails según estado
   ========================= */

if ($estadoMP === 'approved') {

    // admin
    enviarMail(
        'contacto@chapasre.store',
        'Pago aprobado (Mercado Pago)',
        "<p>La compra #$compraId fue aprobada.</p>"
    );

    // cliente
    if (!empty($compra['email'])) {
        enviarMail(
            $compra['email'],
            'Pago confirmado',
            "
            <h2>Pago confirmado</h2>
            <p>Gracias por tu compra.</p>
            <p>Número de compra: #$compraId</p>
            "
        );
    }
}

if ($estadoMP === 'pending') {

    // admin
    enviarMail(
        'contacto@chapasre.store',
        'Compra pendiente de pago',
        "<p>La compra #$compraId está pendiente de acreditación.</p>"
    );

    // cliente
    if (!empty($compra['email'])) {
        enviarMail(
            $compra['email'],
            'Esperando transferencia',
            "
            <h2>Pago pendiente</h2>
            <p>Estamos esperando la acreditación de tu pago.</p>
            <p>Número de compra: #$compraId</p>
            "
        );
    }
}

/* =========================
   1️⃣3️⃣ OK
   ========================= */
http_response_code(200);
echo 'OK';
