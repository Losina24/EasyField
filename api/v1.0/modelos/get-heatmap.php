<?php
// IDs de las parcelas del usuario
$miParcela = array();
$miParcela[$i] = $salida[$i]['id_parcelas'];


// SONDAS //
// Seleccionar el id de la posicion donde las parcelas son las que hemos obtenido antes
$posicion_query[$i] = mysqli_query($conexion, "SELECT idPosicion FROM parcelas WHERE id_parcelas = '$miParcela[$i]'");
$posicion_resultado = array();
while($rowPosicion = mysqli_fetch_assoc($posicion_query[$i])){
  $posicion_resultado[$i] = $rowPosicion;
}

// Lista de IDs de las posiciones de cada sonda
$posicionFinal[] = $posicion_resultado[$i]['idPosicion'];

// Consulta que devuelve los IDs de las sondas cuyo ID de la posicion está en el array anterior
$sondas_query[$i] = mysqli_query($conexion, "SELECT idSondas FROM posicion WHERE id_posicion = '$posicionFinal[$i]' AND idSondas IS NOT NULL");
$sondas_resultado = array();
while($rowSondas = mysqli_fetch_assoc($sondas_query[$i])){
  $sondas_resultado[] = $rowSondas;
}
$posicionSonda = array();
$medicionSonda = array();
// GET POSICION // DE CADA SONDA CORRESPONDIENTE
for ($j=0; $j < count($sondas_resultado); $j++) {
    $idSondas = $sondas_resultado[$j]['idSondas'];


    $slqSondas[$j] = "SELECT * FROM sondas WHERE id_sondas = '$idSondas'";
    $querysondas = mysqli_query($conexion, $slqSondas[$j]);

    while ($sondas = mysqli_fetch_assoc($querysondas)) {
        $posicionSonda[] = $sondas;
    }

    //Para esta sql selecciona todos los datos de la tabla posicion
    $sqlPosicion[$j] = "SELECT * FROM posicion WHERE idSondas = '$idSondas'";
    $queryPosicion = mysqli_query($conexion, $sqlPosicion[$j]);

    while ($row = mysqli_fetch_assoc($queryPosicion)){

        $rowf = $row["id"];
        $sqlmediciones[$j] = "SELECT medida_salinidad, medida_temperatura, medida_humedad, medida_luminosidad FROM mediciones WHERE idPosicion = '$rowf' ORDER BY fecha DESC";
        $queryMediciones = mysqli_query($conexion, $sqlmediciones[$j]);

        while ($mediciones = mysqli_fetch_assoc($queryMediciones)) {
            $medicionSonda[] = $mediciones;
        }
    }

    $posicionS = [];
    // Crear objeto con la posicion //
    if(!isset($medicionSonda[$j])){
        $posicion[$j] = [
            "id" => $idSondas,
            "lat" => floatval($posicionSonda[$j]['latitud_sonda']),
            "lng" => floatval($posicionSonda[$j]['longitud_sonda']),
            "humedad" => "No hay datos",
            "temperatura" => "No hay datos",
            "salinidad" => "No hay datos",
            "luminosidad" => "No hay datos"
        ];
    }else{
        $posicion[$j] = [
            "id" => $idSondas,
            "lat" => floatval($posicionSonda[$j]['latitud_sonda']),
            "lng" => floatval($posicionSonda[$j]['longitud_sonda']),
            "humedad" => floatval(substr($medicionSonda[$j]['medida_humedad'], 0, 2)),
            "temperatura" => floatval(substr($medicionSonda[$j]['medida_temperatura'], 0, 2)),
            "salinidad" => floatval(substr($medicionSonda[$j]['medida_salinidad'], 0, 2)),
            "luminosidad" => floatval(substr($medicionSonda[$j]['medida_luminosidad'], 0, 2)),
        ];
    }

    $jsonPosicionS[$j] =  json_encode($posicion[$j]);


}





?>
