<?php

include ("../../includes/conexion.php");
$id_parcela = $_REQUEST['id_parcela']/10000;

$sql = mysqli_query($conexion, "DELETE FROM favoritos WHERE id_parcela = '$id_parcela'");


?>
