<link rel="stylesheet" href="../assets/css/styles.css">
<?php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/funciones.php';

$compraId = $_GET['id'] ?? ($_SESSION['ultima_compra_id'] ?? null);

if (!$compraId) {
    echo "Compra inválida";
    exit;
}

/* COMPRA */
$stmt = $pdo->prepare("
    SELECT c.*, u.nombre AS usuario_nombre
    FROM compras c
    LEFT JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$compraId]);
$compra = $stmt->fetch();

if (!$compra) {
    echo "Compra inválida";
    exit;
}

if ($compra['usuario_id'] !== null) {
    if (
        !isset($_SESSION['usuario']) ||
        $_SESSION['usuario']['id'] != $compra['usuario_id']
    ) {
        echo "No tenés permiso para ver esta compra";
        exit;
    }
}

if ($compra['estado'] === 'cancelado') {
    setFlash('warning', 'Esta compra fue cancelada');
    header('Location: index.php');
    exit;
}


/* ITEMS */
$stmt = $pdo->prepare("
    SELECT ci.*, p.nombre, p.imagen
    FROM compra_items ci
    JOIN productos p ON ci.producto_id = p.id
    WHERE ci.compra_id = ?
");
$stmt->execute([$compraId]);
$items = $stmt->fetchAll();
?>

<main class="contenedor resumen-container">

    <h1 class="resumen-titulo">🧾 Resumen de compra</h1>

    <!-- CARD INFO COMPRA -->
    <section class="resumen-card">
        <div class="resumen-info">

            <div class="resumen-dato">
                <span>Compra Nº</span>
                <strong>#<?= $compra['id'] ?></strong>
            </div>

            <div class="resumen-dato">
                <span>Fecha</span>
                <strong><?= e($compra['fecha']) ?></strong>
            </div>

            <div class="resumen-dato">
                <span>Total</span>
                <strong class="precio-total">
                    $<?= number_format($compra['total'], 0, ',', '.') ?>
                </strong>
            </div>

            <div class="resumen-dato">
                <span>Estado</span>
                <strong class="estado estado-<?= e($compra['estado']) ?>">
                    <?= ucfirst($compra['estado']) ?>
                </strong>
            </div>

        </div>
    </section>

    <!-- PRODUCTOS -->
    <section class="resumen-productos">
        <h2>🛍️ Productos comprados</h2>

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
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
            <tr>
             <td class="producto-resumen" data-label="Producto">
                    <img
                     src="../assets/images/<?= e($item['imagen']) ?>"
                     alt="<?= e($item['nombre']) ?>"
                    class="producto-mini"
                      >
                 <span><?= e($item['nombre']) ?></span>
                     </td>

                    <td data-label="Precio">
                     $<?= number_format($item['precio'], 0, ',', '.') ?>
                    </td>
                        <td data-label="Cantidad">
                        <?= (int)$item['cantidad'] ?>
                        </td>
                        <td data-label="Subtotal">
                              $<?= number_format(
                             $item['precio'] * $item['cantidad'],
                            0,
                            ',',
                         '.'
                             ) ?>
                        </td>
                </tr>

                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4">No hay productos en esta compra.</td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ACCIONES -->
    <div class="resumen-acciones">
        <a href="pagar_compra.php?id=<?= $compraId ?>" class="btn-principal">
            💳 Confirmar pago
        </a>

        <a href="cancelar_compra.php?id=<?= $compraId ?>" class="btn-secundario">
            ← Cancelar y volver
        </a>

    </div>

</main>
