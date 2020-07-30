<?php
include ("../../includes/conexion.php");

$idUser = $_POST['NombreUser'];
$idParcela = $_POST['parcela'];

$queryParcela = mysqli_query($conexion, "SELECT idParcelas FROM asignacionusparc WHERE idUsuarios = '$idUser' AND idParcelas = '$idParcela'");
$rowParcela = mysqli_fetch_assoc($queryParcela);

if($rowParcela['idParcelas'] != $idParcela){
    mysqli_query($conexion, "INSERT INTO asignacionusparc (idUsuarios, idParcelas) VALUES ($idUser, $idParcela)");
    print_r("OK");
}

?>
