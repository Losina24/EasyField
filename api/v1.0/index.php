<?php

require_once '../includes/conexion.php';

//Definimos una constante con la version del api correspondiente

define('API_VERSION', 'v1.0');

//PARSEAR LA URL //

//Obtenemos la parte del path que va despues de la version de la API.

$uri = explode(API_VERSION.'/',parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))[1];
//lo convertimos en un array

$uri_array = explode('/',$uri);

//Obtenemos el recurso solicitado
$recurso = array_shift($uri_array);

//Obtenemos el tipo de operacion solicitada
$operacion = strtolower($_SERVER['REQUEST_METHOD']);

// Nuestro código

$vista = 'json';
$http_code = 404;
// modelo
include "modelos/$operacion-$recurso.php";
// vista
include "vistas/$vista.php";
//////////////

?>