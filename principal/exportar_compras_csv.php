<?php
session_start();

require dirname(__DIR__) . '/config/database.php';
require dirname(__DIR__) . '/includes/funciones.php';
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    mostrarError('Acceso restringido');
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="compras.csv"');

$output = fopen('php://output', 'w');

// Encabezados
fputcsv($output, [
    'ID',
    'Fecha',
    'Usuario',
    'Email',
    'Total',
    'Estado'
]);

$compra = obtenerCompraSegura($pdo, $id, $_SESSION['usuario']);

if (!$compra || $_SESSION['usuario']['rol'] !== 'admin') {
    mostrarError('Acceso no autorizado');
}


while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['fecha'],
        $row['nombre'] ?? 'Invitado',
        $row['email'] ?? '-',
        $row['total'],
        $row['estado']
    ]);
}

fclose($output);
exit;
