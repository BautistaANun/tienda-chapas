<?php
session_start();


if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'superadmin') {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../../includes/funciones.php';

?>

<h1>Crear nuevo administrador</h1>


<form action="guardar_nuevoAdmin.php" method="post">

    <input type="text" name="nombre" placeholder="Nombre" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Contraseña" required>

    <label>
        Activo:
        <select name="activo">
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select>
    </label>

    <button type="submit">Guardar</button>

</form>

<p><a href="dashboard.php">← Volver al dashboard</a></p>