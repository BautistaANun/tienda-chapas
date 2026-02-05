<?php
session_start();

if (!isset($_POST['id'])) {
    header("Location: carrito.php");
    exit;
}

$id = (int) $_POST['id'];

if (isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

header("Location: carrito.php");
exit;
