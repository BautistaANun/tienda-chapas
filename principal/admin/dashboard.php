<?php
session_start();
require dirname(__DIR__) . '/../config/database.php';
require dirname(__DIR__) . '/../includes/funciones.php';
require dirname(__DIR__) . '/../config/config.php';

/* Seguridad */
if (
    !isset($_SESSION['usuario']) ||
    !in_array($_SESSION['usuario']['rol'] ?? '', ['admin','superadmin'], true)
) {
    mostrarError('Acceso restringido');
}

/* Métricas */

// Total vendido (solo pagadas)
$totalVendido = $pdo->query("
    SELECT COALESCE(SUM(total),0)
    FROM compras
    WHERE estado = 'pagado'
")->fetchColumn();

// Cantidades por estado
$pendientes = $pdo->query("
    SELECT COUNT(*) FROM compras WHERE estado = 'pendiente'
")->fetchColumn();

$pagadas = $pdo->query("
    SELECT COUNT(*) FROM compras WHERE estado = 'pagado'
")->fetchColumn();

$canceladas = $pdo->query("
    SELECT COUNT(*) FROM compras WHERE estado = 'cancelado'
")->fetchColumn();

// Total compras
$totalCompras = $pdo->query("
    SELECT COUNT(*) FROM compras
")->fetchColumn();


/* Ventas por estado */
$ventasEstado = $pdo->query("
    SELECT estado, COUNT(*) cantidad
    FROM compras
    GROUP BY estado
")->fetchAll(PDO::FETCH_ASSOC);

/* Ventas por mes */
$ventasMes = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') mes, SUM(total) total
    FROM compras
    WHERE estado = 'pagado'
    GROUP BY mes
    ORDER BY mes
")->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administración</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body>

<div class="admin-layout">

    <!-- ================= SIDEBAR ================= -->
    <aside class="admin-sidebar" id="adminSidebar">

        <div class="sidebar-header">
            <strong>ADMIN</strong>
            <button class="sidebar-close" onclick="toggleSidebar()">✕</button>
        </div>

        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>principal/admin/dashboard.php">📊 Dashboard</a>
            <a href="<?= BASE_URL ?>principal/admin/compras.php">📋 Compras</a>
            <a href="<?= BASE_URL ?>principal/admin/usuario_compras.php">👥 Usuarios</a>
            <a href="<?= BASE_URL ?>principal/exportar_compras_csv.php">⬇ Exportar CSV</a>
             <?php if ($_SESSION['usuario']['rol'] === 'superadmin'): ?>
        <a href="<?= BASE_URL ?>principal/admin/nuevoAdmin.php">
            + Crear administrador
         </a>
        <?php endif; ?>
            <hr>
            <a href="<?= BASE_URL ?>principal/index.php">🏪 Volver </a>
        </nav>

    </aside>

    <!-- ================= CONTENIDO ================= -->
    <main class="admin-content">

        <!-- TOPBAR MOBILE -->
        <header class="admin-topbar">
            <button class="menu-btn" onclick="toggleSidebar()">☰</button>
            <h1>Dashboard</h1>
        </header>

        <!-- ===== CONTENIDO REAL ===== -->
        <div class="admin-dashboard">

            <header class="admin-header">
                <p class="admin-subtitle">Resumen general del sistema</p>
            </header>

            <!-- MÉTRICAS -->
            <section class="admin-metrics">

                <div class="metric-card">
                    <span class="metric-label">Total vendido</span>
                    <strong class="metric-value">
                        $<?= number_format($totalVendido, 0, ',', '.') ?>
                    </strong>
                </div>

                <div class="metric-card">
                    <span class="metric-label">Total de compras</span>
                    <strong class="metric-value"><?= $totalCompras ?></strong>
                </div>

                <div class="metric-card warning">
                    <span class="metric-label">Pendientes</span>
                    <strong class="metric-value"><?= $pendientes ?></strong>
                </div>

                <div class="metric-card success">
                    <span class="metric-label">Pagadas</span>
                    <strong class="metric-value"><?= $pagadas ?></strong>
                </div>

                <div class="metric-card danger">
                    <span class="metric-label">Canceladas</span>
                    <strong class="metric-value"><?= $canceladas ?></strong>
                </div>

            </section>

            <!-- VENTAS POR ESTADO -->
            <section class="admin-section">
                <h2>Ventas por estado</h2>
                <ul class="admin-list">
                    <?php foreach ($ventasEstado as $v): ?>
                        <li>
                            <span><?= ucfirst($v['estado']) ?></span>
                            <strong><?= $v['cantidad'] ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <!-- VENTAS POR MES -->
            <section class="admin-section">
                <h2>Ventas por mes (pagadas)</h2>
                <ul class="admin-list">
                    <?php foreach ($ventasMes as $v): ?>
                        <li>
                            <span><?= $v['mes'] ?></span>
                            <strong>$<?= number_format($v['total'], 0, ',', '.') ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <!-- ACCIONES -->
            <section class="admin-section">
                <h2>Acciones rápidas</h2>

                <div class="admin-actions">
                    <a href="<?= BASE_URL ?>principal/admin/compras.php" class="admin-btn">
                        Administrar compras
                    </a>

                    <a href="<?= BASE_URL ?>principal/admin/usuario_compras.php" class="admin-btn">
                        Compras por usuario
                    </a>

                    <a href="<?= BASE_URL ?>principal/exportar_compras_csv.php" class="admin-btn outline">
                        Exportar CSV
                    </a>

                
                </div>
            </section>

        </div>
    </main>

</div>

<script>
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('open');
}
</script>

</body>
</html>
