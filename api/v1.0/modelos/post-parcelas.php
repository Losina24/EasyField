<?php
include ("../../includes/conexion.php");

session_start();
$usuario = $_SESSION['usuario'];

$sql = "SELECT idRol FROM usuarios WHERE nombre_user = '$usuario'";
$query = mysqli_query($conexion, $sql);
$Rol = mysqli_fetch_assoc($query);

$sqliduser = "SELECT id_user FROM usuarios WHERE nombre_user = '$usuario'";
$queryiduser = mysqli_query($conexion, $sqliduser);
$iduser = mysqli_fetch_assoc($queryiduser);

$posID = mysqli_query($conexion, "SELECT idPosicion FROM parcelas ORDER BY idPosicion DESC LIMIT 0,1");
$posAID = mysqli_fetch_assoc($posID);
$posicionID =  $posAID['idPosicion'] + 1;

$sql = "SELECT id_user FROM usuarios WHERE grupo = '$usuario'";

if( $Rol != 3 ) {
    $cultivos = $_POST['Cultivo'];
		$ret = 0;
		  if($cultivos == "Arboles"){
		    $ret = 1;
		  } else if ($cultivos == "Frutas"){
		    $ret = 2;
		  } else if($cultivos == "Hortalizas"){
		    $ret = 3;
		  } else if($cultivos == "Cereales"){
		    $ret = 4;
		  } else {
		    $ret = 5;
		  }
    $nombre = $_POST['Nombre'];
    $numero = $_POST['numero-vertices'];
    $duenyo = $_POST['NombreUser'];

		$max = mysqli_query($conexion, "SELECT id_vertices FROM vertices ORDER BY id DESC LIMIT 0,1");
		if(mysqli_num_rows($max) == 0){
			$id_vertices = 0;
		} else {
			$id_vertice = mysqli_fetch_assoc($max);
			$id_vertices = $id_vertice['id_vertices'] + 1;
		}
		print_r($id_vertices);

    for ($i=0; $i < $numero; $i++) {
      $lon[] =  $_POST['longitud'][$i];
      $lat[] = $_POST['latitud'][$i];

      mysqli_query($conexion, "INSERT INTO vertices (id_vertices, longitud, latitud) VALUES ('$id_vertices', '$lon[$i]', '$lat[$i]')");
    }

	$sqlt = "INSERT INTO `parcelas` (`id_parcelas`, `idCultivos`, `idVertices`, `idPosicion`, `Nombre`) VALUES (NULL, '$ret', '$id_vertices', $posicionID, '$nombre')";
	mysqli_query($conexion, $sqlt);
	print_r("test2");



	$queryparcelas = mysqli_query($conexion, "SELECT id_parcelas FROM parcelas WHERE idVertices = '$id_vertices'");
	$idparcelas = mysqli_fetch_assoc($queryparcelas);

	$idparcela = $idparcelas['id_parcelas'];
	$id = $iduser['id_user'];

	$sqltt = "INSERT INTO asignacionusparc (idUsuarios, idParcelas ) VALUES ('$duenyo', '$idparcela')";
  mysqli_query($conexion, $sqltt);

    $http_code = 200;


} else {
	$http_code = 401;

}


?>
