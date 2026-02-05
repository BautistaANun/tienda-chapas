<?php
require __DIR__ . '/../config/database.php';

$dni = $_GET['dni'] ?? '';

if (!preg_match('/^\d{8}$/', $dni)) {
    echo json_encode(['encontrado' => false]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT nombre, apellido, telefono, direccion
    FROM clientes
    WHERE dni = ?
    LIMIT 1
");
$stmt->execute([$dni]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    echo json_encode(['encontrado' => true] + $cliente);
} else {
    echo json_encode(['encontrado' => false]);
}


