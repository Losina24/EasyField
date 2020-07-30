<?php

if ((isset($_POST['usuario']) && $_POST['usuario'] != '') && (isset($_POST['contrasenya']) && $_POST['contrasenya'] != '')) {

    $user = $_POST['usuario'];
    $pass = $_POST['contrasenya'];
    $sql = "SELECT password_user FROM usuarios WHERE nombre_user = '$user'";
    $query = mysqli_query($conexion, $sql);
    $keys = mysqli_fetch_assoc($query);

    if ($pass == $keys['password_user']){
        session_start();
        $_SESSION['registrado'] = 'ok';
        $_SESSION['usuario'] = $user;
        $http_code = 200;
    } else {
        $http_code = 401;
    }
} else {
    $http_code = 400;
}



?>
