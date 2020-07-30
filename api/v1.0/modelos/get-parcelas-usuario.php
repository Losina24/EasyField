<?php
include ("../../includes/conexion.php");

$id = $_POST['id'];

function seleccionarParcelas($usuarioFinal, $con){
    $parcelasFinall = [];

        // Se obtiene el id de las parcelas que pertenecen al usuario.
        $sqlidparcelas = "SELECT idParcelas FROM asignacionusparc WHERE idUsuarios = '$usuarioFinal'";
        $queryidparcelas = mysqli_query($con, $sqlidparcelas);
        $salidaidparcelas = array();

        while ($idparcelas= mysqli_fetch_assoc($queryidparcelas)) {
          // Este array contiene los ids de las parcelas que pertenecen al usuario.
            array_push($salidaidparcelas, $idparcelas);
        };

        $salida = array();
        // Se utiliza un bucle para obtener los datos que deseamos con el id de cada parcela.
        for ($i=0; $i < count($salidaidparcelas); $i++) {
            $aux = $salidaidparcelas[$i];
            $auxiliar = $aux['idParcelas'];

            // Obtenemos los datos de las parcelas cuyos ids eran los anteriormente obtenidos.
            $sqlparcelas[$i] = "SELECT id_parcelas, Nombre FROM parcelas WHERE id_parcelas = '$auxiliar'";
            $queryparcelas = mysqli_query($con, $sqlparcelas[$i]);

            while ($parcelas = mysqli_fetch_assoc($queryparcelas)) {
              // En este array se guardan los datos de las parcelas obtenidas.
              array_push($salida, $parcelas);
            }
        }

        // Ya tenemos las parcelas del usuario guardadas en $salida, ahora comprobamos si esas parcelas ya están en la variable final y si no es así las guardamos en ella.
        for($i = 0; $i < count($salida); $i++){
            if (empty($parcelasFinall)) {
                array_push($parcelasFinall, $salida[$i]);
            } else {
                for ($v=0; $v < count($parcelasFinall); $v++) {
                    if ($salida[$i]['id_parcelas'] != $parcelasFinall[$v]['id_parcelas']) {
                        array_push($parcelasFinall, $salida[$i]);
                    }
                }
            }
        }

    return $parcelasFinall;
}

print_r(json_encode(seleccionarParcelas($id, $conexion)));

?>
