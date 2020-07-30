<?php
// Archivo .php con las sentencias a la bbdd, de las que se extraen los datos necesarios para imprimir las parcelas y las sondas.
include ("../api/includes/conexion.php");
include ("../api/v1.0/modelos/get-usuarios.php");

$nombret = $_SESSION['usuario'];

$sqlt = "SELECT idRol, grupo FROM usuarios WHERE nombre_user = '$nombret'";
$queryt = mysqli_query($conexion, $sqlt);
$rolt = mysqli_fetch_assoc($queryt);
$rolNombre = "";
$counter = 0;

$user = $rolt['idRol'];
$group = $rolt['grupo'];
if($user == 3){
  header("Location: usuario.php");
}

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


// Obtener parcelas existentes
$parcelasFinal = [];
$parcelasUser = [];

$queryUsuariosGrupo = mysqli_query($conexion, "SELECT id_user FROM usuarios WHERE grupo = '$group'");
$usuariosDelGrup = [];
while($rowUsuariosGrupo = mysqli_fetch_assoc($queryUsuariosGrupo)){
    $usuariosDelGrup[] = $rowUsuariosGrupo;
}

function seleccionarParcelas($usuariosDelGrupo, $con){
    $parcelasFinall = [];
    $salida = array();
    for($p = 0; $p < count($usuariosDelGrupo); $p++){
        $usuarioFinal = $usuariosDelGrupo[$p]['id_user'];

        // Se obtiene el id de las parcelas que pertenecen al usuario.
        $sqlidparcelas = "SELECT idParcelas FROM asignacionusparc WHERE idUsuarios = '$usuarioFinal'";
        $queryidparcelas = mysqli_query($con, $sqlidparcelas);
        $salidaidparcelas = array();

        while ($idparcelas= mysqli_fetch_assoc($queryidparcelas)) {
          // Este array contiene los ids de las parcelas que pertenecen al usuario.
            array_push($salidaidparcelas, $idparcelas);
        };

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
            for ($x=0; $x < count($salida); $x++){
                    if(empty($parcelasFinall)){
                        array_push($parcelasFinall, $salida[$x]);
                    }else{
                        $auxV = 0;
                        for ($v=0; $v < count($parcelasFinall); $v++) {
                            //print_r($parcelasFinall[$v]);
                            if ($salida[$x] == $parcelasFinall[$v]) {
                                $auxV++;
                            }
                        }
                        if($auxV == 0) {
                            array_push($parcelasFinall, $salida[$x]);
                        }
                    }
            }
    }
    return $parcelasFinall;
}

$parcelasFinal = seleccionarParcelas($usuariosDelGrup, $conexion);

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
          <button id="botonMenu" type="button" name="cerrarMenu" onclick="clickBell()"><i class="fal fa-times"></i></button>
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
    <div class="fondo-menu" onclick="clickBell()"></div>

  <div class="cuerpo-pagina" id="cuerpo">

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
        <form action="" method="POST" value="" role="search" autocomplete="off" class="form-busqueda" onKeypress="if(event.keyCode == 13){event.preventDefault()}">
          <input type="search" name="barra-de-busqueda" value="" class="barra-de-busqueda" placeholder="Filtrar usuarios..." id="busqueda">
        </form>

        <button type="button" name="config" id="bell" onclick="clickBell()"><i class="fas fa-bell"></i><?php if(count($resNot)>0){?> <i class="fas fa-circle punto-alerta"></i> <?php } ?></button>
        <button type="button" name="config" id="cog" onclick="clickPuntos(event)"><i class="fas fa-cog"></i></button>
      </div>

      <article class="apartado-parcelas"> <!-- Apartado que contiene la lista de las parcelas -->
        <ul class="lista-de-parcelas" id="datos">
        </ul>
      </article>
    </section>

    <!-- <button type="button" name="anyadir-parcela" class="anyadir-parcela" onclick="anyadirParcela(event)">Añadir Parcela</button> -->
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
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>--> <!-- jQuery UI -->

  <script>
  // Función que abre y cierra el menu lateral de notificaciones //
  function clickBell(){
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
          data: {usuario: <?php print_r($rowf)?>},
          success: function(data){
              $(".cuadro-notificaciones").fadeOut(500);
              $(".punto-alerta").css("display", "none");
              setTimeout(function(){
                  $(".lista-notificaciones").empty();
              },550);
          },
          error: function(data){
              alert("Problemas al tratar de enviar el formulario");
          }
      });
  }

  function enviarSonda(event){
      event.preventDefault();

      var formular = $("#form-sondas");

      $.ajax({
        url: '../api/v1.0/modelos/post-sondas.php',
        type: 'POST',
        data: formular.serialize()
      })
      .done(function(datos){
          if(datos == 200){
              sessionStorage.setItem("sondaCreada", "true");
              location.reload();

          }else{
              alert("no se pudo añadir la sonda");
          }
      })
      .fail(function(){
        // ALERTA //
      })
  }


  // Despliega la lista de parcelas de un usuario cuando se hace click en este
  $(".texto-usuario").on("click", function(){
    var miID = $(this).attr('id');
    $("#p" + miID).stop().slideToggle(400);
  });

  function seleccionarUsuario(identificador, idd){
    $("#p" + idd).stop().slideToggle(400);
    $(".li-usuario").not("#"+identificador).toggle("slide");
  }

  // Variable que contiene el código html del formulario para crear parcelas
  var imprimir = '<section class="crear-parcela-padre" id="alertilla">'+
  '      <article class="crear-parcela-contenedor">'+
  '        <h1 class="crear-parcela-titulo">Crear parcela</h1>'+
  '        <form class="crear-parcela-form" method="post" onsubmit="enviarparcela(event)"  id="formularioCrearParcela">'+
  '          <!-- NOMBRE DEL USUARIO -->'+
  '	  <label for="nombreUser"></label>'+
  '	  <input class="caja-label nombre-input" type="text" placeholder="Nombre del usuario" id="inputNombreUser" name="NombreUser" required>'+
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


  // Formulario para añadir una parcela existente a un usuario.
  var imprimirBuscar = '<section class="crear-parcela-padre" id="alertilla">'+
  '      <article class="crear-parcela-contenedor">'+
  '        <h1 class="crear-parcela-titulo">Añadir parcela</h1>'+
  '        <form class="crear-parcela-form" method="post" onsubmit="addParcela(event)"  id="formularioAddParcela">'+
  '	          <label for="nombreUser"></label>'+
  '	          <input class="caja-label nombre-input" type="text" placeholder="Nombre del usuario" id="inputIdUser" name="NombreUser" required>'+
  '          <label for="parcela"></label>'+
  '	             <select class="seleccionar-cultivo caja-label select-sondas" name="parcela" value="parcela" id="selectParcela" required>'+
  '                    <option value="" selected disabled hidden>Seleccionar parcela</option>'+
  <?php for($i = 0; $i < count($parcelasFinal); $i++){ ?>
  '                    <option value="<?php print_r($parcelasFinal[$i]["id_parcelas"]) ?>"><?php print_r($parcelasFinal[$i]["Nombre"]) ?></option>'+
  <?php } ?>
  '              </select>'+
  '	             <!-- SUBMIT -->'+
  '	             <input class="boton-enviar" type="submit">'+
  '        </form>'+
  '      </article>'+
  '      <button href="#" onclick="cerrarJquery()" class="cerrarSonda">+</button>'+
  '    </section>';

  // Formulario para quitar una parcela existente a un usuario.
  var imprimirQuitar = '<section class="crear-parcela-padre" id="alertilla">'+
  '      <article class="crear-parcela-contenedor">'+
  '        <h1 class="crear-parcela-titulo">Quitar parcela</h1>'+
  '        <form class="crear-parcela-form" method="post" onsubmit="quitarParcelas(event)"  id="formularioQuitarParcela">'+
  '	          <label for="nombreUser"></label>'+
  '	          <input class="caja-label nombre-input" type="text" placeholder="Nombre del usuario" id="inputQuitarUser" name="NombreUser" required>'+
  '          <label for="parcela"></label>'+
  '	             <select class="seleccionar-cultivo caja-label select-sondas" name="parcela" value="parcela" id="selectOptionP" required>'+
  '                    <option value="" selected disabled hidden>Seleccionar parcela</option>'+
  '              </select>'+
  '	             <!-- SUBMIT -->'+
  '	             <input class="boton-enviar btn-send" type="submit">'+
  '        </form>'+
  '      </article>'+
  '      <button href="#" onclick="cerrarJquery()" class="cerrarSonda">+</button>'+
  '    </section>';


  // Variable con el codigo html que contiene el formulario para crear usuarios
  var formUsuario =
      '<section class="crear-parcela-padre" id="alertilla">'+
      '  <article class="crear-parcela-contenedor">'+
      '    <h1 class="crear-parcela-titulo">Crear Usuario</h1>'+
      '    <form class="crear-parcela-form" method="post" onsubmit="enviar(event)" id="formUsuario">'+
      '      <label for="NombreUsuario"></label>'+
      '      <input class="caja-label" type="text" placeholder="Nombre Usuario" name="nombre" required>'+
      '      <label for="ContrasenyaUsuario"></label>'+
      '      <input class="caja-label" type="text" placeholder="Contraseña Usuario" name="contrasenya" required>'+
      '      <label for="idRolUsuario"></label>'+
      '	     <select class="seleccionar-cultivo caja-label select-sondas" name="nombreRol"  required>'+
      '            <option value="" selected disabled hidden>Rol</option>'+
      '            <option value="3" class="opcion-vertice">Usuario</option>'+
      '            <option value="1" class="opcion-vertice">Administrador</option>'+
      '      </select>'+
      '      <label for="TelefonoUsuario"></label>'+
      '      <input class="caja-label" type="text" placeholder="Telefono Usuario" name="telefono" >'+
      '      <input class="boton-enviar" type="submit" value="Crear">'+
      '    </form>'+
      '   </article>'+
      '   <button href="#" onclick="cerrarJquery()" class="cerrar-formUsuario">+</button>'+
      '</section>';

  // Esta función imprime en la página el formulario para crear sondas
  function crearSonda(evento, varuser){
      cerrarPuntos();
    $("#contenedor-jquery").html(
      '<section class="crear-parcela-padre" id="alertilla">'+
      '  <article class="crear-parcela-contenedor">'+
      '    <h1 class="crear-parcela-titulo">Crear sonda</h1>'+
      '    <form class="crear-parcela-form" method="post" onsubmit="enviarSonda(event)" id="form-sondas">'+
      '      <label for="nombre-parcela"></label>'+
      '      <select class="seleccionar-cultivo caja-label select-sondas inputDis" name="nombre" required id="varuserSondas">'+
      '        <option selected hidden value="'+varuser+'">'+varuser+'</option>'+
      '      </select>'+
      '      <label for="longitud"></label>'+
      '      <input class="caja-label" type="number" step="0.00000000001" placeholder="Posición de la sonda (Longitud)" name="longitud" required>'+
      '      <label for="latitud"></label>'+
      '      <input class="caja-label" type="number" step="0.00000000001" placeholder="Posición de la sonda (Latitud)" name="latitud" required>'+
      '      <input class="boton-enviar" type="submit" value="Crear">'+
      '    </form>'+
      '   </article>'+
      '   <button href="#" onclick="cerrarJquery()" class="cerrar" id="cerrarSonda">+</button>'+
      '</section>');

  }

  function crearUsuario(){
      cerrarPuntos();
    $("#contenedor-jquery").html(formUsuario);
  }

  // Imprime en la página el formulario para crear parcelas
  function anyadirParcelaUser(id){
    $("#contenedor-jquery").html(imprimir);
    $("#inputNombreUser").val(id);

}

  // Imprime en la página el formulario para crear parcelas
  function buscarParcela(id){
    $("#contenedor-jquery").html(imprimirBuscar);
    $("#inputIdUser").val(id);

  }

  // Imprime en la página el formulario para declinar parcelas
  function quitarParcela(id){
    $("#contenedor-jquery").html(imprimirQuitar);
    $("#inputQuitarUser").val(id);
    $.ajax({
      url: '../../src/api/v1.0/modelos/get-parcelas-usuario.php',
      type: 'POST',
      data: {id: id}
    })
    .done(function(data){
        var datos = JSON.parse(data);
        var dato = Object.values(datos);

        for(var i = 0; i < Object.keys(dato).length; i++){
            $("#selectOptionP").append(new Option(datos[i].Nombre, datos[i].id_parcelas));
        }
    })
  }

  //imprime en la pagina el formulario editar perfil
  function editarPerfil(){
      cerrarPuntos();
      $("#contenedor-jquery").html(formEditarPerfil);
  }

  // Envia la petición para crear un usuario a post-usuarios.php //
  $("#boton-crear-user").on("click", function(){
    var formulario = $("#formUsuario");
    $.ajax({
      url: '../../src/api/v1.0/modelos/post-usuarios.php',
      type: 'POST',
      data: formulario.serialize()
    })
    .done(function(){
      // ALERTA //
    })
    .fail(function(){
      // ALERTA //
    });
  });

  // Vacia el contenedor que se utiliza para imprimir los formularios de crear sondas y parcelas.
  function cerrarJquery(){
    $("#contenedor-jquery").html("");
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
  '           <a href="#" id="eliminar" onclick="eliminarSeleccion()">Eliminar Parcelas</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#" id="postUsuario" onclick="crearUsuario()">Crear Usuario</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#" onclick="editarPerfil()">Editar Perfil</a>'+
  '      </li>'
  '    </ul>'+
  '  </div>';

  var bellita = '<div class="etiqueta-opciones-2" id="tag tag2">'+
  '    <ul class="lista-opciones">'+
  '    <?php for($i=0; $i < count($resNot); $i++){ ?>'+
  '      <li class="elemento-opciones medic">'+
  '           <p><?php print_r($resNot[$i]["mensaje"]); ?></p>'+
  '           <p><?php print_r($resNot[$i]["fecha"]); ?></p>'+
  '      </li>'+
  '    <?php } ?>'+
  '    </ul>'+
  '</div>';

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
      '   <button href="#" onclick="cerrarJquery()" class="cerrar-formUsuario">+</button>'+
      '</section>';

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


// FUNCION QUE MUESTRA ALERTA DE ELIMINAR SONDA
$(".boton-sondas-eliminar").click(function detectarid(){
      //identifica el id de la parcela//
      var ididentificado = $(this).attr('id');
    document.getElementById("alerta-elemento-eliminar").innerHTML = "¿Está seguro de que quiere eliminar la sonda "+ididentificado+"?";
      $("#confirmacion-eliminar").show();
        $("#fondoAlerta").show();
        //Si da a cancelar oculta el mensaje //
      $("#cancel").click(function(){
          $("#confirmacion-eliminar").fadeOut();
          $("#fondoAlerta").fadeOut();
      });
      //si da a eliminar oculta el mensaje y llama a eliminar sonda //
      $("#delete").click(function(){
          $("confirmacion-eliminar").hide();
          document.getElementById("fondoAlerta").style.display = "none";
          //llama a eliminar sonda enviando el id de la sonda//
          eliminarSonda(ididentificado);
      })

  });

  function eliminarSonda(identificadorSonda){
    $.ajax({
            type: "post",
            url: "../api/v1.0/modelos/delete-sondas.php",
            data: { sonda: identificadorSonda },
            beforeSend: function(){},
            complete:function(data){},
            success: function(data){
                location.reload();
                // Crea una session sonda para saltar una alerta posteriormente//
                sessionStorage.setItem("sonda", "true");

            },
            error: function(data){
                alert("Problemas al tratar de enviar el formulario");
            }
    });
  }

  // Función que despliega las sondas de las parcelas que pertenecen a los usuarios filtrados mediante la barra de búsqueda //
  function abrirSondas(idd){
    $(".p"+idd).stop().slideToggle(400);
  }

//FUNCION QUE ELIMINA LAS PARCELAS SELECCIONADAS //
function eliminarSeleccion(){
    var varelim = new Array();
    //Para cada parcela seleccionada se mete en un array de ids;
    $(".seleccionado").each(function(){
      var attrElim = $(this).attr('id');
      varelim.push(attrElim);
    });
    if(varelim !=0){
    //cambia el html a otro titulo//
     document.getElementById("alerta-elemento-eliminar").innerHTML = "¿Está seguro de que quiere eliminar las parcelas seleccionadas?";
     //cierra el menu de puntos//
     cerrarPuntos();
     //se muestra el mensaje de alerta
      $("#confirmacion-eliminar").show();
        $("#fondoAlerta").show();
        //Si da a cancelar oculta el mensaje //
      $("#cancel").click(function(){
          $("#confirmacion-eliminar").fadeOut();
          $("#fondoAlerta").fadeOut();
      });
      //elimina las parcelas seleccionadas
      $("#delete").click(function(){
          $("confirmacion-eliminar").hide();
          document.getElementById("fondoAlerta").style.display = "none";
          //Elimina las parcelas seleccionadas //
          $.ajax({
            type: "post",
            url: "../api/v1.0/modelos/delete-parcelas.php",
            data: { id_parcela: varelim },
            beforeSend: function(){},
            complete:function(data){},
            success: function(data){
              location.reload();
              //hago un session storage para que se muestre una alerta de confirmacion //
            sessionStorage.setItem("parcelas", "true");
            },
            error: function(data){
                alert("Problemas al tratar de enviar el formulario");
            }
        });
      })
     } else{
        document.getElementById("nombre-elemento-error").innerHTML = "Error : No hay parcelas seleccionadas";
        //muestra la alerta
        alertaError();
        //cierra el menu de eleccion
        cerrarPuntos();
     }
  }

//cuando la pagina es refrescada ejecuta la funcion correspondiente con las alertas //
  window.onload = function() {
      //si encuentra cualquiera de las variables, la elimina y ejecuta la funcion.
      var sonda = sessionStorage.getItem("sonda");
      var parcela = sessionStorage.getItem("parcela");
      var parcelas = sessionStorage.getItem("parcelas");
      var parcelaCreada = sessionStorage.getItem("parcelaCrear");
      var SondaCreada = sessionStorage.getItem("sondaCreada");
      var actualizado = sessionStorage.getItem("actualizado");
      var parcelaEditada = sessionStorage.getItem("parcelaEditada");
      var UsuarioCreado = sessionStorage.getItem("usuarioCreado");
      var UsuarioBorrado = sessionStorage.getItem("usuarioborrado");

      if(parcela){
          sessionStorage.removeItem("parcela");
          document.getElementById("nombre-elemento-eliminado").innerHTML = "La parcela se ha eliminado correctamente";
          alertaCorrecto();
      }
      if (sonda) {
          sessionStorage.removeItem("sonda");
          document.getElementById("nombre-elemento-eliminado").innerHTML = "La sonda se ha eliminado correctamente";
          alertaCorrecto();
      }
      if(parcelas){
          sessionStorage.removeItem("parcelas");
          document.getElementById("nombre-elemento-eliminado").innerHTML = "Las parcelas se han eliminado correctamente";
          alertaCorrecto();
      }
      if(parcelaCreada){
          sessionStorage.removeItem("parcelaCrear");
          document.getElementById("nombre-elemento-eliminado").innerHTML = "Las parcela se ha creado correctamente";
          alertaCorrecto();
      }
      if(SondaCreada){
          sessionStorage.removeItem("sondaCreada");
          document.getElementById("nombre-elemento-eliminado").innerHTML = "Las sonda se ha creado correctamente";
          alertaCorrecto();
      }
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
      if(UsuarioCreado){
          sessionStorage.removeItem("usuarioCreado")

          //cambia el titulo de la alerta
          document.getElementById("nombre-elemento-eliminado").innerHTML = "El usuario se ha creado correctamente";
          //muestra la alerta
          alertaCorrecto();
      }
      if(UsuarioBorrado){
          sessionStorage.removeItem("usuarioborrado")

          //cambia el titulo de la alerta
          document.getElementById("nombre-elemento-eliminado").innerHTML = "El usuario se ha eliminado correctamente";
          //muestra la alerta
          alertaCorrecto();
      }

  };

//funcion que ejecuta que se vea la alerta de "se ha eliminado"
  function alertaCorrecto(){
    document.getElementById("alerta-correcto").style.display = "flex";
    //despues de 3 segundos la alerta desaparece //
    setTimeout(function(){
        $("#alerta-correcto").slideUp();
        document.getElementById("fondoAlerta").style.display = "none";
    },3000)
  }

  //funcion que ejecuta que se vea la alerta de que ha habido algun error //
  function alertaError(){
      document.getElementById("alerta-error").style.display = "flex";
      //despues de 3 segundos la alerta desaparece //
      setTimeout(function(){
          $("#alerta-error").slideUp();
          document.getElementById("fondoAlerta").style.display = "none";
      },3300)
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

  // Crea una nueva parcela
  function enviarparcela(event){
      event.preventDefault();
      fetch('../api/v1.0/parcelas',{
          method: 'post',
          body: new FormData(document.getElementById('formularioCrearParcela'))
      }).then(function(respuesta){
          if(respuesta.status == 200){
              sessionStorage.setItem("parcelaCrear", "true");
              //recarga la pagina para que aparezcan las nuevas parcelas
              location.reload();
          }else{
              alert("La parcela no se pudo crear")
          }
      });
  }

  // Añadir una parcela
  function addParcela(event){
      event.preventDefault();
      var formularioAddParcela = $("#formularioAddParcela");

      $.ajax({
        type: "post",
        url: "../api/v1.0/modelos/add-parcelas.php",
        data:  formularioAddParcela.serialize(),
        success: function(data){
          location.reload();
          //hago un session storage para que se muestre una alerta de confirmacion //
          sessionStorage.setItem("parcelaCrear", "true");
        },
        error: function(data){
            alert("Problemas al añadir la parcela");
        }
    });
  }

  // Declinar una parcela
  function quitarParcelas(event){
      event.preventDefault();
      var formularioQuitarParcela = $("#formularioQuitarParcela");

      $.ajax({
        type: "post",
        url: "../api/v1.0/modelos/quitar-parcela.php",
        data:  formularioQuitarParcela.serialize(),
        success: function(data){
          location.reload();
          //hago un session storage para que se muestre una alerta de confirmacion //
          sessionStorage.setItem("parcelas", "true");
        },
        error: function(data){
            alert("Problemas al quitar la parcela");
        }
    });
  }

  //FUNCION QUE ENVIA EL FORMULARIO DE CREAR USUARIOS EN MODO ADMINISTRADOR//
  function enviar(evento){
      evento.preventDefault();
      fetch('../api/v1.0/usuarios', {
          method: 'post',
          body: new FormData(document.getElementById('formUsuario'))
      }).then(function(respuesta) {
          if (respuesta.status == 200) {
              location.reload();
              sessionStorage.setItem("usuarioCreado", "true");

              setTimeout(function(){
                  alertaCorrecto();
              },350)

          } else if(respuesta.status == 406) {
              cerrarJquery();
              //Como el usuario ya esta creado, muestra un alerta de error //
              document.getElementById("nombre-elemento-error").innerHTML = "Error : El usuario ya existe";
              //muestra la alerta
              setTimeout(function(){
                  alertaError();
              },350)
          } else{
              alert("Error: no se pudo crear el Usuario")
          }
      });
  }

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
  </script>

  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIJ9XX2ZvRKCJcFRrl-lRanEtFUow4piM&libraries=visualization&callback=initMap" async defer></script>
  <!-- key=AIzaSyDIJ9XX2ZvRKCJcFRrl-lRanEtFUow4piM&  la clave de la api key -->
  <script src="js/mapa.js"></script> <!-- Script con el mapa de Google Maps -->
  <script src="js/iconos.js"></script> <!-- Este script contiene los iconos que se están usando en la página -->
  <script src="js/session.js"></script> <!-- Este script hace que si el usuario no este logueado lo mande a la pagina de login automaticamente-->
  <script src="js/filtrar.js"></script> <!-- Este script permite filtrar usuarios mediante la barra de búsqueda -->

  <script> <!-- En este script están las funciones del mapa de calor -->

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
        if(x < 15.00){
            return 0.5;
        } else if(x < 30.00){
            return 1;
        } else if(x < 50.00){
            return 2;
        } else if(x < 70.00){
            return 3;
        } else {
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

</body>
</html>
