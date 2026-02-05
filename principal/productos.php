
<link rel="stylesheet" href="../assets/css/styles.css">


<?php
session_start();
require __DIR__ . "/../includes/funciones.php";
require __DIR__ . "/../config/database.php";
require __DIR__ . '/../config/config.php';

?>

<header class="header">
     <a href="carrito.php">🛒 Carrito (<?= totalItemsCarrito() ?>)</a>


</header>

<main class="contenedor">
<?php


$id = obtenerGet('id');

if (!ctype_digit($id)) {
    mostrarError("Producto no válido");
}
$categoriaSeleccionada = obtenerGet('categoria');
$busqueda = obtenerGet('q');
$orden = obtenerGet('orden');
$page = max(1, (int) obtenerGet('page'));
$producto = obtenerProductoPorId($pdo, (int)$id);

$orderBy = 'nombre ASC';



if (!$producto) {
    echo "Producto no encontrado";
    exit;
}

$telefono = WHATSAPP_PHONE;
$nombreInstagram = INSTAGRAM_USER;

$mensaje = urlencode(
    "Hola! Me interesa la chapa: " .
    $producto['nombre'] .
    " ($" . $producto['precio'] . ")"
);
?>


<h3><?= e($producto['nombre']) ?></h3>

<div class="producto-detalle">

    <div class="producto-detalle-img">
        <img src="../assets/images/<?= e($producto['imagen']) ?>"
             alt="<?= e($producto['nombre']) ?>">
    </div>

    <div class="producto-detalle-info">
        <h1><?= e($producto['nombre']) ?></h1>

        <p class="precio">$<?= number_format($producto['precio'], 0, ',', '.') ?></p>

        <form action="carrito_agregar.php" method="post">
            <input type="hidden" name="id" value="<?= e($producto['id']) ?>">
            <button type="submit" class="btn-principal">
                🛒 Agregar al carrito
            </button>
        </form>

        <p class="categoria">
            Categoría: <strong><?= e($producto['categoria']) ?></strong>
        </p>

        <p class="descripcion">
            <?= e($producto['descripcion']) ?>
        </p>

        <div class="producto-links">
            <a href="https://wa.me/<?= $telefono ?>?text=<?= $mensaje ?>" target="_blank">
                Pedir por WhatsApp
            </a>

            <a href="https://www.instagram.com/<?= $nombreInstagram ?>" target="_blank">
                Ver Instagram
            </a>
        </div>
    </div>

</div>

<a href="index.php" class="volver">← Volver a la tienda</a>

</main>


