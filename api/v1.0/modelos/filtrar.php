<?php
include ("../../includes/conexion.php");
include ("get-usuarios.php");

$user = htmlentities($_SESSION['usuario']);

// Se obtiene el id del usuario que tiene la sesión activa.
$sql1 = "SELECT id_user, grupo FROM usuarios WHERE nombre_user = '$user'";
$queryiduser = mysqli_query($conexion, $sql1);
$ids = mysqli_fetch_assoc($queryiduser);

$idVar = $ids['id_user']; // ID del usuario.
$grupo = $ids['grupo']; // Grupo del usuario.
$counter = 0;

$salidaF = "";
$query = "SELECT * FROM usuarios WHERE grupo = '$grupo'";
$query2 = "SELECT * FROM usuarios WHERE grupo = '$grupo'";

if(isset($_POST['consulta']) && $_POST['consulta'] != false){
  $q = $conexion->real_escape_string($_POST['consulta']);

  $query = "SELECT * FROM usuarios WHERE nombre_user LIKE '%".$q."%' AND grupo = '$grupo'";
} else {
  $query = "SELECT * FROM usuarios WHERE grupo = '$grupo'";
}

$resultado = $conexion->query($query);
$resultado2 = $conexion->query($query2);

// Imprime en HTML el resultado de los usuarios que se han búscado en la barra de búsqueda.
if($resultado->num_rows > 0){
  $salidaF.="";
  $i = 0;
  $util = [];
  $util2 = [];

  while($fila2 = $resultado2->fetch_assoc()){
    $util2[] = $fila2;
  }

  $p = 0;

  while($p < count($util2)){
    $fila = $resultado->fetch_assoc();
    if($p == 0){} else {
      if($fila == $util[$p-1]){
        break;
      }
    }

    for($u = 0; $u < count($util2); $u++){
      if($util2[$u] == $fila){
        $util[] = $fila;
        break;
      } else {
        $util[] =  null;
      }
    }
    $p++;
  }

  function medicionColor($medida){
    if($medida >= 50.00 and $medida <= 80.00){
        return "medicion-azul";
    }else if($medida < 50.00 and $medida >= 25.00){
        return "medicion-amarillo";
    } else if($medida > 80.00 and $medida < 90.00){
        return "medicion-amarillo";
    } else {
        return "medicion-rojo";
    }
  };

  function medicionColorTemperatura($medida){
    if($medida >= 15.00 and $medida <= 24.00){
        return "medicion-azul";
    }else if($medida < 15.00 and $medida >= 5.00){
        return "medicion-amarillo";
    } else if($medida > 24.00 and $medida < 28.00){
        return "medicion-amarillo";
    } else {
        return "medicion-rojo";
    }
  };

  function comparar($lista1, $lista2){
    $lista3 = [];
    for ($i=0; $i < count($lista2) ; $i++) {
      for ($j=0; $j < count($lista1); $j++) {
        if($lista2[$i] == $lista1[$j]){
          $lista3[$i] = $lista1[$j];
          break;
        } else {
          $lista3[$i] = null;
        }
      }
    }
    return $lista3;
  };

  $final = comparar($util, $util2);

  for($i = 0; $i < count($final); $i++){
    if($final[$i]==null){}else{
    $us = $util2[$i]['id_user'];
    $abrv = "abrirSondas(".$util2[$i]['id_user'].")";
    $abr = "seleccionarUsuario(\"user".$util2[$i]['id_user']."\", ".$util2[$i]['id_user'].")";
    $salidaF.= "
      <li class='li-parcela'>
        <div class='parcela li-usuario' id='user".$util2[$i]['id_user']."'>
          <div class='texto-parcela texto-usuario' id='".$util2[$i]['id_user']."' onclick='".$abr."'>
            <h2 class='nombre-parcela'>".$util2[$i]['nombre_user']."</h2>
            <h3 class='tipo-parcela'>".getRol($util2[$i]['idRol'])."</h3>
          </div>

          <div class='botones-parcela'>
            <button class='boton-sondas tooltip' onclick='anyadirParcelaUser(".$util2[$i]['id_user'].")'>
              <i class='fas fa-vector-square button-sonda'></i>
              <span class=\"tooltiptext\">Crear Parcela</span>
            </button>
            <button class='boton-sondas tooltip' onclick='buscarParcela(".$util2[$i]['id_user'].")'>
              <i class='fas fa-layer-plus button-sonda'></i>
              <span class=\"tooltiptext\">Asignar Parcela</span>
            </button>
            <button class='boton-sondas tooltip' onclick='quitarParcela(".$util2[$i]['id_user'].")'>
              <i class='fas fa-layer-minus button-sonda'></i>
              <span class=\"tooltiptext\">Declinar Parcela</span>
            </button>
            <button class='boton-sondas button-delete-user tooltip' id='d".$util2[$i]['id_user']."'>
              <i class='fas fa-trash button-sonda'></i>
              <span class=\"tooltiptext\">Borrar Usuario</span>
            </button>

          </div>
        </div>

        <ul id='p".$util2[$i]['id_user']."' class='parcela-admin'>";

        for($x = 0; $x < count($salidaidparcelas_x[$i]); $x++){
          include ("get-sondas-admin.php");
          $idd = "\"id\"";
          $counter = $counter+1;
          $listaParcelas[] = ($salida[$i][$x]['Nombre']);
          $crearSonda = "crearSonda(event, ".$salida[$i][$x]['id_parcelas']." )";
          $salidaF.= "
            <li>
              <div class='parcela'>
                <div class='icono-parcela azul'>
                  <i class='icono fas ".queIcono($salida[$i][$x]['idCultivos'])."'></i>
                </div>

                <div onclick='abrirSondas($(this).attr(".$idd.")+".$util2[$i]['id_user'].")' class='texto-parcela texto-de-la-parcela' id='p".$salida[$i][$x]['id_parcelas']."'>
                  <h2 class='nombre-parcela'>".$salida[$i][$x]['Nombre']."</h2>
                  <h3 class='tipo-parcela'>".queCultivo($salida[$i][$x]['idCultivos'])."</h3>
                </div>

                <div class='botones-parcela'>
                  <button class='boton-sondas tooltip' onclick='mostrarmapa(".$jsonObj[$i][$x]." , ".json_encode($posicion).")'>
                    <i class='fas fa-map button-sonda'></i>
                    <span class=\"tooltiptext\">Ver en el mapa</span>
                  </button>

                  <button class='boton-sondas tooltip' id='d".$salida[$i][$x]['id_parcelas']."' onclick='".$crearSonda."'>
                            <i class='fas fa-map-marker-alt button-sonda'></i>
                            <span class=\"tooltiptext\">Añadir sonda</span>
                  </button>

                  <button class='boton-sondas button-edit tooltip' id='d".$salida[$i][$x]['id_parcelas']."'>
                            <i class='fas fa-edit button-sonda'></i>
                            <span class=\"tooltiptext\">Editar Parcela</span>
                  </button> <!-- Editar -->

                  <button class='boton-sondas button-delete tooltip' id='d".$salida[$i][$x]['id_parcelas']."'>
                    <i class='fas fa-trash button-sonda'></i>
                    <span class=\"tooltiptext\">Borrar Parcela</span>
                  </button>

                  <button id='".$salida[$i][$x]['id_parcelas']."' class='boton-sondas select-parcela tooltip'>
                    <i class='fas fa-check button-sonda check'></i>
                    <span class=\"tooltiptext\">Seleccionar Parcela</span>
                  </button>
                </div>
              </div>

              <ul class='lista-sondas pp".$salida[$i][$x]['id_parcelas'].$util2[$i]['id_user']."'>";

              for ($k=0; $k < count($sondas_resultado); $k++){
                  // Obtener mediciones //
                  $idSondasMed = $sondas_resultado[$k]['idSondas'];

                  $posQuery = mysqli_query($conexion, "SELECT id FROM posicion WHERE idSondas = '$idSondasMed'");
                  $idPosicionMed = mysqli_fetch_assoc($posQuery);
                  $idPosicionM = $idPosicionMed['id'];
                  $medQuery = mysqli_query($conexion, "SELECT * FROM mediciones WHERE idPosicion = '$idPosicionM'");
                  $mediciones[$k] = mysqli_fetch_assoc($medQuery);

                $salidaF.="
                  <li class='li-sonda'>
                    <i class='circulo medicion-azul fas fa-circle'></i>

                    <a class='titulo-sondas' href='mediciones.php?idsonda=".$sondas_resultado[$k]['idSondas']."'>Sonda ".$sondas_resultado[$k]['idSondas']."</a>

                    <div class='mediciones-parcela'>
                      <div class='informacion-parcela'>
                        <p class='datos-medicion ".medicionColor(round($mediciones[$k]['medida_humedad']))."'>".round($mediciones[$k]['medida_humedad'])."%</p>
                        <p class='tipo-medicion'>Hum.</p>
                      </div>
                      <div class='informacion-parcela'>
                        <p class='datos-medicion ".medicionColor(round($mediciones[$k]['medida_salinidad']))."'>".round($mediciones[$k]['medida_salinidad'])."%</p>
                        <p class='tipo-medicion'>Salin.</p>
                      </div>
                      <div class='informacion-parcela'>
                        <p class='datos-medicion ".medicionColorTemperatura(round($mediciones[$k]['medida_temperatura']))."'>".round($mediciones[$k]['medida_temperatura'])."ºC</p>
                        <p class='tipo-medicion'>Temp.</p>
                      </div>
                      <div class='informacion-parcela'>
                        <p class='datos-medicion ". medicionColor(round($mediciones[$k]['medida_luminosidad']))."'>".round($mediciones[$k]['medida_luminosidad'])."%</p>
                        <p class='tipo-medicion'>Lum.</p>
                      </div>

                      <button class='boton-sondas-eliminar tooltip' id=".$sondas_resultado[$k]['idSondas'].">
                        <i class='fas fa-trash button-sonda'></i>
                        <span class=\"tooltiptext\">Borrar Sonda</span>
                      </button>
                    </div>
                  </li>
                ";
              }
          $salidaF.="
              </ul>
            </li>";
        };
    $salidaF.= "
        </ul>
      </li>";

  }
}
} else {
  $salidaF.="<h3 class='sinresultado'>No existen usuarios.</h3>";
}

echo $salidaF;

$conexion->close();
?>
<script src="../app/js/funciones.js"></script>
<script>
    //impresion de editar perfil
    var formEditarPerfil =
        '<section class="crear-parcela-padre" id="alertilla">'+
        '  <article class="crear-parcela-contenedor">'+
        '    <h1 class="crear-parcela-titulo">Editar Perfil</h1>'+
        '    <form class="crear-parcela-form" method="post" onsubmit="enviarPerfil(event)" id="formEditarPerfil" autocomplete="off">'+<?php if($user != 3){ ?>
        '      <label for="NombreUsuario"></label>'+
        '      <input class="caja-label" type="text" placeholder="Nuevo Nombre Usuario" name="nombreUser" required>'+<?php } ?>
        '      <label for="ContrasenyaVieja"></label>'+
        // autocomplete="new-password" hace que no se rellenen las contraseñas
        '      <input class="caja-label" autocomplete="new-password" type="password" autocomplete="off" placeholder="Antigua Contraseña" name="contrasenyaVieja" required  />'+
        '      <label for="ContrasenyaNueva"></label>'+
        '      <input class="caja-label" type="password" placeholder="Nueva Contraseña" name="contrasenyaNueva" required >'+
        '      <label for="ContrasenyaNuevaConfirmar"></label>'+
        '      <input class="caja-label" type="password" placeholder="Confirmar Nueva Contraseña" name="contrasenyaNuevaConfirmar" required >'+
        '      <label for="TelefonoUsuario"></label>'+
        '      <input class="caja-label" type="text" placeholder="Telefono Usuario" name="telefono" autocomplete="off" >'+
        '      <p id="antigua-incorrecta">Error: Antigua contraseña incorrecta</p>'+
        '      <p id="contrasenyas-no-coinciden">La nueva contraseña y confirmacion no coinciden</p>'+
        '      <input class="boton-enviar" type="submit" value="Actualizar">'+
        '    </form>'+
        '   </article>'+
        '   <button href="#" onclick="cerrarJquery()" class="cerrar-formUsuario">+</button>'+
        '</section>';


</script>
