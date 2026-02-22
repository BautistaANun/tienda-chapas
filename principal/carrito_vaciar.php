

<?php

/*
|--------------------------------------------------------------------------
| VACIAR CARRITO
|--------------------------------------------------------------------------
|
| Este archivo elimina completamente el carrito almacenado en sesión.
|
| Se utiliza unset() sobre la variable de sesión correspondiente
| para limpiar todos los productos agregados por el usuario.
|
| Mejora técnica:
| - Evitar cualquier salida HTML antes de header() para no romper redirecciones.
|
*/
session_start();

unset($_SESSION['carrito']);

header("Location: carrito.php");
exit;
