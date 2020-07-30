<?php
include '../../includes/acceso.php';
session_start();

if(isset($_SESSION['registrado']) && $_SESSION['registrado'] == 'ok'){
    $http_code = 200;
}else{
    $http_code = 401;
}
