<?php

include ("../../includes/conexion.php");

$id = $_POST['usuario'];
mysqli_query($conexion, "DELETE FROM notificaciones WHERE usuario = '$id'");


?>
