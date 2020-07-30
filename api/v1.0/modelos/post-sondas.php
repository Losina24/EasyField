<?php
include ("../../includes/conexion.php");

session_start();

$nombre = $_SESSION['usuario'];

$sqlUser = "SELECT idRol FROM usuarios WHERE nombre_user = '$nombre'";
$query = mysqli_query($conexion, $sqlUser);
$rol = mysqli_fetch_assoc($query);

if( $rol != 3 ) {

    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $nombreParcela = $_POST['nombre'];

    mysqli_query($conexion, "INSERT INTO sondas (latitud_sonda, longitud_sonda) VALUES ('$latitud', '$longitud')");

    $idPosicionQuery = mysqli_query($conexion, "SELECT idPosicion FROM parcelas WHERE id_parcelas = '$nombreParcela'");
    $idPos = mysqli_fetch_assoc($idPosicionQuery);
    $idPosicion = $idPos['idPosicion'];

    $sqlSondas = mysqli_query($conexion, "SELECT id_sondas FROM sondas WHERE latitud_sonda = '$latitud' AND longitud_sonda = '$longitud'");
    $miSondita = mysqli_fetch_assoc($sqlSondas);
    $miSonda = $miSondita['id_sondas'];

    if($idPosicion == ""){
        $quePosicion = mysqli_query($conexion, "SELECT idPosicion FROM parcelas ORDER BY idPosicion DESC LIMIT 0,1");
        $posicionFin = mysqli_fetch_assoc($quePosicion);
        $posicionFinal = $posicionFin['idPosicion'];
        $pos = $posicionFinal;
        mysqli_query($conexion, "INSERT INTO posicion (id_posicion, idSondas) VALUES ('$pos', '$miSonda')");
        mysqli_query($conexion, "UPDATE parcelas SET idPosicion = '$pos' WHERE id_parcelas = '$nombreParcela'");
    } else {
        mysqli_query($conexion, "INSERT INTO posicion (id_posicion, idSondas) VALUES ('$idPosicion','$miSonda')");

    }

    $http_code = 200;
    print_r($http_code);

} else {
    $http_code = 401;
    print_r($http_code);
}

?>
