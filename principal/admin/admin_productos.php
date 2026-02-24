<?php
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

$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
$productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel Admin - Productos</title>
     <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<h1 class>Panel de Administración - Productos</h1>

<a href="admin_producto_crear.php" class="admin-btn">➕ Crear Producto</a>
<br><br>

<table class="admin-table th"border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Imagen</th>
        <th>Activo</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($productos as $producto): ?>
        <tr>
            <td><?= $producto['id'] ?></td>
            <td><?= e($producto['nombre']) ?></td>
            <td>$<?= number_format($producto['precio'], 0, ',', '.') ?></td>
            <td><?= $producto['stock'] ?></td>

            <td>
             <?php if (!empty($producto['imagen'])): ?>
               <img src="../../uploads/images/<?= $producto['imagen'] ?>" 
                 style="width:60px; height:60px; object-fit:cover;">
            <?php else: ?>
                Sin imagen
            <?php endif; ?>
            </td>
            
            <td>
                <?= $producto['activo'] ? 'Sí' : 'No' ?>     
            </td>
            <td class="admin-card">
                <a href="admin_producto_editar.php?id=<?= $producto['id'] ?>">✏ Editar</a>
                |
                <a href="admin_producto_toggle.php?id=<?= $producto['id'] ?>">
                    <?= $producto['activo'] ? 'Desactivar' : 'Activar' ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<br>
<a href="../index.php" class="btn-secundario">← Volver a la tienda</a>

</body>
</html>