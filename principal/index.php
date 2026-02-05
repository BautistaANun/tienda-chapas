
<link rel="stylesheet" href="../assets/css/styles.css">


<?php
session_start();
require __DIR__ . "/../includes/funciones.php";
require __DIR__ . "/../config/database.php";

?>


<main class="contenedor">
<header class="header">
    
    <?php if ($flash = getFlash()): ?>
    <div class="flash <?= e($flash['tipo']) ?>">
        <?= e($flash['mensaje']) ?>
    </div>
    <?php endif; ?>

<nav class="main-nav">
    <a href="index.php" class="logo">Tienda de Chapas</a>

    <div class="nav-right">

        <?php if (isset($_SESSION['usuario'])): ?>
            <div class="nav-user">
                <button class="nav-user-btn">
                    👤 <?= e($_SESSION['usuario']['nombre']) ?>
                </button>

                <div class="nav-dropdown">
                    <a href="mis_compras.php">🧾 Mis compras</a>
                    <a href="carrito.php">
                        🛒 Carrito (<?= totalItemsCarrito() ?>)
                    </a>
                    <hr>
                    <a href="auth/logout.php" class="danger">
                        Cerrar sesión
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="auth/login.php">Iniciar sesión</a>
            <a href="auth/registro.php">Registrarse</a>
            <a href="carrito.php">
                🛒 Carrito (<?= totalItemsCarrito() ?>)
            </a>
        <?php endif; ?>

    </div>
</nav>

</header>


<script src="../assets/js/flash.js"></script>


<?php

$filtros = [
    'categoria'  => obtenerGet('categoria'),
    'busqueda'   => obtenerGet('q'),
    'orden'      => obtenerGet('orden', 'nombre'),
    'page'       => max(1, (int) obtenerGet('page', 1)),
    'porPagina'  => 9
];


$productos = obtenerProductos($pdo, $filtros);
$productosPorCategoria = agruparPorCategoria($productos);

$totalProductos = contarProductos($pdo, $filtros);
$totalPaginas = (int) ceil($totalProductos / $filtros['porPagina']);

?>

<br>

<form method="get">
    <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda ?? '') ?>">

   <?php if (!empty($filtros['categoria'])): ?>
    <input type="hidden" name="categoria" value="<?= e($filtros['categoria']) ?>">
<?php endif; ?>

<?php if (!empty($filtros['orden'])): ?>
    <input type="hidden" name="orden" value="<?= e($filtros['orden']) ?>">
<?php endif; ?>

    
    <button type="submit">Buscar</button>
</form>


<div class="orden">
    <a href="?<?=
        http_build_query([
            'categoria' => $filtros['categoria'],
            'orden' => 'precio_asc'
        ])
    ?>">Precio: menor a mayor</a>

    <a href="?<?=
        http_build_query([
            'categoria' => $filtros['categoria'],
            'orden' => 'precio_desc'
        ])
    ?>">Precio: mayor a menor</a>
</div>


<?php if (empty($productos)): ?>
    <p>No se encontraron coincidencias.</p>
<?php else: ?>

<?php foreach ($productosPorCategoria as $categoria => $lista): ?>

<section class="categoria">
    <h2 class="categoria-titulo"><?= e($categoria) ?></h2>

    <div class="productos-grid">
        <?php foreach ($lista as $producto): ?>

        <article class="producto-card">
            <a href="productos.php?id=<?= e($producto['id']) ?>">
                <img
                    src="../assets/images/<?= e($producto['imagen']) ?>"
                    alt="<?= e($producto['nombre']) ?>"
                >

                <h3><?= e($producto['nombre']) ?></h3>
            </a>

            <p class="precio">$<?= number_format($producto['precio'], 0, ',', '.') ?></p>

            <form action="carrito_agregar.php" method="post">
                <input type="hidden" name="id" value="<?= e($producto['id']) ?>">
                <button type="submit">Agregar al carrito</button>
            </form>
        </article>

        <?php endforeach; ?>
    </div>
</section>

<?php endforeach; ?>

<?php endif; ?>


<?php if ($totalPaginas > 1): ?>
<nav class="paginacion">
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="?<?= http_build_query([
            'page' => $i,
            'categoria' => $categoriaSeleccionada,
            'q' => $filtros['busqueda'],
            'orden' => $filtros['orden']

        ]) ?>"
        <?= $i === $filtros['page'] ?'class="activa"' : '' ?>>
            <?= $i ?>
        </a>
    <?php endfor; ?>
</nav>

<script src="../assets/js/flash.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.flash');

    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
        }, 3000);

        setTimeout(() => {
            flash.remove();
        }, 3500);
    }
});
</script>



<?php endif; ?>
</main>



