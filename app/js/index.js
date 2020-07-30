$(document).ready(function(){

  // Variable que contiene el código html del formulario para crear parcelas
  var imprimir = '<section class="crear-parcela-padre" id="alertilla">'+
  '      <article class="crear-parcela-contenedor">'+
  '        <h1 class="crear-parcela-titulo">Crear parcela</h1>'+
  '        <form class="crear-parcela-form" method="post" action="../api/v1.0/modelos/post-parcelas.php" id="formularioCrearParcela">'+
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

  // Variable con el codigo html que contiene el formulario para crear sondas
  var formSonda =
  '<section class="crear-parcela-padre" id="alertilla">'+
  '  <article class="crear-parcela-contenedor">'+
  '    <h1 class="crear-parcela-titulo">Crear sonda</h1>'+
  '    <form class="crear-parcela-form" method="post" action="../api/v1.0/modelos/post-sondas.php" id="form-sondas">'+
  '      <label for="nombre-parcela"></label>'+
  '      <select class="seleccionar-cultivo caja-label select-sondas" name="Cultivo" value="Nombre de la parcela" required>'+
  '        <option class="opcion-vertice">Test</option>'+
  '      </select>'+
  '      <label for="longitud"></label>'+
  '      <input class="caja-label" type="number" step="0.0001" placeholder="Posición de la sonda (Longitud)" name="longitud" required>'+
  '      <label for="latitud"></label>'+
  '      <input class="caja-label" type="number" step="0.0001" placeholder="Posición de la sonda (Latitud)" name="latitud" required>'+
  '      <input class="boton-enviar" type="submit">'+
  '    </form>'+
  '   </article>'+
  '   <button href="#" onclick="cerrarJquery()" class="cerrar">+</button>'+
  '</section>';

  // Esta función imprime en la página el formulario para crear sondas
  function crearSonda(evento){
    $("#contenedor-jquery").html(formSonda);
  }

  // Imprime en la página el formulario para crear parcelas
  function anyadirParcela(evento){
    $("#contenedor-jquery").html(imprimir);
  }

  // Vacia el contenedor que se utiliza para imprimir los formularios de crear sondas y parcelas.
  function cerrarJquery(){
    $("#contenedor-jquery").html("");
  }

  // Llama a una función que selecciona una parcela
  var attrPrint = "seleccionar($(this).attr('id'))";

  // Llama a una función que selecciona todas las parcelas
  var selectAll = "seleccionarTodo()";

  // Código html de un recuadro con opciones
  var printOpciones = '<div class="etiqueta-opciones" id="tag">'+
  '    <ul class="lista-opciones">'+
  '      <li class="elemento-opciones">'+
  '           <a id="p1" href="#" onclick="'+ selectAll +'">Seleccionar todo</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#" onclick="mostrarmapa()">Ver en el Mapa</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#">Editar</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#" id="eliminar" onclick="eliminarParc()">Eliminar</a>'+
  '      </li>'+
  '      <li class="elemento-opciones">'+
  '           <a href="#" id="postSonda" onclick="crearSonda(event)">Añadir sonda</a>'+
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

  // Obtiene el id del elemento al cuál se hace click
  $(".otras-opciones").click(function detectarid(){
      var ididentificado = $(this).attr('id');
      var idcorrespondiente = ididentificado.substr(1);
      $("#inputt").val(idcorrespondiente);
  })

  // Envía el formulario a delete-parcelas.php cuando se hace click en 'Eliminar'
  $("#eliminar").click(function(){
    $("#form-espontaneo").submit();
  })

  // Cierra el recuadro con opciones cuando se hace click en el otra vez
  function cerrarPuntos(){
    if ($('.cuerpo-pagina').hasClass("cuadro-activo")){
      $(".etiqueta-opciones").remove();
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
  '<input class=\'caja-label\' type=\'number\' step=\'0.0001\' placeholder=\'Posición del vértice (Longitud)\' name=\'longitud[]\' required>'+
  '<label for=\'vertices\'></label>'+
  '<input class=\'caja-label\' type=\'number\' step=\'0.0001\' placeholder=\'Posición del vértice (Latitud)\' name=\'latitud[]\' required>';

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
    if($("#p"+p).find(".parcela").find(".botones-parcela").find(".select-parcela").hasClass("selected")){
      $("#p"+p).find(".parcela").find(".botones-parcela").find(".select-parcela").removeClass("selected");
    } else {
      $("#p"+p).find(".parcela").find(".botones-parcela").find(".select-parcela").addClass("selected");
    }
  }

  $(".select-parcela").on("click", function(){
    var idPar = $(this).attr('id');
    alert(idPar);
    seleccionar(idPar);
  });

  // Selecciona todas las parcelas
  function seleccionarTodo(){
    if($(".parcela").hasClass("selected")){
      $(".parcela").removeClass("selected");
    } else {
      $(".parcela").addClass("selected");
    }
  }

  // Crea una nueva parcela
  function anyadirparcela(evento){
      evento.preventDefault();
      fetch('api/v1.0/parcelas',{
          method: 'post',
          body: new FormData(document.getElementById('formularioCrearParcela'))
      }).then(function(respuesta){
          if(respuesta.status == 200){

          }else{

          }
      });
  }

  // Envia el formulario que elimina una parcela
  function eliminarParc(){
    $("#form-espontaneo").submit()
  }

  // Despliega la lista de sondas de una parcela cuando se hace click dentro de esta
  $(".texto-parcela").on("click", function(){
    var miID = $(this).attr('id');
    $("#p" + miID).stop().slideToggle(400);
  });


  // FUNCIONES DEL MAPA //

  var map;
  function initMap() {
      map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 38.9965055, lng: -0.1674364},
          zoom: 15,
          mapTypeId: 'hybrid',
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

  function dibujarparcela(parcela) {
      let polygon = new google.maps.Polygon({
          paths: parcela.vertices,
          strokeColor: parcela.color,
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: parcela.color,
          fillOpacity: 0.35,
          map: map
      });
  }

  function mostrarmapa(){
    document.getElementById("padre-mapa").style.display = "flex";
  }

  function cerrarMapa(){
    document.getElementById("padre-mapa").style.display = "none";
  }

  function dibujarparcela(parcela) {
    let polygon = new google.maps.Polygon({
        paths: parcela.vertices,
        strokeColor: parcela.color,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: parcela.color,
        fillOpacity: 0.35,
        map: map
    });
  }

});
