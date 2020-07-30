<?php
include ("../../includes/conexion.php");

$idUser = $_POST['NombreUser'];
$idParcela = $_POST['parcela'];

$queryParcela = mysqli_query($conexion, "SELECT * FROM asignacionusparc WHERE idUsuarios = '$idUser'");
$rowParcela = mysqli_fetch_assoc($queryParcela);

if($rowParcela != ""){
    mysqli_query($conexion, "DELETE FROM asignacionusparc WHERE idUsuarios = '$idUser' AND idParcelas = '$idParcela'");
}

?>
