<?php
session_start();

// Incluimos funciones auxiliares (por ejemplo para mensajes flash)
require __DIR__ . '/../../includes/funciones.php';

// Eliminamos los datos del usuario autenticado
unset($_SESSION['usuario']);

// En un entorno más estricto podríamos destruir completamente la sesión:
// session_unset();
// session_destroy();

// Si se desea mayor seguridad, también se puede eliminar la cookie de sesión
// para evitar reutilización del ID
/*
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
*/

// Mensaje flash que se mostrará en la siguiente petición
setFlash('success', 'Sesión cerrada correctamente');

// Redirección para evitar reenvío y mantener flujo limpio
header('Location: ../index.php');

//Elimino los datos del usuario autenticado de la sesión y redirijo al usuario.
//En un entorno productivo se podría destruir completamente la sesión y eliminar la cookie asociada para mayor seguridad.
exit;