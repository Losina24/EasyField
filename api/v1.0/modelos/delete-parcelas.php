<?php
include ("../../includes/conexion.php");

session_start();

$nombre = $_SESSION['usuario'];

$sql = "SELECT idRol FROM usuarios WHERE nombre_user = '$nombre'";
$query = mysqli_query($conexion, $sql);
$Rol = mysqli_fetch_assoc($query);

if( $Rol['idRol'] != 3 ) {
    $idParcela = $_POST['id_parcela'];

    if(is_array($idParcela) == false){
      mysqli_query($conexion, "DELETE FROM parcelas WHERE id_parcelas = '$idParcela'");
    } else {
      for ($i=0; $i < count($idParcela); $i++) {
        $final = $idParcela[$i];
        mysqli_query($conexion, "DELETE FROM parcelas   WHERE id_parcelas = '$final'");
      }
    }
    $http_code = 200;
    //echo $http_code;
} else {
  $http_code = 401;
  //echo $http_code;
}
