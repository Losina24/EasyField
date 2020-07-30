<?php
include ("../../includes/conexion.php");

session_start();
$usuario = $_SESSION['usuario'];
//hago una sentencia para recoger la contraseña del usuario //
$sql = "SELECT password_user FROM usuarios WHERE nombre_user = '$usuario'";
$query = mysqli_query($conexion, $sql);
$keys = mysqli_fetch_assoc($query);

//hago una sentencia para recoger el rol de usuario
$sql = "SELECT idRol FROM usuarios WHERE nombre_user = '$usuario'";
$query = mysqli_query($conexion, $sql);
$Rol = mysqli_fetch_assoc($query);

//defino las variables recogidas en el formulario
$nombre = $_POST['nombreUser'] ;
$contrasenyaVieja = $_POST['contrasenyaVieja'];
$contrasenyaNueva = $_POST['contrasenyaNueva'];
$contrasenyaNuevaConfirmar = $_POST['contrasenyaNuevaConfirmar'];
$numero = $_POST['telefono'];

//Compruebo que la contraseña Vieja del usuario es igual a la del usuario
if ($contrasenyaVieja == $keys['password_user']) {
    //si la contraseña nueva es igual a la contraseña de confirmacion se procedera con el cambio en la base de datos
    if ($contrasenyaNueva == $contrasenyaNuevaConfirmar) {
            $sqlupdate = "UPDATE `usuarios` SET nombre_user = '$nombre', password_user = '$contrasenyaNueva', telefono_user = '$numero' WHERE nombre_user = '$usuario'";
            mysqli_query($conexion, $sqlupdate);

            //Borra la session antigua y crea una nueva sesion con el nuevo nombre de usuario//
            unset($_SESSION['registrado']);
            session_destroy();
            session_start();
            $_SESSION['registrado'] = 'ok';
            $_SESSION['usuario'] = $nombre;

            $http_code = 200;
    } else {
        //manda codigo 400 si la nueva contrasenya no coincide con la confirmacion
        $http_code = 400;
    }

} else {
    //manda codigo 401 si la contraseña vieja no coincide con ella misma.
    $http_code = 401;
}

?>