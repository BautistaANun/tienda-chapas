<link rel="stylesheet" href="../assets/css/styles.css">

<?php
session_start();

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/database.php';
require dirname(__DIR__) . '/includes/funciones.php';

$usuario = $_SESSION['usuario'] ?? null;
$esAdmin = $usuario && ($usuario['rol'] ?? '') === 'admin';

$id = $_GET['id'] ?? null;

if (!ctype_digit($id)) {
    mostrarError('Compra inválida');
}

$compra = obtenerCompraSegura($pdo, $id, $usuario);

if (!$compra) {
    mostrarError('Compra no encontrada o sin permisos');
}

/* Datos comprador */
$stmt = $pdo->prepare("
    SELECT nombre, apellido, dni, direccion, comentario
    FROM datos_comprador
    WHERE compra_id = ?
");
$stmt->execute([$id]);
$datosComprador = $stmt->fetch(PDO::FETCH_ASSOC);

/* Items */
$stmt = $pdo->prepare("
    SELECT ci.cantidad, ci.precio, p.nombre
    FROM compra_items ci
    JOIN productos p ON p.id = ci.producto_id
    WHERE ci.compra_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="contenedor detalle-container">

    <h1 class="detalle-titulo">🧾 Compra #<?= $compra['id'] ?></h1>

    <!-- INFO COMPRA -->
    <section class="detalle-card">
        <div class="detalle-info">

            <div class="detalle-dato">
                <span>Fecha</span>
                <strong><?= date('d/m/Y H:i', strtotime($compra['created_at'])) ?></strong>
            </div>

            <div class="detalle-dato">
                <span>Total</span>
                <strong class="precio-total">
                    $<?= number_format($compra['total'], 0, ',', '.') ?>
                </strong>
            </div>

            <div class="detalle-dato">
                <span>Estado</span>
                <strong class="estado estado-<?= e($compra['estado']) ?>">
                    <?= ucfirst($compra['estado']) ?>
                </strong>
            </div>

        </div>
    </section>

    <!-- PRODUCTOS -->
    <section class="detalle-productos">
        <h2>🛍️ Productos</h2>

        <div class="tabla-resumen">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cant.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($items as $item): ?>
                    <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                    <tr>
                        <td data-label="Producto"><?= e($item['nombre']) ?></td>
                        <td data-label="Precio">
                            $<?= number_format($item['precio'], 0, ',', '.') ?>
                        </td>
                        <td data-label="Cantidad"><?= (int)$item['cantidad'] ?></td>
                        <td data-label="Subtotal">
                            $<?= number_format($subtotal, 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- DATOS COMPRADOR -->
    <section class="detalle-card">
        <h2>👤 Datos del comprador</h2>

        <div class="detalle-info">
            <div class="detalle-dato">
                <span>Nombre: </span>
                <strong><?= e($datosComprador['nombre']) ?></strong>
            </div>

            <div class="detalle-dato">
                <span>Apellido: </span>
                <strong><?= e($datosComprador['apellido']) ?></strong>
            </div>

            <div class="detalle-dato">
                <span>DNI: </span>
                <strong><?= e($datosComprador['dni']) ?></strong>
            </div>

            <div class="detalle-dato">
                <span>Dirección: </span>
                <strong><?= e($datosComprador['direccion']) ?></strong>
            </div>

            <?php if (!empty($datosComprador['comentario'])): ?>
                <div class="detalle-dato">
                    <span>Comentario: </span>
                    <strong><?= e($datosComprador['comentario']) ?></strong>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ACCIONES -->
    <div class="detalle-acciones">
        <?php if ($esAdmin): ?>
            <a href="<?= BASE_URL ?>principal/exportar_compras_csv.php?id=<?= $id ?>"
               class="btn-secundario">
                Exportar CSV
            </a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>principal/compra_pdf.php?id=<?= $id ?>"
           target="_blank"
           class="btn-principal">
            🧾 Descargar PDF
        </a>

        <a href="<?= BASE_URL ?>principal/mis_compras.php"
           class="btn-secundario">
            ← Volver a mis compras
        </a>

        <?php if ($esAdmin || ($_SESSION['usuario']['rol'] === 'superadmin')): ?>
            <a href="<?= BASE_URL ?>principal/admin/compras.php"
               class="btn-secundario">
                Ajustes
            </a>
        <?php endif; ?>
    </div>

</main>
