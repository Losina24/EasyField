$(buscarDatos());

function buscarDatos(consulta){
  $.ajax({
    url: '../../src/api/v1.0/modelos/filtrar.php',
    type: 'POST',
    data: {consulta: consulta}
  })
  .done(function(respuesta){
    $("#datos").html(respuesta);
  })
  .fail(function(){
    console.log("Error");
  });
}

$("#busqueda").on('keyup', function(){
  var valor = $("#busqueda").val();

  if(valor == ""){
    $.post("../../src/api/includes/print_admin.php", function(varPost){
      $("#datos").html(varPost);
    });
  } else {
    buscarDatos(valor);
  }
});
