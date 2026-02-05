<?php

function setFlash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = [
        'tipo' => $tipo,      // success | error | info
        'mensaje' => $mensaje
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']); // MUY IMPORTANTE
    return $flash;
}

