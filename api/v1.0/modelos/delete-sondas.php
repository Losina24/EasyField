<?php

include ("../../includes/conexion.php");
$recibido = $_REQUEST['sonda'];

$consulta = mysqli_query($conexion, "SELECT id FROM posicion WHERE idSondas = $recibido");
$row = mysqli_fetch_assoc($consulta);
$final = $row['id'];
$sql = mysqli_query($conexion, "DELETE FROM sondas WHERE id_sondas = '$recibido'");

?>
