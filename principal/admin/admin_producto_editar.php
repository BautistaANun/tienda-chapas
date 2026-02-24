

<?php
/*Este archivo:

Recibe id por GET

Valida que exista

Trae datos del producto

Muestra formulario precargado*/

session_start();

if (
    !isset($_SESSION['usuario']) ||
    !in_array($_SESSION['usuario']['rol'], ['admin','superadmin'], true)
) {
    header('Location: ../index.php');
    exit;
}
require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';

/* =========================
   VALIDAR ID
   ========================= */

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID inválido.");
}

/* =========================
   OBTENER PRODUCTO
   ========================= */

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    die("Producto no encontrado.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<h1 class="contenedor">Editar Producto</h1>

<form action="admin_producto_actualizar.php" method="post" enctype="multipart/form-data" class="contenedor">

    <input type="hidden" name="id" value="<?= $producto['id'] ?>">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?= e($producto['nombre']) ?>" required><br><br>

    <label>Precio:</label><br>
    <input type="number" name="precio" step="0.01" min="0"
           value="<?= $producto['precio'] ?>" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" min="0"
           value="<?= $producto['stock'] ?>" required><br><br>

    <label>Categoría:</label><br>
    <input type="text" name="categoria"
           value="<?= e($producto['categoria']) ?>" required><br><br>

    <label>Nueva Imagen:</label>
    <input type="file" name="imagen" accept="image/*"><br>
    

    <label>Descripción:</label><br>
    <textarea name="descripcion" rows="4" required><?= e($producto['descripcion']) ?></textarea><br><br>

    <label>Activo:</label>
    <select name="activo">
        <option value="1" <?= $producto['activo'] ? 'selected' : '' ?>>Sí</option>
        <option value="0" <?= !$producto['activo'] ? 'selected' : '' ?>>No</option>
    </select><br><br>

    <button type="submit" class="btn-principal">Actualizar Producto</button>
</form>

<br>
<a href="admin_productos.php" class="btn-secundario">← Volver al listado</a>

</body>
</html>