<?php

include ("../../includes/conexion.php");
$recibido = $_REQUEST['iduser'];

mysqli_query($conexion, "DELETE FROM usuarios WHERE id_user = '$recibido'");

?>
