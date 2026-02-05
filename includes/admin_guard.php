<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    mostrarError('Acceso restringido');
}
