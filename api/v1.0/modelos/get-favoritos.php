<?php

$idUser = $_SESSION['usuario'];

//hago una busqueda previa en la tabla favoritos a ver si encuentra el favorito añadido
$sql = "SELECT id_parcela FROM favoritos";
$queryidfavorito= mysqli_query($conexion, $sql);


$favoritos_resultado = array();
while($rowFavoritos = mysqli_fetch_assoc($queryidfavorito)){
    $favoritos_resultado[] = $rowFavoritos['id_parcela']*10000;
}

$favoritos_resultado_id = array();
while($rowFavoritos_id = mysqli_fetch_assoc($queryidfavorito)){
    $favoritos_resultado_id[] = $rowFavoritos_id['id_parcela'];
}




?>
