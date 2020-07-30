fetch('../api/v1.0/sesion').then(function(response){
    if(response.status != 200){
        location.href = '../index.html';
        //hace session de un item que permite enviar una alerta despues de iniciar session en la aplicacion sin permiso
        sessionStorage.setItem("accesoNoPermitido", "true");
    }
});