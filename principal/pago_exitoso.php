<?php
$id = $_GET['id'] ?? null;
header("Location: compra_detalle.php?id=$id");
exit;
