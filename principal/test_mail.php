<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$ok = mail(
    'bautistaanunez0510@gmail.com',
    'Test mail local',
    'Esto es una prueba desde XAMPP'
);

var_dump($ok);
