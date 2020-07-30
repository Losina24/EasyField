<?php

session_start();

$contador = 0;
$user = htmlentities($_SESSION['usuario']);

$users = array();
$ids_x = array();
$salidaidparcelas_x = array();

// Se obtiene el id del usuario que tiene la sesión activa.
$sql = "SELECT id_user, grupo FROM usuarios WHERE nombre_user = '$user'";
$queryiduser = mysqli_query($conexion, $sql);
$ids = mysqli_fetch_assoc($queryiduser);

$idVar = $ids['id_user']; // ID del usuario.
$grupo = $ids['grupo']; // Grupo del usuario.


// Obtener los usuarios que forman parte de tu equipo
$sqlTeam = "SELECT * FROM usuarios WHERE grupo = '$grupo'";
$queryTeam = mysqli_query($conexion, $sqlTeam);
while($rowUsers = mysqli_fetch_assoc($queryTeam)){
  $users[] = $rowUsers;
}


// ====== A partir de aqui obtenemos las parcelas de cada usuario ====== //

for ($x=0; $x < count($users); $x++) {
  $nombreUsuario[$x] = $users[$x]['nombre_user'];

  // Se obtiene el id de cada usuario;
  $sql_x = "SELECT id_user FROM usuarios WHERE nombre_user = '$nombreUsuario[$x]'";
  $queryiduser_x[$x] = mysqli_query($conexion, $sql_x);
  while($rowUsuarios[$x] = mysqli_fetch_assoc($queryiduser_x[$x])){
    $ids_x[$x] = $rowUsuarios[$x];
  }

  $idVar_x[$x] = $ids_x[$x]['id_user']; // ID del usuario.

  // Se obtiene el id de las parcelas que pertenecen al usuario cuya sesión está activa.
  $sqlidparcelas_x = "SELECT idParcelas FROM asignacionusparc WHERE idUsuarios = '$idVar_x[$x]'";
  $queryidparcelas_x = mysqli_query($conexion, $sqlidparcelas_x);
  $auxSalida = [];
  while ($idparcelas_x = mysqli_fetch_assoc($queryidparcelas_x)) {
    // Este array contiene los ids de las parcelas que pertenecen al usuario.
    $auxSalida[] = $idparcelas_x;
  };
  $salidaidparcelas_x[$x] = $auxSalida;

  $salida = array();
  $esquina = array();
  $idSondinas2 = array();
  $miSonda = array();
}

// Bucle[i] -> Depende del número de usuarios.
for($i=0; $i < count($salidaidparcelas_x); $i++) {
  // Bucle[j] -> Depende del número de parcelas que tenga cada usuario.
  for($j = 0; $j < count($salidaidparcelas_x[$i]); $j++){
    $aux = $salidaidparcelas_x[$i][$j]['idParcelas'];

    // Obtenemos los datos de las parcelas cuyos ids eran los anteriormente obtenidos.
    $sqlparcelas[$i][$j] = "SELECT * FROM parcelas WHERE id_parcelas = '$aux'";
    $queryparcelas[$i][$j] = mysqli_query($conexion, $sqlparcelas[$i][$j]);

    while($parcelas = mysqli_fetch_assoc($queryparcelas[$i][$j])) {
      // En este array se guardan los datos de las parcelas obtenidas.
      $salida[$i][$j] = $parcelas;
    }

        $idVertice = $salida[$i][$j]['idVertices'];

        // Obtenemos la latitud y la longitud de los vertices cuyos ids
        $slqvertices[$i][$j] = "SELECT * FROM vertices WHERE id_vertices = '$idVertice'";
        $queryvertices = mysqli_query($conexion, $slqvertices[$i][$j]);

        while($vertices = mysqli_fetch_assoc($queryvertices)){
            array_push($esquina, $vertices);
        }

        // Crear objeto con los vertices //
        $objeto[$i][$j] = [
          "id" => $salida[$i][$j]['id_parcelas'],
          "nombre" => $salida[$i][$j]['Nombre'],
          "color" => "#29c7ef",
          "vertices" => sacarCoordenadas($esquina, $contador)
        ];

        $jsonObj[$i][$j] =  json_encode($objeto[$i][$j]['vertices']);
  }
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


// Función que recibe un id de rol y devuelve el nombre del rol en string
// int -> getRol() -> string //
function getRol($rol){
  if($rol == 1){
    return "Administrador";
  } else {
    return "Usuario";
  }
}

?>
