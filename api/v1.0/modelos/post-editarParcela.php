<?php
include ("../../includes/conexion.php");

session_start();
$idParcela = $_POST['id_Parcela'];
$idcultivo = $_POST['id-cultivo'];
$nombre = $_POST['nombreParcela'];

mysqli_query($conexion,"UPDATE `parcelas` SET `idCultivos` = '$idcultivo', `Nombre` = '$nombre' WHERE `id_parcelas` = '$idParcela'");
$http_code = 200;
?>
