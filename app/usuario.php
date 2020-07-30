<?php
error_reporting(0);
// Archivo .php con las sentencias a la bbdd, de las que se extraen los datos necesarios para imprimir las parcelas y las sondas
include ("../api/v1.0/modelos/get-parcelas.php");
include ("../api/v1.0/modelos/get-favoritos.php");
$nombret = $_SESSION['usuario'];

$sqlt = "SELECT idRol FROM usuarios WHERE nombre_user = '$nombret'";
$queryt = mysqli_query($conexion, $sqlt);
$rolt = mysqli_fetch_assoc($queryt);

$user = $rolt['idRol'];
if($user != 3){
  header("Location: administrador.php");
}

$sqltt = "SELECT id_user FROM usuarios WHERE nombre_user = '$nombret'";
$querytt = mysqli_query($conexion, $sqltt);
$iduser = mysqli_fetch_assoc($querytt);
$id_user = $iduser['id_user'];

// Notificaciones.

$sqlID="SELECT id_user FROM usuarios WHERE nombre_user = '$nombret'";
$resultT=mysqli_query($conexion, $sqlID);
$rowT = mysqli_fetch_assoc($resultT);

    $rowf = $rowT['id_user'];
    $sqlQuery = "SELECT * FROM notificaciones WHERE usuario = '$rowf'";
    $res=mysqli_query($conexion, $sqlQuery);
    $resNot = [];
    while($mostrar = mysqli_fetch_assoc($res)){
        $resNot[] = $mostrar;
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bienvenido <?php print_r($_SESSION['usuario'])?></title>

    <!-- Estilos -->
    <link href="css/index.css" rel="stylesheet">
    <link href="fonts/fonts.css" rel="stylesheet">

</head>

<body>
  <!-- MENÚ DESLIZABLE - NOTIFICACIONES -->
  <nav class="menu-de-notificaciones">
    <div class="contenedor-menu">
        <button id="botonMenu" type="button" name="cerrarMenu" onclick="clickBell(event)"><i class="fal fa-times"></i></button>
        <button id="botonBorrar" type="button" name="cerrarMenu" onclick="deleteBell()"><i class="fas fa-trash"></i></button>
    </div>
    <h2 class="titulo-notificaciones">Notificaciones</h2>
    <ul class="lista-notificaciones">
        <?php for($v = 0; $v < count($resNot); $v++){
            if($v == 10){break;} ?>
            <li class="cuadro-notificaciones">
                <div class="icono-notificaciones medicion-rojo"><i class="fas fa-exclamation-triangle"></i></div>
                <p class="texto-alerta"><?php print_r($resNot[$v]['mensaje']) ?></p>
            </li>
        <?php } ?>
    </ul>
  </nav>
  <div class="fondo-menu" onclick="clickBell(event)"></div>


  <div class="cuerpo-pagina" id="cuerpo"> <!-- Cuerpo de la página -->

    <!-- HEADER -->
    <header class="encabezado">
      <a href="usuario.php" class="enlace-imagen"><img class="logotipo-encabezado" src="images/Logotype.png" alt="Logotipo de la empresa"></a> <!-- Imagotipo -->

      <a class="boton-cerrar-sesion"href="javascript: fetch ('../api/v1.0/sesion',{method:'delete'}).then( function(respuesta) { if(respuesta.status== 200) location .href= '..'; })" >Cerrar Sesión</a>
    </header>


    <!-- MENSAJE DE CORRECTO CUANDO UN ELEMENTO SE ELIMINA-->
      <div id="alerta-correcto">
          <!-- Le inserto un id para editarlo en javascript dependiendo del elemento -->
          <h2 id="nombre-elemento-eliminado">El elemento ha sido eliminado</h2>
      </div>
      <!-- MENSAJE DE ERROR CUANDO ALGO VA MAL-->
      <div id="alerta-error">
          <!-- Le inserto un id para editarlo en javascript dependiendo del elemento -->
          <h2 id="nombre-elemento-error">Mensaje de error</h2>
      </div>


    <!-- CONTENIDOS -->
    <section class="seccion-principal">
      <div class="banner-superior">
        <h1 class="titulo-parcelas">Parcelas</h1>

        <div>
            <button type="button" name="config" id="bell" onclick="clickBell(event)"><i class="fas fa-bell"></i><?php if(count($resNot)>0){?> <i class="fas fa-circle punto-alerta"></i> <?php } ?></button>
            <button type="button" name="config" id="cog" onclick="clickPuntos(event)"><i class="fas fa-cog"></i></button>
        </div>
      </div>

      <article class="apartado-parcelas"> <!-- Apartado que contiene la lista de las parcelas -->
        <ul class="lista-de-parcelas">


          <?php
            // Si el usuario no tiene parcelas a su nombre, entonces aparace un mensaje para que cree una
            if(empty($salidaidparcelas)){
              echo "<h2 class='titulo-php'>No tienes parcelas</h2>";
            }

            // Bucle que se repite tantas veces como parcelas haya en la lista de parcelas del usuario
            for ($i=0; $i < count($salida); $i++) {

            // GET Sondas, obtiene la lista de sondas de cada parcela
            include ("../api/v1.0/modelos/get-sondas.php");
            ?>
              <!-- Lista de sondas --> <!-- El ID varía dependiendo del ID que tenga la parcela en la BBDD -->
              <li id="<?php print_r("p".$salida[$i]['id_parcelas']) ?>" class="li-parcela <?php print_r("p".$salida[$i]['id_parcelas']) ?>">
                <div class="parcela">

                  <!-- Icono de la parcela, que depende del tipo de cultivo que tenga asignado en la BBDD -->
                  <div class="icono-parcela azul"><i class="icono fas <?php print_r(queIcono($salida[$i]['idCultivos'])) ?>"></i></div>

                  <!-- Texto con el nombre de la parcela y el tipo de cultivo que contiene. Se obtienen dichos datos de la BBDD -->
                  <div class="texto-parcela" id="<?php print_r("p".$salida[$i]['id_parcelas']) ?>">
                    <h2 class="nombre-parcela truncate"><?php print_r($salida[$i]['Nombre']) ?></h2>
                    <h3 class="tipo-parcela"><?php print_r(queCultivo($salida[$i]['idCultivos'])) ?></h3>
                  </div>

                  <!-- Contenedor con los botones que permiten al usuario seleccionar, editar, eliminar y ver en el mapa -->
                  <div class="botones-parcela">
                    <button class="boton-sondas tooltip" onclick='mostrarmapa(<?php print_r($jsonObj[$i])?>, <?php print_r(json_encode($posicion))?>)'>
                      <i class="fas fa-map button-sonda"></i>
                        <span class="tooltiptext">Ver en el Mapa</span>
                    </button> <!-- Ver en el Mapa -->
                      <button class="boton-sondas button-edit tooltip" id="<?php print_r("d".$salida[$i]['id_parcelas']) ?>">
                          <i class="fas fa-edit button-sonda"></i>
                          <span class="tooltiptext">Editar Parcela</span>
                      </button> <!-- Editar -->
                    <button id="<?php print_r($salida[$i]['id_parcelas']) ?>" class="boton-sondas select-parcela tooltip">
                      <i class="fas fa-check button-sonda check"></i>
                        <span class="tooltiptext">Seleccionar Parcela</span>
                    </button> <!-- Seleccionar -->
                      <button id="<?php print_r($salida[$i]['id_parcelas']*10000) ?>" class="boton-sondas select-parcela-favorita tooltip">
                          <i class="fas fa-star button-sonda favorita"></i>
                          <span class="tooltiptext">Añadir Favorito</span>
                      </button> <!-- Seleccionar -->
                  </div>
                </div>

                <!-- Lista con las sondas que hay dentro de cada parcela -->
                <ul class="lista-sondas" id="<?php print_r("pp".$salida[$i]['id_parcelas']) ?>">
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
                  </li>
                <?php } ?>
                </ul>
              </li>
            <?php } ?>
        </ul>



      </article>
    </section>

      <!-- MENSAJE DE ALERTA AL ELIMINAR ALGUN ELEMENTO -->
      <div id="confirmacion-eliminar">
           <!-- Le inserto una id al h2 para poder modificar el mensaje en JS -->
          <h2 id="alerta-elemento-eliminar">¿Estas seguro de que quieres eliminar este elemento?</h2>
          <h3>No se podrán deshacer los cambios</h3>
          <input type="button" value="Eliminar" id="delete">
          <input type="button" value="Cancelar" id="cancel">
      </div>
      <div class="fondo-alerta" id="fondoAlerta"></div>


  <!-- ===== CONTENIDO INVISIBLE ===== -->
  <!-- Estos contenedores se utilizan para imprimir código html dentro utilizando funciones de JavaScript -->
  <div id="contenedor-jquery"></div>
  </div>

  <!-- MAPA -->
  <article id="padre-mapa">
      <div id="map"></div>
      <button href="#" class="cerrarmapa" onclick="cerrarMapa()">+</button>
      <button href="#" class="mapa-de-calor mdc1" onclick="hm('humedad')"><i class="fal fa-tint map-icon icon-1"></i></button>
      <button href="#" class="mapa-de-calor mdc2" onclick="hm('salinidad')"><i class="fal fa-mountains map-icon icon-2"></i></button>
      <button href="#" class="mapa-de-calor mdc3" onclick="hm('temperatura')"><i class="fal fa-thermometer-half map-icon icon-3"></i></button>
      <button href="#" class="mapa-de-calor mdc4" onclick="hm('luminosidad')"><i class="fal fa-sun map-icon icon-4"></i></button>

      <div class="leyenda ley-hum">
        <div class="barra-color-hum"></div>
        <div class="texto-leyenda">
            <p>70%</p>
            <p>50%</p>
            <p>20%</p>
        </div>
      </div>

      <div class="leyenda ley-sal">
        <div class="barra-color-sal"></div>
        <div class="texto-leyenda">
            <p>70%</p>
            <p>50%</p>
            <p>20%</p>
        </div>
      </div>

      <div class="leyenda ley-lum">
        <div class="barra-color-lum"></div>
        <div class="texto-leyenda">
            <p>70%</p>
            <p>50%</p>
            <p>20%</p>
        </div>
      </div>

      <div class="leyenda ley-calor">
        <div class="barra-color"></div>
        <div class="texto-leyenda-2">
            <p>25º</p>
            <p>15º</p>
            <p>5º</p>
        </div>
      </div>
  </article>

  <!-- SCRIPTS -->
  <script src="js/jquery.min.js"></script> <!-- jQuery -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> <!-- jQuery UI -->

  <script>
      //funcion que ejecuta que se vea la alerta de "se ha eliminado"
      function alertaCorrecto(){
          document.getElementById("alerta-correcto").style.display = "flex";
          //despues de 3 segundos la alerta desaparece //
          setTimeout(function(){
              $("#alerta-correcto").slideUp();
              document.getElementById("fondoAlerta").style.display = "none";
          },3000)
      }

      // Función que ejecuta que se vea la alerta de que ha habido algun error //
      function alertaError(){
          document.getElementById("alerta-error").style.display = "flex";
          //despues de 3 segundos la alerta desaparece //
          setTimeout(function(){
              $("#alerta-error").slideUp();
              document.getElementById("fondoAlerta").style.display = "none";
          },3300)
      }

      // Cuando la pagina es refrescada ejecuta la funcion correspondiente con las alertas //
      window.onload = function() {
          //si encuentra cualquiera de las variables, la elimina y ejecuta la funcion.
          var actualizado = sessionStorage.getItem("actualizado");
          var parcelaEditada = sessionStorage.getItem("parcelaEditada");

          if(actualizado){
              sessionStorage.removeItem("actualizado");
              document.getElementById("nombre-elemento-eliminado").innerHTML = "La perfil se ha actualizado correctamente";
              alertaCorrecto();
          }
          if(parcelaEditada){
              sessionStorage.removeItem("parcelaEditada");
              document.getElementById("nombre-elemento-eliminado").innerHTML = "La parcela se ha editado correctamente";
              alertaCorrecto();
          }

      };


      // Función que abre y cierra el menu lateral de notificaciones //
      function clickBell(event){
          $('.menu-de-notificaciones').stop().animate({
              width: 'toggle'
          }, 500);
          if($('.menu-de-notificaciones').hasClass('activo')){
              $('.fondo-menu').fadeTo(500, 0);
              $('.cuadro-notificaciones').fadeTo(250, 0);
              $('#botonMenu').fadeTo(250, 0);
              $('#botonBorrar').fadeTo(250, 0);
              $('.titulo-notificaciones').fadeTo(250, 0);
              $('#cuerpo').removeClass('blur');
              $('.menu-de-notificaciones').removeClass('activo');
              setTimeout(function(){
                  $('.menu-de-notificaciones').css('display','none');
                  $('.fondo-menu').css('display','none');
              }, 400);
          } else {
              $('.menu-de-notificaciones').addClass('activo');
              $('.fondo-menu').fadeTo(500, 0.35);
              $('#cuerpo').addClass('blur');
              $('.menu-de-notificaciones').css('display','flex');
              $('.fondo-menu').css('display','block');
              setTimeout(function(){
                  $('.cuadro-notificaciones').fadeTo(250, 1);
                  $('#botonMenu').fadeTo(250, 1);
                  $('#botonBorrar').fadeTo(250, 1);
                  $('.titulo-notificaciones').fadeTo(250, 1);
              }, 200);
          }
      }

      function deleteBell(){
          $.ajax({
              type: "post",
              url: "../api/v1.0/modelos/delete-notificaciones.php",
              data: {usuario: <?php print_r($id_user)?>},
              success: function(data){
                  $(".cuadro-notificaciones").effect("drop", {direction: "right"}, 500);
                  setTimeout(function(){
                      $(".lista-notificaciones").empty();
                  },550);
              },
              error: function(data){
                  alert("Problemas al tratar de enviar el formulario");
              }
          });
      }

  // Variable que contiene el código html del formulario para crear parcelas
  var imprimir = '<section class="crear-parcela-padre" id="alertilla">'+
  '      <article class="crear-parcela-contenedor">'+
  '        <h1 class="crear-parcela-titulo">Crear parcela</h1>'+
  '        <form class="crear-parcela-form" method="post" onsubmit="enviarparcela(event)"  id="formularioCrearParcela">'+
  '          <!-- NOMBRE DE LA PARCELA -->'+
  '	  <label for="nombre"></label>'+
  '	  <input class="caja-label nombre-input" type="text" placeholder="Nombre" id="inputNombre" name="Nombre" required>'+
  '	  <!-- CULTIVO -->'+
  '          <label for="cultivo"></label>'+
  '	  <select class="seleccionar-cultivo caja-label select-sondas" name="Cultivo" value="Cultivo" required>'+
  '            <option value="" selected disabled hidden>Cultivo</option>'+
  '            <option value="Frutas">Frutas</option>'+
  '            <option value="Hortalizas">Hortalizas</option>'+
  '            <option value="Cereales">Cereales</option>'+
  '            <option value="Arboles">Árboles</option>'+
  '            <option value="Varios">Varios</option>'+
  '          </select>'+
  '          <!-- NUMERO DE VERTICES -->'+
  '          <label for="numero-vertices"></label>'+
  '	  <select class="seleccionar-cultivo caja-label select-sondas" name="numero-vertices" value="Número de vértices" id="vertices-select" onchange="vertices()" required>'+
  '            <option value="" selected disabled hidden>Número de vértices</option>'+
  '            <option value="3" class="opcion-vertice">3</option>'+
  '            <option value="4" class="opcion-vertice">4</option>'+
  '            <option value="5" class="opcion-vertice">5</option>'+
  '            <option value="6" class="opcion-vertice">6</option>'+
  '            <option value="7" class="opcion-vertice">7</option>'+
  '            <option value="8" class="opcion-vertice">8</option>'+
  '            <option value="9" class="opcion-vertice">9</option>'+
  '            <option value="10" class="opcion-vertice">10</option>'+
  '          </select>'+
  '          <!-- VÉRTICES -->'+
  '          <div class="contenedor-vertices">'+
  '          </div>'+
  '	  <!-- SUBMIT -->'+
  '	  <input class="boton-enviar" type="submit">'+
  '        </form>'+
  '      </article>'+
  '      <button href="#" onclick="cerrarJquery()" class="cerrar">+</button>'+
  '    </section>';

  var phpLista = "<?php for ($i=0; $i < count($salida); $i++) { ?> <option class='opcion-vertice'><?php print_r($salida[$i]['Nombre']) ?></option><?php } ?>";

  // Variable con el codigo html que contiene el formulario para crear sondas
  var formSonda =
  '<section class="crear-parcela-padre" id="alertilla">'+
  '  <article class="crear-parcela-contenedor">'+
  '    <h1 class="crear-parcela-titulo">Crear sonda</h1>'+
  '    <form class="crear-parcela-form" method="post" action="../api/v1.0/modelos/post-sondas.php" id="form-sondas">'+
  '      <label for="nombre-parcela"></label>'+
  '      <select class="seleccionar-cultivo caja-label select-sondas" name="nombre" required>'+
  '      '+phpLista+
  '      </select>'+
  '      <label for="longitud"></label>'+
  '      <input class="caja-label" type="number" step="0.00000000001" placeholder="Posición de la sonda (Longitud)" name="longitud" required>'+
  '      <label for="latitud"></label>'+
  '      <input class="caja-label" type="number" step="0.00000000001" placeholder="Posición de la sonda (Latitud)" name="latitud" required>'+
  '      <input class="boton-enviar" type="submit" value="Crear">'+
  '    </form>'+
  '   </article>'+
  '   <button href="#" onclick="cerrarJquery()" class="cerrar">+</button>'+
  '</section>';

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

      //variable que contiene el formulario de editar parcela //
      var formEditarParcela =
          '<section class="crear-parcela-padre" id="alertilla">'+
          '  <article class="crear-parcela-contenedor">'+
          '    <h1 class="crear-parcela-titulo">Editar Parcela</h1>'+
          '    <form class="crear-parcela-form" method="post" onsubmit="enviarEditarParcela(event)" id="formEditarParcela">'+
          '      <label for="NombreParcela"></label>'+
          '      <input class="caja-label" type="text" placeholder="Nuevo Nombre parcela" name="nombreParcela" required>'+
          '	     <select class="seleccionar-cultivo caja-label select-sondas" name="id-cultivo" required>'+
          '            <option value="" selected disabled hidden>Tipo Cultivo</option>'+
          '            <option value="1" class="opcion-vertice">Arboles</option>'+
          '            <option value="2" class="opcion-vertice">Frutas</option>'+
          '            <option value="3" class="opcion-vertice">Hortalizas</option>'+
          '            <option value="4" class="opcion-vertice">Cereales</option>'+
          '            <option value="5" class="opcion-vertice">Varios</option>'+
          '      </select>'+
          '      <input class="boton-enviar" type="submit" value="Actualizar">'+
          '    </form>'+
          '   </article>'+
          '   <button href="#" onclick="cerrarJquery()" class="cerrar-editP">+</button>'+
          '</section>';

  // Vacia el contenedor que se utiliza para imprimir los formularios de crear sondas y parcelas.
  function cerrarJquery(){
    $("#contenedor-jquery").html("");
  }
  //imprime en la pagina el formulario editar perfil
  function editarPerfil(){
      cerrarPuntos();
      $("#contenedor-jquery").html(formEditarPerfil);
      $("#formEditarPerfil").append("<input type='hidden' name='nombreUser' value='<?php print_r($nombret) ?>' />");
  }

  // Llama a una función que selecciona una parcela
  var attrPrint = "seleccionar($(this).attr('id'))";

  // Llama a una función que selecciona todas las parcelas
  var selectAll = "seleccionarTodo()";

  // Código html de un recuadro con opciones // Esto es lo que muestra la ruedecita //
  var printOpciones = '<div class="etiqueta-opciones" id="tag">'+
  '    <ul class="lista-opciones">'+
  '      <li class="elemento-opciones">'+
  '           <a id="p1" href="#" onclick="'+ selectAll +'">Seleccionar todo</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#">Editar Parcelas</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#" onclick="editarPerfil()">Editar Perfil</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#" onclick="mostrarFavoritas()">Mostrar favoritas</a>'+
  '      </li>'+
  '    </ul>'+
  '  </div>';

  // Imprime el codigo html del recuadro en el lugar donde se hace click
  function clickPuntos(event){
    if ($('.cuerpo-pagina').hasClass("cuadro-activo")){
      $(".etiqueta-opciones").remove();
      $(".cuadro-activo").removeClass("cuadro-activo");
    } else {
    $('body').append(printOpciones)
    $('.etiqueta-opciones').css({
      "right": 50 + 'px',
      "top": event.pageY + 'px'
    });
    $('.cuerpo-pagina').addClass("cuadro-activo");
    }
  }


  // Cierra el recuadro con opciones cuando se hace click en el otra vez
  function cerrarPuntos(){
    if ($('.cuerpo-pagina').hasClass("cuadro-activo")){
      $(".etiqueta-opciones").remove();
      $(".etiqueta-opciones-2").remove();
      $(".cuadro-activo").removeClass("cuadro-activo");
    }
  }

  // Cierra el recuadro con opciones cuando se hace click fuera de él
  $(document).mouseup(function(e){
    var container = $("#tag");
    if (!container.is(e.target) && container.has(e.target).length === 0){
        container.remove();
        cerrarPuntos();
    }
  });

  // Formulario para crear vértices
  var verticesImpresion = '<h2 class=\'titulo-vertices\'>Vértice</h2>'+
  '<label for=\'latitud\'></label>'+
  '<input class=\'caja-label\' type=\'number\' step=\'0.00000000001\' placeholder=\'Posición del vértice (Longitud)\' name=\'longitud[]\' required>'+
  '<label for=\'vertices\'></label>'+
  '<input class=\'caja-label\' type=\'number\' step=\'0.00000000001\' placeholder=\'Posición del vértice (Latitud)\' name=\'latitud[]\' required>';

  // Añade vertices al formulario en función del número de vértices elegido
  function vertices(){
    $(".contenedor-vertices").empty();
    var valor = $("#vertices-select option:selected").text();
    for(var i=0; i<valor; i++){
      $(".contenedor-vertices").append(verticesImpresion);
    }
  }

  // Selecciona una parcela
  function seleccionar(p){
    if($("#"+p).find(".check").hasClass("selected")){
      $("#"+p).removeClass("seleccionado");
      $("#"+p).find(".check").removeClass("selected");
    } else {
      $("#"+p).find(".check").addClass("selected");
      $("#"+p).addClass("seleccionado");
    }
  }

      $(".select-parcela").on("click", function(){
          var idPar = $(this).attr('id');
          seleccionar(idPar);
      });

      $(".select-parcela").on("click", function(){
          var idPar = $(this).attr('id');
          seleccionar(idPar);
      });

      //For que detecta si la estrella esta en la base de datos, si esta la pinta de rojo
      <?php
      for ($i=0; $i < count($favoritos_resultado); $i++) {
      ?>
      if(<?php print_r($favoritos_resultado[$i])?>){
          $("#"+<?php print_r($favoritos_resultado[$i])?>).find(".favorita").addClass("seleccionada");
          $("#"+<?php print_r($favoritos_resultado[$i])?>).addClass("seleccionadas");
      }
      <?php } ?>

      function anyadirfavorita(p){
          if($("#"+p).find(".favorita").hasClass("seleccionada")){
              $.ajax({
                  type: "post",
                  url: "../api/v1.0/modelos/delete-favoritos.php",
                  data: { id_parcela: p},
                  beforeSend: function(){},
                  complete:function(data){},
                  success: function(data){
                      $("#"+p).removeClass("seleccionadas");
                      $("#"+p).find(".favorita").removeClass("seleccionada");
                      location.reload();
                  },
                  error: function(data){
                      alert("Problemas al tratar de enviar el formulario");
                  }
              });

          } else {
              $.ajax({
                  type: "post",
                  url: "../api/v1.0/modelos/post-favoritos.php",
                  data: { id_parcela: p , id_usuario: <?php print_r($id_user)?>},
                  beforeSend: function(){},
                  complete:function(data){},
                  success: function(data){
                      $("#"+p).find(".favorita").addClass("seleccionada");
                      $("#"+p).addClass("seleccionadas");
                      location.reload();
                  },
                  error: function(data){
                      alert("Problemas al tratar de enviar el formulario");
                  }
              });

          }
      }

      $(".select-parcela-favorita").on("click", function(){
          var idPar = $(this).attr('id');
          anyadirfavorita(idPar);
      });

      var seleccionados = 0;
      function mostrarFavoritas(){

          if(seleccionados == 0) {
              $(".li-parcela").slideUp(500);
              <?php
              for ($i=0; $i < count($favoritos_resultado); $i++) {
              ?>
              var id = <?php print_r($favoritos_resultado[$i])?>/10000;
              $(".p" + id).slideDown(500);
              seleccionados++;
              cerrarPuntos();
              <?php } ?>
          }else{
              $(".li-parcela").slideDown();
              seleccionados = 0;
              cerrarPuntos();
          }


      }



  // Selecciona todas las parcelas
  function seleccionarTodo(){
      cerrarPuntos();
    if($(".check").hasClass("selected")){
      $(".check").removeClass("selected");
      $(".check").parent().removeClass("seleccionado");
    } else {
      $(".check").addClass("selected");
      $(".check").parent().addClass("seleccionado");
    }
  }


  // Despliega la lista de sondas de una parcela cuando se hace click dentro de esta
  $(".texto-parcela").on("click", function(){
    var miID = $(this).attr('id');
    $("#p" + miID).stop().slideToggle(400);
  });

  // FUNCIONES DEL MAPA //

  var heatH, heatT, heatS, heatL;
  var map, hmStatus;
  var heatmap = new Array();
  function initMap() {
     map = new google.maps.Map(document.getElementById('map'), {
         center: {lat: 38.9965055, lng: -0.1674364},
         zoom: 15,
         mapTypeId: 'satellite',
         styles: [
             {
                 featureType: 'poi',
                 stylers: [{visibility: 'off'}]
             },
             {
                 featureType: 'transit',
                 stylers: [{visibility: 'off'}]
             }
         ],
         mapTypeControl: false,
         streetViewControl: false,
         rotateControl: false,
     });

     map.setTilt(0);
 }

 function hm(dato){
     if(dato == "humedad"){
         if(dato == hmStatus){
             heatH.setMap(heatH.getMap() ? null : map);
             hmStatus = "";
         } else {
             heatH.setMap(heatH.getMap() ? null : map);
             hmStatus = "humedad";
         }
     }

     if(dato == "temperatura"){
         if(dato == hmStatus){
             heatT.setMap(heatT.getMap() ? null : map);
             hmStatus = "";
         } else {
             heatT.setMap(heatT.getMap() ? null : map);
             hmStatus = "temperatura";
         }
     }

     if(dato == "salinidad"){
         if(dato == hmStatus){
             heatS.setMap(heatS.getMap() ? null : map);
             hmStatus = "";
         } else {
             heatS.setMap(heatS.getMap() ? null : map);
             hmStatus = "salinidad";
         }
     }

     if(dato == "luminosidad"){
         if(dato == hmStatus){
             heatL.setMap(heatL.getMap() ? null : map);
             hmStatus = "";
         } else {
             heatL.setMap(heatL.getMap() ? null : map);
             hmStatus = "luminosidad";
         }
     }
 }

 var contadorHM = 0;
 function mostrarmapa(vertices, posiciones){
   document.getElementById("padre-mapa").style.display = "flex";
   dibujarparcela(vertices);
     cerrarPuntos();
   var countHM = [];
   for (var i=0; i<posiciones.length; i++){
     let arrayPos = posiciones[i];
     cargarPosiciones(arrayPos);
     countHM.push(arrayPos);
   }
   cargarHeatmap(countHM);
   cerrarPuntos();
 }

      function recomendar(h,t){
          if(h>80){
              if(t>25){
                  return "Aguacates";
              }else{
                  return "Arroz";
              }
          } else if(h>40){
              if(t>25){
                 return "Garbanzos";
              }else if(t>12){
                 return "Tomates";
              }else{
                 return "Patatas";
              }
          }else{
              if(t>25){
                 return "Nísperos";
              }else if(t>12){
                 return "Olivos";
              }else{
                 return "Vides";
              }
          }
      }

       function cargarPosiciones(posicion){
           var marker = new google.maps.Marker({
               position: posicion,
               animation: google.maps.Animation.DROP,
               map: map,
               label: posicion.id
           });

           //contenido del popup//
           var contentString = '<div id="container-popup">'+
               '<h1 class="header-popup">Sonda ' +
                   posicion.id+
               '</h1>'+
               '<div class="container-mediciones">'+
                 '<div>'+
               '<p class="popup-text"><i class="fal fa-tint icono-popup fal-hum"></i><span class="medidaColor">' +
               posicion.humedad+
               ' %</span></p>'+
               '<p class="popup-text"><i class="fal fa-mountains icono-popup fal-sal"></i><span class="medidaColor">' +
               posicion.salinidad+
               ' %</span></p>'+
                 '</div>'+
                 '<div>'+
               '<p class="popup-text"><i class="fal fa-thermometer-half icono-popup fal-temp"></i><span class="medidaColor">' +
               posicion.temperatura+
               ' ºC</span></p>'+
               '<p class="popup-text"><i class="fal fa-sun icono-popup fal-lum"></i><span class="medidaColor">' +
               posicion.luminosidad+
               ' %</span></p>'+
                 '</div>'+
               '</div>'+
               '<p class="enlace-popup"><a href="mediciones.php?idsonda=' +
               posicion.id+
               '">Mas Estadísticas</a> ' +
               '</p>'+
               '<div class=recomend>'+
               ' <p class="rec0">Cultivo recomendado: <span class="recSpan">'+recomendar(posicion.humedad, posicion.temperatura)+'</span></p>'+
               '</div>'+
               '</div>';

      var infowindow = new google.maps.InfoWindow({
          content: contentString
      });

      marker.addListener('click', function() {
          infowindow.open(map, marker);
      });
  };

  function cargarHeatmap(posicion){
      heatmap = posicion;

      heatH = new google.maps.visualization.HeatmapLayer({
           data: getPoints("humedad"),
           map: map,
           opacity: 0.75,
           gradient: [
      'rgba(0, 255, 255, 0)',
      'rgba(0, 255, 255, 1)',
      'rgba(0, 191, 255, 1)',
      'rgba(0, 127, 255, 1)',
      'rgba(0, 63, 255, 1)',
      'rgba(0, 0, 255, 1)',
      'rgba(0, 0, 223, 1)',
      'rgba(0, 0, 191, 1)',
      'rgba(0, 0, 159, 1)',
      'rgba(0, 0, 127, 1)',
      'rgba(63, 0, 91, 1)',
      'rgba(127, 0, 63, 1)',
      'rgba(191, 0, 31, 1)',
      'rgba(255, 0, 0, 1)'
    ]
      });

      heatT = new google.maps.visualization.HeatmapLayer({
           data: getPoints("temperatura"),
           map: map
      });

      heatS = new google.maps.visualization.HeatmapLayer({
           data: getPoints("salinidad"),
           map: map,
           opacity: 0.85,
           gradient: [
      'rgba(255, 255, 255, 0)',
      'rgba(225, 225, 225, 1)',
      'rgba(250, 250, 250, 1)',
      'rgba(240, 240, 240, 1)',
      'rgba(235, 235, 235, 1)',
      'rgba(200, 200, 200, 1)',
      'rgba(175, 175, 175, 1)',
      'rgba(150, 150, 150, 1)',
      'rgba(140, 140, 140, 1)',
      'rgba(125, 125, 125, 1)',
      'rgba(115, 115, 115, 1)',
      'rgba(100, 100, 100, 1)',
      'rgba(75, 75, 75, 1)',
      'rgba(60, 60, 60, 1)'
    ]
      });

      heatL = new google.maps.visualization.HeatmapLayer({
           data: getPoints("luminosidad"),
           map: map,
           opacity: 0.65,
           gradient: [
      'rgba(231,237,48,0)',
      'rgba(244,255,27,1)',
      'rgba(255,239,27,1)',
      'rgba(255,181,26,1)',
      'rgba(255,151,0,1)',
      'rgba(255,126,0,1)',
      'rgba(255,126,0,1)',
      'rgba(255,100,0,1)',
      'rgba(255,0,8,1)',
      'rgba(255,0,30,1)',
      'rgba(255,0,59,1)',
      'rgba(255,0,131,1)',
      'rgba(255,0,179,1)',
      'rgba(230,42,237,1)'
    ]
      });

      heatT.setMap(heatT.getMap() ? null : map);
      heatL.setMap(heatL.getMap() ? null : map);
      heatS.setMap(heatS.getMap() ? null : map);
      heatH.setMap(heatH.getMap() ? null : map);
      heatT.set('radius', heatT.get('radius') ? null : 40);
      heatL.set('radius', heatL.get('radius') ? null : 40);
      heatS.set('radius', heatS.get('radius') ? null : 40);
      heatH.set('radius', heatH.get('radius') ? null : 40);
  }

 function cerrarMapa(){
   document.getElementById("padre-mapa").style.display = "none";
     initMap();
 }

function dibujarparcela(vertices) {
    let bounds = new google.maps.LatLngBounds();
   let polygon = new google.maps.Polygon({
       paths: vertices,
       strokeColor: "#29c7ef",
       strokeOpacity: 0.8,
       strokeWeight: 2,
       fillColor: "#29c7ef",
       fillOpacity: 0.3,

   });
     polygon.getPath().getArray().forEach(function (v) {
         bounds.extend(v);
     })
     polygon.setMap(map);
     map.fitBounds(bounds);
 }

 function getPoints(dato){
     if(dato == null){
         return [];
     }

     var resHM = new Array();
     for(var contadorN = 0; contadorN < heatmap.length; contadorN++){
         if(dato == "humedad"){
             var med = queMedida(heatmap[contadorN]['humedad']);
             var latt = heatmap[contadorN]['lat'];
             var lonn = heatmap[contadorN]['lng'];
             var res = {location: new google.maps.LatLng(latt, lonn), weight: med};
             resHM.push(res);
         } else if(dato == "salinidad"){
             var med = queMedida(heatmap[contadorN]['salinidad']);
             var latt = heatmap[contadorN]['lat'];
             var lonn = heatmap[contadorN]['lng'];
             var res = {location: new google.maps.LatLng(latt, lonn), weight: med};
             resHM.push(res);
         } else if(dato == "temperatura"){
             var med = queCalor(heatmap[contadorN]['temperatura']);
             var latt = heatmap[contadorN]['lat'];
             var lonn = heatmap[contadorN]['lng'];
             var res = {location: new google.maps.LatLng(latt, lonn), weight: med};
             resHM.push(res);
         } else if(dato == "luminosidad"){
             var med = queMedida(heatmap[contadorN]['luminosidad']);
             var latt = heatmap[contadorN]['lat'];
             var lonn = heatmap[contadorN]['lng'];
             var res = {location: new google.maps.LatLng(latt, lonn), weight: med};
             resHM.push(res);
         }
     }
     return resHM;
 }

 function queMedida(x){
     if(x < 15){
         return 0.1;
     } else if(x < 30){
         return 0.5;
     } else if(x < 50){
         return 2;
     } else if(x < 70){
         return 3;
     } else if(x < 100) {
         return 4;
     }
 }

 function queCalor(x){
     if(x < 5){
         return 0.5;
     } else if(x < 15){
         return 2;
     } else if(x < 25){
         return 3;
     } else {
         return 4;
     }
 }

 $(".mdc1").on("click", function(){
     if($(this).hasClass("slc")){
         $(".mdc1").removeClass("slc");
         $(".icon-1").removeClass("slc");
         $(".ley-hum").css("display", "none");
     } else{
         $(".icon-1").addClass("slc");
         $(".mdc1").addClass("slc");
         $(".ley-hum").css("display", "flex");
         $(".ley-lum").css("display", "none");
         $(".ley-calor").css("display", "none");
         $(".ley-sal").css("display", "none");
     }
 });

 $(".mdc2").on("click", function(){
     if($(this).hasClass("slc")){
         $(".mdc2").removeClass("slc");
         $(".icon-2").removeClass("slc");
         $(".ley-sal").css("display", "none");
     } else{
         $(".icon-2").addClass("slc");
         $(".mdc2").addClass("slc");
         $(".ley-lum").css("display", "none");
         $(".ley-calor").css("display", "none");
         $(".ley-hum").css("display", "none");
         $(".ley-sal").css("display", "flex");
     }
 });

 $(".mdc3").on("click", function(){
     if($(this).hasClass("slc")){
         $(".mdc3").removeClass("slc");
         $(".icon-3").removeClass("slc");
         $(".ley-calor").css("display", "none");
     } else{
         $(".icon-3").addClass("slc");
         $(".mdc3").addClass("slc");
         $(".ley-lum").css("display", "none");
         $(".ley-calor").css("display", "flex");
         $(".ley-hum").css("display", "none");
         $(".ley-sal").css("display", "none");
     }
 });

 $(".mdc4").on("click", function(){
     if($(this).hasClass("slc")){
         $(".mdc4").removeClass("slc");
         $(".icon-4").removeClass("slc");
         $(".ley-lum").css("display", "none");
     } else{
         $(".icon-4").addClass("slc");
         $(".mdc4").addClass("slc");
         $(".ley-lum").css("display", "flex");
         $(".ley-calor").css("display", "none");
         $(".ley-hum").css("display", "none");
         $(".ley-sal").css("display", "none");
     }
 });

  </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIJ9XX2ZvRKCJcFRrl-lRanEtFUow4piM&libraries=visualization&callback=initMap" async defer></script>
  <!-- key=AIzaSyDIJ9XX2ZvRKCJcFRrl-lRanEtFUow4piM&  la clave de la api key -->
  <script src="js/mapa.js"></script> <!-- Script con el mapa de Google Maps -->
  <script src="js/iconos.js"></script> <!-- Este script contiene los iconos que se están usando en la página -->
  <script src="js/session.js"></script> <!-- Este script hace que si el usuario no este logueado lo mande a la pagina de login automaticamente-->
  <script src="js/funciones.js"></script>
</body>
</html>
