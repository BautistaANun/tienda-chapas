<?php

// Verifica que exista una sesión activa
// y que el usuario autenticado tenga rol 'admin'.
// Este archivo debe incluirse al inicio de cualquier
// sección administrativa protegida.
//Esto evita repetir lógica en cada archivo y centraliza la seguridad.

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {

//Mostramos un mensaje genérico para evitar revelar información sensible.
    mostrarError('Acceso restringido');
}

// En producción podría implementarse:
    // - Registro del intento en logs
    // - Redirección a login