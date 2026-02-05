<?php
session_start();
require dirname(__DIR__) . '/../config/database.php';
require dirname(__DIR__) . '/../includes/funciones.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol'] ?? '') !== 'admin') {
    mostrarError('Acceso restringido');
}

$stmt = $pdo->query("
    SELECT u.id, u.nombre, u.email, COUNT(c.id) AS total_compras
    FROM usuarios u
    JOIN compras c ON c.usuario_id = u.id
    GROUP BY u.id
    ORDER BY total_compras DESC
");

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios con compras</title>

    <!-- CSS ADMIN -->
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<main class="admin-page">

    <!-- HEADER -->
    <header class="admin-page-header">
        <h1>Usuarios con compras</h1>
        <p class="admin-page-subtitle">
            Resumen de actividad de usuarios registrados
        </p>
    </header>

    <!-- CARD -->
    <section class="admin-card">

        <div class="admin-card-header">
            <h2>Listado de usuarios</h2>
        </div>

        <?php if (empty($usuarios)): ?>
            <p class="admin-empty">
                No hay usuarios con compras registradas.
            </p>
        <?php else: ?>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Total compras</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= e($u['nombre']) ?></td>

                        <td><?= e($u['email']) ?></td>

                        <td>
                            <strong><?= $u['total_compras'] ?></strong>
                        </td>

                        <td>
                            <a
                                href="compras_usuario.php?id=<?= $u['id'] ?>"
                                class="admin-btn small"
                            >
                                Ver compras
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
        <a href="dashboard.php" class="admin-link">
            ← Volver al dashboard
        </a>
    </footer>

</main>

</body>
</html>
