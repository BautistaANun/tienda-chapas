

<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require __DIR__ . '/../config/database.php';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/funciones.php';
require __DIR__ . '/../includes/mailer.php';

$id = $_GET['id'] ?? null;

if (!ctype_digit($id)) {
    mostrarError('Compra inválida');
}

/* =========================
   BUSCAR COMPRA
   ========================= */
$stmt = $pdo->prepare("
    SELECT c.*, u.email AS email_usuario
    FROM compras c
    LEFT JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.id = ?
");

$stmt->execute([$id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);




if (!$compra) {
    mostrarError('Compra no encontrada');
}

if ($compra['metodo_pago'] !== 'efectivo') {
    mostrarError('Método de pago incorrecto');
}

 $emailDestino = null;

if (!empty($compra['usuario_id'])) {
    // Compra de usuario logueado
    $emailDestino = $compra['email_usuario'];
} else {
    // Compra de invitado
    $emailDestino = $compra['email'];
}


/* =========================
   ENVIAR MAILS
   ========================= */
if ((int)$compra['mail_enviado'] === 0) {

    /* Detalle de items */
    $stmt = $pdo->prepare("
        SELECT nombre, precio, cantidad
        FROM compra_items
        WHERE compra_id = ?
    ");
    $stmt->execute([$compra['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $tabla = '
<table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;border:1px solid #ccc">
    <tr style="background:#f2f2f2">
        <th align="left">Producto</th>
        <th align="center">Cant.</th>
        <th align="right">Precio</th>
        <th align="right">Subtotal</th>
    </tr>
';


foreach ($items as $item) {
    $subtotal = $item['precio'] * $item['cantidad'];

    $tabla .= "
    <tr>
        <td>{$item['nombre']}</td>
        <td align='center'>{$item['cantidad']}</td>
        <td align='right'>$" . number_format($item['precio'], 0, ',', '.') . "</td>
        <td align='right'>$" . number_format($subtotal, 0, ',', '.') . "</td>
    </tr>";
}

$tabla .= "
    <tr style='background:#f9f9f9'>
        <td colspan='3' align='right'><strong>Total</strong></td>
        <td align='right'><strong>$" . number_format($compra['total'], 0, ',', '.') . "</strong></td>
    </tr>
</table>";

    /* Mail cliente */
    $mensajeCliente = "
    <h2>¡Gracias por tu compra!</h2>
    <p>Hola {$compra['nombre']},</p>
    <p>Recibimos correctamente tu pedido <strong>#{$compra['id']}</strong>.</p>
    <p>Elegiste <strong>pago en efectivo a coordinar</strong>.</p>
    <p><strong>Detalle de tu compra:</strong></p>
    $tabla
    <p>En breve nos vamos a contactar para coordinar la entrega y el pago.</p>
    <hr>
    <p>Tienda Chapas</p>
    ";


if (!empty($emailDestino)) {
    enviarMail(
        $emailDestino,
        'Recibimos tu pedido - Pago en efectivo',
        $mensajeCliente
    );
}


    /* Mail administrador */
    $mensajeAdmin = "
    <h2>Nueva compra en efectivo</h2>
    <p><strong>Compra:</strong> #{$compra['id']}</p>
    <p><strong>Cliente:</strong> {$compra['nombre']} {$compra['apellido']}</p>
    <p><strong>DNI:</strong> {$compra['dni']}</p>
    <p><strong>Teléfono:</strong> {$compra['telefono']}</p>
    <p><strong>Dirección:</strong> {$compra['direccion']}</p>
    <p><strong>Método de pago:</strong> Efectivo a coordinar</p>
    <p><strong>Detalle de la compra:</strong></p>
    $tabla
    <hr>
    <p>Tienda Chapas</p>
    ";

    enviarMail(
        'bautistaanunez0510@gmail.com',
        'Nueva compra en efectivo #' . $compra['id'],
        $mensajeAdmin
    );

    /* Marcar como enviado */
    $upd = $pdo->prepare("UPDATE compras SET mail_enviado = 1 WHERE id = ?");
    $upd->execute([$compra['id']]);
}

header("Location: gracias.php?id={$compra['id']}");
exit;


