<?php

include ("../../includes/conexion.php");
$id_parcela = $_REQUEST['id_parcela']/10000;
$id_usuario = $_REQUEST['id_usuario'];

//lo añade a la base de datos
mysqli_query($conexion, "INSERT INTO favoritos (id_parcela, id_usuario) VALUES ('$id_parcela', '$id_usuario')");
/*
//hago una busqueda previa en la tabla favoritos a ver si encuentra el favorito añadido
$sql = "SELECT id_favorito FROM favoritos WHERE id_usuario = '$id_usuario' AND id_parcela = '$id_parcela'";
$queryidfavorito= mysqli_query($conexion, $sql);
$id = mysqli_fetch_assoc($queryiduser);

$id_favorito = $id['id_favorito']
//si lo encuentra lo elimina
    if(!isset($id_favorito)){
        $sql = mysqli_query($conexion, "DELETE FROM favoritos WHERE id_parcela = '$id_parcela'");
    }
    //si lo encuentra no añade favoritos a la tabla

*/



?>
