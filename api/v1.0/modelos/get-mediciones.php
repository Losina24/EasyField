<?php

	include ("../../includes/conexion.php");

	$data = array();

	$idSonda = $_POST['idSonda'];

	$res = mysqli_query($conexion, "SELECT id FROM posicion WHERE idSondas = '$idSonda'");

	while ($row = mysqli_fetch_assoc($res)){

		$rowf = $row["id"];
		$sqlQuery = mysqli_query($conexion, "SELECT medida_salinidad, medida_temperatura, medida_humedad, medida_luminosidad, fecha FROM mediciones WHERE idPosicion = '$rowf' ORDER BY `fecha` DESC");
	}

	foreach ($sqlQuery as $rown) {
		$data[] = $rown;
	}

    echo json_encode($data);
