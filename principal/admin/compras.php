<?php
session_start();
require dirname(__DIR__) . '/../config/database.php';
require dirname(__DIR__) . '/../includes/funciones.php';

/* Seguridad: solo admin */
if (
    !isset($_SESSION['usuario']) ||
    !in_array($_SESSION['usuario']['rol'], ['admin','superadmin'], true)
) {
    header('Location: ../index.php');
    exit;
}
/* Traer TODAS las compras + cantidad de modificaciones */
$stmt = $pdo->query("
    SELECT 
        c.id,
        c.created_at,
        c.total,
        c.estado,
        u.nombre AS usuario_nombre,
        u.email,
        COUNT(h.id) AS total_cambios
    FROM compras c
    LEFT JOIN usuarios u ON u.id = c.usuario_id
    LEFT JOIN compras_estado_log h ON h.compra_id = c.id
    GROUP BY c.id
    ORDER BY c.created_at DESC
");


$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de compras</title>

    
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<main class="admin-page">

    <header class="admin-page-header">
        <h1>Administración de compras</h1>
        <p class="admin-page-subtitle">
            Gestión y control de estados de compra
        </p>
    </header>

    <section class="admin-card">

        <div class="admin-card-header">
            <h2>Listado de compras</h2>

            <a href="exportar_compras_csv.php" class="admin-btn outline">
                Exportar CSV
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Actualizar</th>
                        <th>Historial</th>
                        <th>Detalle</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($compras as $c): ?>
                    <tr>
                        <td>#<?= $c['id'] ?></td>

                        <td><?= e($c['usuario_nombre'] ?? 'Invitado') ?></td>

                        <td><?= e($c['email'] ?? '-') ?></td>

                        <td><?= e($c['created_at']) ?></td>

                        <td>
                            $<?= number_format($c['total'], 0, ',', '.') ?>
                        </td>

                        <td>
                            <span class="estado estado-<?= e($c['estado']) ?>">
                                <?= ucfirst($c['estado']) ?>
                            </span>
                        </td>

                        <td>
                            <form method="post" action="editar_estado.php" class="estado-form">
                                <input type="hidden" name="compra_id" value="<?= $c['id'] ?>">

                                <select name="estado" <?= $c['estado']==='cancelado'?'disabled':'' ?>>
                                    <option value="pendiente" <?= $c['estado']=='pendiente'?'selected':'' ?>>Pendiente</option>
                                    <option value="pagado" <?= $c['estado']=='pagado'?'selected':'' ?>>Pagado</option>
                                    <option value="cancelado" <?= $c['estado']=='cancelado'?'selected':'' ?>>Cancelado</option>
                                </select>

                                <button type="submit"
                                    class="admin-btn small"
                                    <?= $c['estado']==='cancelado'?'disabled':'' ?>>
                                    Guardar
                                </button>
                            </form>
                        </td>

                        <td class="text-center">
                            <a href="historial_estado.php?id=<?= $c['id'] ?>" class="link">
                                <?= $c['total_cambios'] ?> cambios
                            </a>
                        </td>

                        <td>
                            <a href="../compra_detalle.php?id=<?= $c['id'] ?>" class="admin-btn muted small">
                                Ver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </section>

    <footer class="admin-footer">
        <a href="dashboard.php" class="admin-link">
            ← Volver al dashboard
        </a>
    </footer>

</main>

</body>
</html>
