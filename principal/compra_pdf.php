<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';

if (!isset($_SESSION['usuario'])) {
    mostrarError('Acceso no autorizado');
}

$id = $_GET['id'] ?? null;

if (!ctype_digit($id)) {
    mostrarError('Compra inválida');
}

$compra = obtenerCompraSegura($pdo, $id, $_SESSION['usuario']);

if (!$compra) {
    mostrarError('Compra no encontrada o sin permisos');
}


// Items
$stmt = $pdo->prepare("
    SELECT p.nombre, ci.precio, ci.cantidad
    FROM compra_items ci
    JOIN productos p ON p.id = ci.producto_id
    WHERE ci.compra_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Compra #<?= $compra['id'] ?></title>
    <style>
        body { font-family: Arial; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:8px; }
        th { background:#eee; }
    </style>
</head>
<body onload="window.print()">

<h2>Compra #<?= $compra['id'] ?></h2>
<p><strong>Fecha:</strong> <?= $compra['created_at'] ?></p>
<p><strong>Total:</strong> $<?= number_format($compra['total'], 0, ',', '.') ?></p>
<p><strong>Estado:</strong> <?= ucfirst($compra['estado']) ?></p>

<table>
    <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach ($items as $item): ?>
    <tr>
        <td><?= e($item['nombre']) ?></td>
        <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>
        <td><?= $item['cantidad'] ?></td>
        <td>$<?= number_format($item['precio'] * $item['cantidad'], 0, ',', '.') ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
