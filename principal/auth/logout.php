<?php
session_start();
require __DIR__ . '/../../includes/funciones.php';

unset($_SESSION['usuario']);

setFlash('success', 'Sesión cerrada correctamente');
header('Location: ../index.php');
exit;