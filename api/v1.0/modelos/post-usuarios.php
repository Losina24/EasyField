<?php
include ("../../includes/conexion.php");

session_start();
$usuario = $_SESSION['usuario'];

$sql = "SELECT idRol, grupo FROM usuarios WHERE nombre_user = '$usuario'";
$query = mysqli_query($conexion, $sql);
$ids = mysqli_fetch_assoc($query);

$nombre = $_POST['nombre'];
$contrasenya = $_POST['contrasenya'];
$idRol = $_POST['nombreRol'];
$numero = $_POST['telefono'];
$grupo = $ids['grupo']; // Grupo del usuario.

//hago recogida de todos los usuarios para luego checkear si el nuevo usuario no esta en la base de datos. Si esta, no lo creará//
$slqusuariosCreados = "SELECT * FROM usuarios WHERE nombre_user = '$nombre'";
$queryUsuarios = mysqli_query($conexion, $slqusuariosCreados);
$salidausuarios = mysqli_fetch_assoc($queryUsuarios);

if(empty($salidausuarios)){
    if( $ids['idRol'] != 3) {
        mysqli_query($conexion, "INSERT INTO usuarios (`nombre_user`, `password_user`, `telefono_user`, `idRol`, `grupo`) VALUES ('$nombre', '$contrasenya', '$numero', '$idRol', '$grupo')");
        $http_code = 200;
    }else{
        $http_code = 401;

    }
}else{
    //manda http code error de que el envio no es aceptable, ya existe un usuario
    $http_code = 406;

}
?>
