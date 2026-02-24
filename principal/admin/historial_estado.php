<?php
session_start();

require dirname(__DIR__) . '/../config/database.php';
require dirname(__DIR__) . '/../includes/funciones.php';

if (
    !isset($_SESSION['usuario']) ||
    !in_array($_SESSION['usuario']['rol'], ['admin','superadmin'], true)
) {
    header('Location: ../index.php');
    exit;
}
/* Validar ID */
$compraId = $_GET['id'] ?? null;

if (!$compraId || !ctype_digit($compraId)) {
    mostrarError('Compra inválida');
}

/* Traer historial SOLO de esa compra */
$stmt = $pdo->prepare("
    SELECT 
        h.estado_anterior,
        h.estado_nuevo,
        h.fecha,
        u.nombre AS admin
    FROM compras_estado_log h
    LEFT JOIN usuarios u ON u.id = h.admin_id
    WHERE h.compra_id = ?
    ORDER BY h.fecha DESC
");
$stmt->execute([$compraId]);

$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>


<div class="admin-panel">
    
    <div class="admin-header">
        <h1>📜 Historial de estados</h1>
        <span class="admin-subtitle">Compra #<?= $compraId ?></span>
    </div>

    <?php if (empty($historial)): ?>
        <div class="admin-empty">
            No hay modificaciones registradas para esta compra.
        </div>
    <?php else: ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado anterior</th>
                        <th>Estado nuevo</th>
                        <th>Administrador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                        <tr>
                            <td><?= $h['fecha'] ?></td>
                            <td>
                                <span class="estado estado-anterior">
                                    <?= ucfirst($h['estado_anterior']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="estado estado-nuevo">
                                    <?= ucfirst($h['estado_nuevo']) ?>
                                </span>
                            </td>
                            <td><?= $h['admin'] ?? '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <a href="compras.php" class="admin-back">← Volver a compras</a>

</div>


</body>
</html>
