<?php
session_start();
require dirname(__DIR__) . '/../config/database.php';
require dirname(__DIR__) . '/../includes/funciones.php';
require dirname(__DIR__) . '/../config/config.php';

if (
    !isset($_SESSION['usuario']) ||
    !in_array($_SESSION['usuario']['rol'], ['admin','superadmin'], true)
) {
    header('Location: ../index.php');
    exit;
}
$usuario_id = $_GET['id'] ?? null;

if (!ctype_digit($usuario_id)) {
    mostrarError('Usuario inválido');
}

$stmt = $pdo->prepare("
    SELECT c.*
    FROM compras c
    WHERE c.usuario_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$usuario_id]);

$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compras del usuario</title>

    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<main class="admin-page">

    <!-- HEADER -->
    <header class="admin-page-header">
        <h1>Compras del usuario</h1>
        <p class="admin-page-subtitle">
            Historial de compras del usuario #<?= $usuario_id ?>
        </p>
    </header>

    <!-- CARD -->
    <section class="admin-card">

        <div class="admin-card-header">
            <h2>Listado de compras</h2>
        </div>

        <?php if (empty($compras)): ?>
            <p class="admin-empty">
                Este usuario no tiene compras registradas.
            </p>
        <?php else: ?>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Detalle</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($compras as $c): ?>
                    <tr>
                        <td>#<?= $c['id'] ?></td>

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
                            <a
                                href="<?= BASE_URL ?>principal/compra_detalle.php?id=<?= $c['id'] ?>"
                                class="admin-btn muted small"
                            >
                                Ver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>

    </section>

    <!-- FOOTER -->
    <footer class="admin-footer">
        <a href="usuario_compras.php" class="admin-link">
            ← Volver a usuarios
        </a>
    </footer>

</main>

</body>
</html>
