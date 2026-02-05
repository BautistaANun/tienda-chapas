
<link rel="stylesheet" href="../assets/css/styles.css">


<?php
session_start();

unset($_SESSION['carrito']);

header("Location: carrito.php");
exit;
