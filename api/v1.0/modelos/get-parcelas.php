<?php
include ("../api/includes/conexion.php");

// Se inicia la sesión.
session_start();

$contador = 0;
$user = htmlentities($_SESSION['usuario']);

// Se obtiene el id del usuario que tiene la sesión activa.
$sql = "SELECT id_user FROM usuarios WHERE nombre_user = '$user'";
$queryiduser = mysqli_query($conexion, $sql);
$ids = mysqli_fetch_assoc($queryiduser);

$idVar = $ids['id_user']; // ID del usuario.

// Se obtiene el id de las parcelas que pertenecen al usuario cuya sesión está activa.
$sqlidparcelas = "SELECT idParcelas FROM asignacionusparc WHERE idUsuarios = '$idVar'";
$queryidparcelas = mysqli_query($conexion, $sqlidparcelas);
$salidaidparcelas = array();

while ($idparcelas= mysqli_fetch_assoc($queryidparcelas)) {
  // Este array contiene los ids de las parcelas que pertenecen al usuario.
    array_push($salidaidparcelas, $idparcelas);
};

$salida = array();
$esquina = array();
$idSondinas2 = array();
$miSonda = array();

// Se utiliza un bucle para obtener los datos que deseamos con el id de cada parcela.
for ($i=0; $i < count($salidaidparcelas); $i++) {
    $aux = $salidaidparcelas[$i];
    $auxiliar = $aux['idParcelas'];

    // Obtenemos los datos de las parcelas cuyos ids eran los anteriormente obtenidos.
    $sqlparcelas[$i] = "SELECT * FROM parcelas WHERE id_parcelas = '$auxiliar'";
    $queryparcelas = mysqli_query($conexion, $sqlparcelas[$i]);

    while ($parcelas = mysqli_fetch_assoc($queryparcelas)) {
      // En este array se guardan los datos de las parcelas obtenidas.
      array_push($salida, $parcelas);
    }

    $idVertice = $salida[$i]['idVertices'];

    // Obtenemos la latitud y la longitud de los vertices cuyos ids
    $slqvertices[$i] = "SELECT * FROM vertices WHERE id_vertices = '$idVertice'";
    $queryvertices = mysqli_query($conexion, $slqvertices[$i]);

    while($vertices = mysqli_fetch_assoc($queryvertices)){
        array_push($esquina, $vertices);
    }

    // Crear objeto con los vertices //
    $objeto[$i] = [
      "id" => $salida[$i]['id_parcelas'],
      "nombre" => $salida[$i]['Nombre'],
      "color" => "#29c7ef",
      "vertices" => sacarCoordenadas($esquina, $contador)
    ];
    $jsonObj[$i] =  json_encode($objeto[$i]['vertices']);
}
$http_code = 200;

function queCultivo($num){
  if($num == 1){
    $ret = "Árboles";
    return $ret;
  } else if ($num == 2){
    $ret = "Frutas";
    return $ret;
  } else if($num == 3){
    $ret = "Hortalizas";
    return $ret;
  } else if($num == 4){
    $ret = "Cereales";
    return $ret;
  } else {
    $ret = "Varios";
    return $ret;
  }
}

function queIcono($num){
  if($num == 1){
    $ret = "fa-tree-palm";
    return $ret;
  } else if ($num == 2){
    $ret = "fa-apple-alt";
    return $ret;
  } else if($num == 3){
    $ret = "fa-carrot";
    return $ret;
  } else if($num == 4){
    $ret = "fa-wheat";
    return $ret;
  } else {
    $ret = "fa-seedling";
    return $ret;
  }
}

function sacarCoordenadas($t, $cont){
  $corner = [];
  for($num = $cont; $num < count($t); $num++){
      $corner[] = [
        "lat" => floatval($t[$num]['latitud']),
        "lng" => floatval($t[$num]['longitud'])
      ];
      $GLOBALS['contador']++; // <------ Se está intentando buscar una forma alternativa para llegar al mismo punto sin usar var globales
  }
  return $corner;
}
?>
