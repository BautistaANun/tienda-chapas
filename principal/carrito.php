
<link rel="stylesheet" href="../assets/css/styles.css">


<?php


session_start();
require __DIR__ . "/../includes/funciones.php";

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;



?>
<main class="contenedor">
<div class="carrito-container">

<h1>  🛒 Carrito de compras</h1>

<?php if ($flash = getFlash()): ?>
    <div class="flash <?= e($flash['tipo']) ?>">
        <?= e($flash['mensaje']) ?>
    </div>
<?php endif; ?>

<?php if (empty($carrito)): ?>
    <div class="carrito-vacio">
        <p>Tu carrito está vacío</p>
        <a href="index.php" class="btn">← Volver a la tienda</a>
    </div>

<?php else: ?>

    
    

<div class="tabla-carrito">
<table>
    <thead>
        <tr>
            <th></th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cant.</th>
            <th>Subtotal</th>
            <th></th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($carrito as $id=> $item): ?>
        <?php
            $subtotal = $item['precio'] * $item['cantidad'];
            $total += $subtotal;
        ?>
        <tr>

            <td class="carrito-img">
                <?php if (!empty($item['imagen'])): ?>
             <img 
                src="../uploads/images/<?= e($item['imagen']) ?>" 
               
                alt="<?= e($item['nombre']) ?>"
                >
                <?php endif; ?>
            </td>
            

            <td><?= e($item['nombre']) ?></td>

            <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>

            <td>
                <form method="post" action="carrito_actualizar.php">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1">
                <button>↻</button>
            </form>
            </td>

            <td>$<?= number_format($subtotal, 0, ',', '.') ?></td>

            <td>
                <form method="post" action="carrito_actualizar.php">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="cantidad" value="0">
                <button class="btn-eliminar">✕</button>
                </form>

            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="carrito-resumen">
    <h3>Total: $<?= number_format($total, 0, ',', '.') ?></h3>

    <form method="post" action="carrito_finalizar.php" class="checkout-form">
        <input name="nombre" id="nombre" required placeholder="Nombre">
        <input name="apellido" id="apellido" required placeholder="Apellido">
        <input type="text" name="dni" id="dni" maxlength="8" pattern="\d{8}" inputmode="numeric" required placeholder="DNI: ">
        <input type="text"name="telefono"maxlength="15" pattern="\d{8,15}"inputmode="numeric"required placeholder="Teléfono"        >
        <input name="direccion" required placeholder="Dirección">
        <input type="text" name="comentario" placeholder="Comentario (opcional)">
        <div class="metodos-pago">
        <p><strong>Método de pago</strong></p>

        <label>
            <input type="radio" name="metodo_pago" value="efectivo" required>
            💵 Efectivo (a coordinar)
        </label>

        <label>
            <input type="radio" name="metodo_pago" value="transferencia" required>
            💳 Transferencia / Mercado Pago
        </label>
        </div>

        <button type="submit" class="btn-principal">Finalizar compra</button>
    </form>

    <div class="carrito-acciones">
        <a href="index.php">← Seguir comprando</a>
        <a href="carrito_vaciar.php" class="link-danger">Vaciar carrito</a>
    </div>
</div>
<script src="../assets/js/ajax.js"></script>
<script src="../assets/js/flash.js"></script>

<?php endif; ?>

</div>
</main>