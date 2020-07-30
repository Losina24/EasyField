<?php
session_start();

if (!isset($_SESSION['registrado']) || $_SESSION['registrado'] != 'ok' || isset($_SESSION['registrado'])) {
    http_response_code(401);
    die('{"error":"Usted no tiene acceso autorizado"}');

}