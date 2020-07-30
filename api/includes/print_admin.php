<?php
include ("conexion.php");
include ("../v1.0/modelos/get-usuarios.php");
  // Si el usuario no tiene parcelas a su nombre, entonces aparace un mensaje para que cree una.
  if(empty($users)){
    echo "<h2 class='titulo-php'>Picha en 'Añadir usuarios' para crear un usuario nuevo.</h2>";
  }

  // Funciones que dan el color apropiado a las mediciones de cada sonda.
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

  // Bucle de usuarios
  for($i = 0; $i < count($users); $i++){ ?>

  <!-- Lista de sondas --> <!-- El ID varía dependiendo del ID que tenga la parcela en la BBDD -->
  <li class="li-parcela">
    <div class="parcela li-usuario" id="user<?php print_r($i) ?>">

      <!-- Texto con el nombre del usuario. Se obtienen dichos datos de la BBDD -->
      <div class="texto-parcela texto-usuario" id="<?php print_r($users[$i]['id_user']) ?>" onclick="seleccionarUsuario('user<?php print_r($i) ?>', <?php print_r($users[$i]['id_user']) ?>)">
        <h2 class="nombre-parcela"><?php print_r($users[$i]['nombre_user']) ?></h2>
        <h3 class="tipo-parcela"><?php print_r(getRol($users[$i]['idRol'])) ?></h3>
      </div>

      <!-- Contenedor con los botones que permiten al usuario editar y eliminar -->
      <div class="botones-parcela">
          <button class="boton-sondas" onclick="anyadirParcelaUser(<?php print_r($users[$i]['id_user']) ?>)">
            <i class="fas fa-vector-square button-sonda"></i>
          </button> <!-- Crear -->
          <button class="boton-sondas" onclick="buscarParcela(<?php print_r($users[$i]['id_user']) ?>)">
            <i class="fas fa-layer-plus button-sonda"></i>
        </button> <!-- Asignar Parcela -->
        <button class="boton-sondas" onclick="quitarParcela(<?php print_r($users[$i]['id_user']) ?>)">
          <i class="fas fa-layer-minus button-sonda"></i>
        </button> <!-- Quitar Parcela -->
          <button class="boton-sondas button-delete-user" id="<?php print_r("d".$users[$i]['id_user']) ?>">
            <i class="fas fa-trash button-sonda"></i>
          </button> <!-- Eliminar -->
      </div>
    </div>

    <ul id="<?php print_r("p". $users[$i]['id_user']) ?>" class="parcela-admin">
      <?php for($x = 0; $x < count($salidaidparcelas_x[$i]); $x++){
        include ("../v1.0/modelos/get-sondas-admin.php");
        $listaParcelas[] = ($salida[$i][$x]['Nombre']);
      ?>
        <li>
        <div class="parcela">
          <!-- Icono de la parcela, que depende del tipo de cultivo que tenga asignado en la BBDD -->
          <div class="icono-parcela azul"><i class="icono fas <?php print_r(queIcono($salida[$i][$x]['idCultivos'])) ?>"></i></div>

          <!-- Texto con el nombre de la parcela y el tipo de cultivo que contiene. Se obtienen dichos datos de la BBDD -->
          <div class="texto-parcela texto-de-la-parcela" id="<?php print_r("p".$salida[$i][$x]['id_parcelas']) ?>" onclick="abrirSondas($(this).attr('id')+<?php print_r($users[$i]['id_user']); ?>)">
            <h2 class="nombre-parcela"><?php print_r($salida[$i][$x]['Nombre']) ?></h2>
            <h3 class="tipo-parcela"><?php print_r(queCultivo($salida[$i][$x]['idCultivos'])) ?></h3>
          </div>

          <!-- Contenedor con los botones que permiten al usuario seleccionar, editar, eliminar y ver en el mapa -->
          <div class="botones-parcela">
            <button class="boton-sondas" onclick='mostrarmapa(<?php print_r($jsonObj[$i][$x])?>, <?php print_r(json_encode($posicion))?>)'>
              <i class="fas fa-map button-sonda"></i>
            </button> <!-- Ver en el Mapa -->
            <button class="boton-sondas" onclick="crearSonda(event, <?php print_r($salida[$i][$x]['id_parcelas']) ?> )">
              <i class="fas fa-layer-group button-sonda"></i>
            </button>
            <button class="boton-sondas">
                <i class="fas fa-edit button-sonda"></i>
              </button> <!-- Editar -->
            <button class="boton-sondas button-delete" id="<?php print_r("d".$salida[$i][$x]['id_parcelas']) ?>">
                <i class="fas fa-trash button-sonda"></i>
            </button> <!-- Eliminar -->
            <button id="<?php print_r($salida[$i][$x]['id_parcelas']) ?>" class="boton-sondas select-parcela">
              <i class="fas fa-check button-sonda check"></i>
            </button> <!-- Seleccionar -->
          </div>
        </div>

        <!-- Lista con las sondas que hay dentro de cada parcela -->
        <ul class="lista-sondas <?php print_r("pp".$salida[$i][$x]['id_parcelas'].$users[$i]['id_user']) ?>">
          <?php
            // Bucle que se repite tantas veces como sondas haya en cada parcela
            for ($k=0; $k < count($sondas_resultado); $k++) {
                // Obtener mediciones //
                $idSondasMed = $sondas_resultado[$k]['idSondas'];

                $posQuery = mysqli_query($conexion, "SELECT id FROM posicion WHERE idSondas = '$idSondasMed'");
                $idPosicionMed = mysqli_fetch_assoc($posQuery);
                $idPosicionM = $idPosicionMed['id'];
                $medQuery = mysqli_query($conexion, "SELECT * FROM mediciones WHERE idPosicion = '$idPosicionM'");
                $mediciones[$k] = mysqli_fetch_assoc($medQuery);
          ?>

          <!-- Lista de sondas que contiene cada parcela -->
          <li class="li-sonda">
            <i class="circulo medicion-azul fas fa-circle"></i>

            <a class="titulo-sondas" href="mediciones.php?idsonda=<?php print_r($sondas_resultado[$k]['idSondas']) ?>">Sonda <?php print_r($sondas_resultado[$k]['idSondas']); ?></a>

            <div class="mediciones-parcela">
              <div class="informacion-parcela">
                <p class="datos-medicion <?php print_r(medicionColor(round($mediciones[$k]['medida_humedad']))) ?>"><?php print_r(round($mediciones[$k]['medida_humedad'])) ?>%</p>
                <p class="tipo-medicion">Hum.</p>
              </div>
              <div class="informacion-parcela">
                <p class="datos-medicion <?php print_r(medicionColor(round($mediciones[$k]['medida_salinidad']))) ?>"><?php print_r(round($mediciones[$k]['medida_salinidad'])) ?>%</p>
                <p class="tipo-medicion">Sal.</p>
              </div>
              <div class="informacion-parcela">
                <p class="datos-medicion <?php print_r(medicionColorTemperatura(round($mediciones[$k]['medida_temperatura']))) ?>"><?php print_r(round($mediciones[$k]['medida_temperatura'])) ?>ºC</p>
                <p class="tipo-medicion">Temp.</p>
              </div>
              <div class="informacion-parcela">
                <p class="datos-medicion <?php print_r(medicionColor(round($mediciones[$k]['medida_luminosidad']))) ?>"><?php print_r(round($mediciones[$k]['medida_luminosidad'])) ?>%</p>
                <p class="tipo-medicion">Lum.</p>
              </div>

              <button class="boton-sondas button-delete boton-sonditas" onclick="eliminarSonda(<?php print_r($sondas_resultado[$k]['idSondas']) ?>)">
                <i class="fas fa-trash button-sonda"></i>
              </button> <!-- Eliminar -->
            </div>
          </li>
        <?php } ?>
        </ul>
      </li>
<?php } ?>
</ul>
</li>

<?php } ?>
