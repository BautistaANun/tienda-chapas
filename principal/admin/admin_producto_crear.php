<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../includes/funciones.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Producto</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>

<h1>Crear Nuevo Producto</h1>

<form action="admin_producto_guardar.php" method="post" enctype="multipart/form-data">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Precio:</label><br>
    <input type="number" name="precio" step="0.01" min="0" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" min="0" required><br><br>

    <label>Categoría:</label><br>
    <input type="text" name="categoria" required><br><br>

    <label>Imagen:</label>
    <input type="file" name="imagen" accept="image/*"><br><br>
    <?php
    // Limitar tamaño a 2MB
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($_FILES['imagen']['size'] > $maxSize) {
    die("La imagen supera el tamaño máximo permitido (2MB).");
    }
    ?>

    <label>Descripción:</label><br>
    <textarea name="descripcion" rows="4" required></textarea><br><br>

    <label>Activo:</label>
    <select name="activo">
        <option value="1">Sí</option>
        <option value="0">No</option>
    </select><br><br>

    <button type="submit">Guardar Producto</button>
</form>

<br>
<a href="admin_productos.php">← Volver al listado</a>

</body>
</html>