<link rel="stylesheet" href="../assets/css/styles.css">

<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';

/* Seguridad */
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$esAdmin = ($usuario['rol'] ?? '') === 'admin';

if ($esAdmin) {
    $stmt = $pdo->query("
        SELECT c.*, u.nombre AS usuario_nombre, u.email
        FROM compras c
        LEFT JOIN usuarios u ON u.id = c.usuario_id
        ORDER BY c.created_at DESC
    ");
} else {
   $stmt = $pdo->prepare("
    SELECT *
    FROM compras
    WHERE usuario_id = ?
    AND estado != 'cancelado'
    ORDER BY created_at DESC
");
$stmt->execute([$usuario['id']]);

}

$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="contenedor compras-container">

    <h1 class="compras-titulo">🛍️ Mis compras</h1>

    <?php if ($esAdmin): ?>
        <p class="admin-badge">Vista administrador</p>
    <?php endif; ?>

    <?php if (empty($compras)): ?>
        <div class="compras-vacio">
            <p>No realizaste ninguna compra todavía.</p>
            <a href="index.php" class="btn-principal">
                ← Volver a la tienda
            </a>
        </div>

    <?php else: ?>

        <div class="tabla-resumen">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($compras as $compra): ?>
                    <tr>
                        <td data-label="Compra Nº">
                            #<?= $compra['id'] ?>
                        </td>

                        <td data-label="Fecha">
                            <?= date('d/m/Y', strtotime($compra['created_at'])) ?>
                        </td>

                        <td data-label="Total">
                            $<?= number_format($compra['total'], 0, ',', '.') ?>
                        </td>

                        <td data-label="Estado">
                            <span class="estado estado-<?= e($compra['estado']) ?>">
                                <?= ucfirst($compra['estado']) ?>
                            </span>
                        </td>

                        <td>
                            <a href="compra_detalle.php?id=<?= $compra['id'] ?>"
                               class="btn-secundario">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="compras-acciones">
            <a href="index.php" class="btn-secundario">
                ← Volver a la tienda
            </a>

            <form method="post"
                  action="baja_usuario.php"
                  onsubmit="return confirm('¿Estás seguro? Esta acción es irreversible')">
                <button type="submit" class="btn-peligro">
                    ❌ Darse de baja
                </button>
            </form>
        </div>

    <?php endif; ?>

</main>
