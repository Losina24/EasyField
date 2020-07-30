// FUNCION QUE MUESTRA UNA ALERTA DE ELIMINAR PARCELA //
$(".button-delete").click(function detectarid(){

    //identifica el id de la parcela//
    var ididentificado = $(this).attr('id');
    var idcorrespondiente = ididentificado.substr(1);
    document.getElementById("alerta-elemento-eliminar").innerHTML = "¿Está seguro de que quiere eliminar la parcela?";

    // Muestra el mensaje //
    $("#confirmacion-eliminar").show();
    $("#fondoAlerta").show();

    // Si da a cancelar oculta el mensaje //
    $("#cancel").click(function(){
        $("#confirmacion-eliminar").fadeOut();
        $("#fondoAlerta").fadeOut();
    });

    // Si da a eliminar oculta el mensaje, elimina la parcela y refresca la pagina//
    $("#delete").click(function(){
        $("#confirmacion-eliminar").hide();
        document.getElementById("fondoAlerta").style.display = "none";

        $.ajax({
            type: "post",
            url: "../api/v1.0/modelos/delete-parcelas.php",
            data: {id_parcela: idcorrespondiente},
            beforseSend: function(){},
            complete:function(data){},
            success: function(data){
                location.reload();
                //hago un sessionStorage para que cuando se recargue la pagina, esta tenga un item pendiente que mostrar
                sessionStorage.setItem("parcela", "true");
            },
            error: function(data){
                alert("Problemas al tratar de enviar el formulario");
            },
        });
    });
});


//al hacer click en editar parcela se muestra el formulario para editarla//
$(".button-edit").click(function detectarid(){
    //detecta el id de la parcela que se esta editando//
    var ididentificado = $(this).attr('id');
    var idcorrespondiente = ididentificado.substr(1);
    console.log(idcorrespondiente);
    //muestra el formulario
    $("#contenedor-jquery").html(formEditarParcela);
    //envia el id de la parcela de manera oculta //
    $('#formEditarParcela').append("<input type='hidden' name='id_Parcela' value='"+idcorrespondiente+"' />");
});

function enviarEditarParcela(event){
    event.preventDefault();
    fetch('../api/v1.0/editarParcela',{
        method: 'POST',
        body: new FormData(document.getElementById('formEditarParcela'))
    }).then(function(respuesta){
        if(respuesta.status == 200){
            sessionStorage.setItem("parcelaEditada", "true");
            location.reload();
        }else{
            alert("No se pudo editar la parcela");
        }
    });
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


// FUNCION QUE MUESTRA ALERTA DE ELIMINAR SONDA
$(".boton-sondas-eliminar").click(function detectarid(){
    //identifica el id de la sonda//
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
    });
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
            //crea una session sonda para saltar una alerta posteriormente//
            sessionStorage.setItem("sonda", "true");

        },
        error: function(data){
            alert("Problemas al tratar de enviar el formulario");
        }
    });
}
//FUNCION QUE CAMBIA LOS DATOS DE USUARIO
function enviarPerfil(event){
    event.preventDefault();
    fetch('../api/v1.0/perfil',{
        method: 'POST',
        body: new FormData(document.getElementById('formEditarPerfil'))
    }).then(function(respuesta){
        if(respuesta.status == 200){
            sessionStorage.setItem("actualizado", "true");
            location.reload();

        }else if(respuesta.status == 401){
            $("#contrasenyas-no-coinciden").hide();
            $("#antigua-incorrecta").slideDown();

        }else{
            $("#antigua-incorrecta").hide();
            $("#contrasenyas-no-coinciden").slideDown();

        }
    });
}
//Funcion que crea la sonda
/*function enviarSonda(event){
    event.preventDefault();
    fetch('../api/v1.0/sondas',{
        method: 'POST',
        body: new FormData(document.getElementById('form-sondas'))
    }).then(function(respuesta){
        if(respuesta.status == 200){
            sessionStorage.setItem("sondaCreada", "true");
            location.reload();

        }else{
            alert("no se pudo añadir la sonda");
        }
    });
}*/


// Función que elimina un usuario //
$(".button-delete-user").click(function detectarid(){
    //identifica el id de la sonda//
    var ididentificado = $(this).attr('id');
    var returned = ididentificado.substr(1);
    document.getElementById("alerta-elemento-eliminar").innerHTML = "¿Está seguro de que quiere eliminar este usuario?";
    $("#confirmacion-eliminar").show();
    $("#fondoAlerta").show();
    //Si da a cancelar oculta el mensaje //
    $("#cancel").click(function(){
        $("#confirmacion-eliminar").fadeOut();
        $("#fondoAlerta").fadeOut();
    });
    //si da a eliminar oculta el mensaje y llama a eliminar usuario //
    $("#delete").click(function(){
        $("confirmacion-eliminar").hide();
        document.getElementById("fondoAlerta").style.display = "none";
        //llama a eliminar sonda enviando el id de la sonda//
        eliminarUser(returned);
    });
});

function eliminarUser(idUser){
  $.ajax({
      type: "post",
      url: "../api/v1.0/modelos/delete-user.php",
      data: { iduser: idUser },
      beforeSend: function(){},
      complete:function(data){},
      success: function(data){
          location.reload();
          //crea una session usuario para saltar una alerta posteriormente//
          sessionStorage.setItem("usuarioborrado", "true");

      },
      error: function(data){
          alert("Problemas al tratar de enviar el formulario");
      }
  });
}
