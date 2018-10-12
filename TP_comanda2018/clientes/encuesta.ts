
let auxServer = "http://127.0.0.1:8080/TP_comanda/server/";
$(function () {



    let btnTiempo = $("#btnTiempo")//document.getElementByIdmismos selectors que css . o #
    btnTiempo.click(function () {
        let pedidotxt: any = $("#pedidotxt").val();
        let mesatxt: any = $("#mesatxt").val();

        let form_data = new FormData();

        form_data.append('idPedido', pedidotxt);
        form_data.append('nroMesa', mesatxt);

            $.ajax({
                type: "POST",
                url: auxServer + "pedido/tiempoEstimado/",
                data: form_data,
                contentType: false,
                processData: false,
                cache: false
            })
                .then(function (retorno) {
                    console.info("bien:", retorno);
                    let InfoLogin = $("#InfoLogin").html("<h3 id='InfoLogin'><kbd class= label-success> Tiempo restate</kbd></h3>");
                    if (retorno.tiempoBar != "Sin pedido") {
                        InfoLogin.append("<h4>Bartender: "+ retorno.tiempoBar+" </h4>"); 
                    }
                    if (retorno.tiempoCoc != "Sin pedido") {
                        InfoLogin.append("<h4>Cocina: "+ retorno.tiempoCoc+" </h4>");
                    }
                    if (retorno.tiempoCer != "Sin pedido") {
                        InfoLogin.append("<h4>Cerveceria: "+ retorno.tiempoCer+"</h4>");
                    }
                    let modalInfoLogin = $('#modalInfoLogin').modal('show');
                    limpiarCamposEncuesta();
        
                }, function (error) {
                    console.info("error encuesta", error);
                    let errorLogin = $("#errorLogin").html("<h4 id='errorLogin'><kbd class= label-danger>Error con pedido</kbd></h2>");
                    let errorLoginModal = $('#modalerrorLogin').modal('show');
                    // alert((error.responseJSON).error);
                });
    })


});

function Encuesta() {
    console.log("Encuesta");
    localStorage.setItem("idPedido",String($("#mesa").val()));
    localStorage.setItem("nroMesaTempo",String($("#pedido").val()));

    var datosLogin = { idPedido: $("#pedido").val(), nroMesa: $('#mesa').val() };
    console.log("datosLogin: ",datosLogin);
    if (datosLogin.idPedido == "") {

        let errorEmail = $("#errorEmail").html("<h4 id='errorEmail'><kbd class= label-warning>Debe ingresar pedido</kbd></h2>").fadeToggle('slow');
        return;
    } else {

        let errorEmail = $("#errorEmail").html("");
    }
    if (datosLogin.nroMesa == "") {
        //$("#clave").addClass("error");
        let errorclave = $("#errorClave").html("<h4 id='errorClave'><kbd class= label-warning>Debe ingresar mesa</kbd></h2>").fadeToggle('slow');
        return;
    } else {
        //$("#clave").addClass("sinError");
        let errorclave = $("#errorClave").html("").fadeToggle('slow');
    }

    $.ajax({
        type: "post",
        url: auxServer + "Encuesta/",
        data: datosLogin,
        dataType: 'json'

    })
        .then(function (retorno) {
            console.info("bien:", retorno);
            if (retorno == false) {
                let errorLogin = $("#errorLogin").html("<h4 id='errorLogin'><kbd class= label-danger>Encuesta no disponible o ya completada</kbd></h2>");
                let errorLoginModal = $('#modalerrorLogin').modal('show');
            }
            if (retorno.estado_encuesta) {
                console.info("estado_encuesta:", retorno.estado_encuesta);
                //window.location.replace("./encuesta.html")
                enviarEncuesta(retorno)
            }
        }, function (error) {
            console.info("error login", error.responseJSON);
            let errorLogin = $("#errorLogin").html("<h4 id='errorLogin'><kbd class= label-danger>" + error.responseJSON + "</kbd></h2>");
            let errorLoginModal = $('#modalerrorLogin').modal('show');
            // alert((error.responseJSON).error);
        });

}


function enviarEncuesta(retorno:any) {
    console.log(retorno.idPedido);
    let puntos_mesa: any = $("#puntos_mesa").val();
    let puntos_restaurante: any = $("#puntos_restaurante").val();
    let puntos_mozo: any = $("#puntos_mozo").val();
    let puntos_cocinero: any = $("#puntos_cocinero").val();
    let comentario: any = $("#comentario").val();
    
    
    let form_data = new FormData();
    form_data.append('idPedido', retorno.idPedido);
    form_data.append('nroMesa', retorno.nroMesa);
    form_data.append('puntos_mesa', puntos_mesa);
    form_data.append('puntos_restaurante', puntos_restaurante);
    form_data.append('puntos_mozo', puntos_mozo);
    form_data.append('puntos_cocinero', puntos_cocinero);
    form_data.append('comentario', comentario);
    console.log("form_data: ", form_data.get('idPedido'));
    console.log("form_data: ", form_data.get('nroMesa'));
    console.log("form_data: ", form_data.get('puntos_mesa'));
    
   
    $.ajax({
        type: "POST",
        url: auxServer + "finalizarEncuesta/",
        data: form_data,
        contentType: false,
        processData: false,
        cache: false
    })
        .then(function (retorno) {
            console.info("bien:", retorno);
            let errorLogin = $("#errorLogin").html("<h4 id='errorLogin'><kbd class= label-success>" + retorno + "</kbd></h2>");
            let errorLoginModal = $('#modalerrorLogin').modal('show');
            limpiarCamposEncuesta();

        }, function (error) {
            console.info("error encuesta", error);
            let errorLogin = $("#errorLogin").html("<h4 id='errorLogin'><kbd class= label-danger>" + error + "</kbd></h2>");
            let errorLoginModal = $('#modalerrorLogin').modal('show');
            // alert((error.responseJSON).error);
        });
        
}
    
function tiempoEstimado(){
        alert("hola")
    
}

function limpiarCamposEncuesta() {
    let pedido: any = $("#pedido").val("");
    let mesa: any = $("#mesa").val("");
    let puntos_mesa: any = $("#puntos_mesa").val("0");
    let puntos_restaurante: any = $("#puntos_restaurante").val("0");
    let puntos_mozo: any = $("#puntos_mozo").val("0");
    let puntos_cocinero: any = $("#puntos_cocinero").val("0");
    let comentario: any = $("#comentario").val("");



}
