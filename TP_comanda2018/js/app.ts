//let servidor= "https://santiagolo902.000webhostapp.com/server/";
//let servidor= "https://comanda2018tp.000webhostapp.com/server/";
let servidor = "http://localhost:8080/TP_comanda2018/server/";

let titulo: any;
let fotoTitulo: any;
let md: any;
let claveTemp: any;
let emailTemp: any;
let data: any = datosToken();
let tipo: string;
let idGlobal :number



//FUNCIONES TOKEN 

function datosToken() {
    $.ajax({
        type: "POST",
        url: servidor + "datosToken/",
        headers: { "token": localStorage.getItem('token') }
    }).then(function (retorno) {
        console.log("datosToken: ", retorno.tipo);
        console.log("ID token: ", retorno.id);
        tipo = retorno.tipo;
        idGlobal = retorno.id
        return retorno;
    }, function (error) {

        console.log(error.responseText);
        console.log(error);
    });
}

function holaToken() {
    $.ajax({
        type: "POST",
        url: servidor + "datosToken/",
        headers: { "token": localStorage.getItem('token') }
    }).then(function (retorno) {
        //let foto = servidor +"empleado/verImagen/"+retorno.email;
        //fotoTitulo = `<td><img src="${servidor +"empleado/verImagen/"+retorno.email}" id="tableBanner" width="50" height="50" /></td>`;
        //fotoTitulo = `<img src="${servidor+retorno.foto}" width="50" height="50" />`;
        titulo = retorno.nombre;
        fotoTitulo = servidor + retorno.foto;
        let msjHola = $("#msjHola").html(
            "<h4 id='msjHola' align=center ><kbd>Bienvenido " + retorno.nombre + "</kbd></h4>"
            //`<img src="${servidor+retorno.foto}" class="img-responsive img-thumbnail" width="304" height="236" />
        );
        //let fotoHola = $("#msjHola").html(`<img src="${servidor+retorno.foto}" width="50" height="50" />`);
    }, function (error) {
        //alert("error en cargarDatos. Contacte al administrador.");
        console.log(error.responseText);
        console.log(error);
    });
}


//FUNCIONES LOGIN

$(function () {
    $('.forgot-pass').click(function (event) {
        $(".pr-wrap").toggleClass("show-pass-reset");
    });

    $('.pass-reset-submit').click(function (event) {
        $(".pr-wrap").removeClass("show-pass-reset");
    });


    //AGREGA LOS DATOS DE USUARIOS AL LOGIN

    var btnTestAdmin = $("#TestAdmin");
    btnTestAdmin.click(manejadorAdmin);

    var TestCocinero = $("#TestCocinero");
    TestCocinero.click(manejadorCocinero);

    var TestBartender = $("#TestBartender");
    TestBartender.click(manejadorBartender);

    var TestCervecero = $("#TestCervecero");
    TestCervecero.click(manejadorCervecero);

    var TestMozo = $("#TestMozo");
    TestMozo.click(manejadorMozo);

    let btnLimpiar = $("#btnLimpiar")//document.getElementByIdmismos selectors que css . o #
    btnLimpiar.click(function () {
        localStorage.clear();
        window.location.replace("./index.html");
    })
    let btnCerrarNav = $("#btnCerrarNav")
    btnCerrarNav.click(function () {
        $('.navbar-collapse').collapse('hide');
    })

    var btnABMempleados = $("#btnABMempleados");
    btnABMempleados.click(volverAdmin);

    var btnAgregar = $("#btnAgregar");
  //  btnAgregar.click(agregarEmpleado);

    let cbFilto = $("#cbFilto")
    //cbFilto.change(filtrarAdmin);
    let btnMenuListado = $("#btnMenuListado")
    btnMenuListado.click(sosSocio)
    

});
function esconderNav() {
    $('.navbar-collapse').collapse('hide');
}


function manejadorAdmin() {
    $("#email").val("admin@comanda.com");
    $("#clave").val("abc123");
}

function manejadorCocinero() {
    $("#email").val("cocinero1@comanda.com");
    $("#clave").val("abc123");
}
function manejadorBartender() {
    $("#email").val("bartender1@comanda.com");
    $("#clave").val("abc123");
}
function manejadorCervecero() {
    $("#email").val("cervecero1@comanda.com");
    $("#clave").val("abc123");
}
function manejadorMozo() {
    $("#email").val("mozo1@comanda.com");
    $("#clave").val("abc123");
}


//FUNCIONES POST LOGIN (VALIDACIONES)

function Login() {
    console.log("ingresar");
    var datosLogin = { email: $("#email").val(), clave: $('#clave').val() };
    if (datosLogin.email == "") {
        let errorEmail = $("#errorEmail").html("<h4 id='errorEmail'><kbd class= label-warning>Debe ingresar email</kbd></h2>").fadeToggle('slow');
        return;
    } else {
        //$("#email").addClass("sinError");
        let errorEmail = $("#errorEmail").html("");
    }
    if (datosLogin.clave == "") {
        let errorclave = $("#errorClave").html("<h4 id='errorClave'><kbd class= label-warning>Debe ingresar clave</kbd></h2>").fadeToggle('slow');
        return;
    } else {
        let errorclave = $("#errorClave").html("").fadeToggle('slow');
    }

    $.ajax({
        type: "post",
        url: servidor + "Login/",
        data: datosLogin,
        dataType: 'json'

    })
        .then(function (retorno) {
            console.info("bien:", retorno);
            localStorage.setItem('token', retorno.Token);
            window.location.replace("./home.html")
        }, function (error) {
            console.info("error login", error.responseJSON);
            let errorLogin = $("#errorLogin").html("<h4 id='errorLogin'><kbd class= label-danger>" + error.responseJSON + "</kbd></h2>");
            let errorLoginModal = $('#modalerrorLogin').modal('show');
            // alert((error.responseJSON).error);
        });
}


function volverAdmin() {
    $('.navbar-collapse').collapse('hide');
    $("#filtrosPendientes").hide();
    $("#sTabla").show();
    $("#sTablaEmpleados").hide();
    $("#MinMaxOperaciones").hide();
    $("#filtrosLogin").hide();
    $("#filtrosOperaciones").hide();
    $("#filtrosNegativos").hide();
    $("#filtrosCancelados").hide();
    $("#filtrosMesas").hide();
    $("#usoMesas").hide();
    //TSMostrarGrillaEmpleados();
}



//VALIDACIÓN PERFIL SOCIO


function sosSocio() {
    console.log("tipo en sosSocio: ",tipo);
    
    if (tipo != "socio") {
        //modalIngresar
        let modalIngresar = $("#modalIngresar").html("");
        modalIngresar.append(`
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 style="color: red" class="modal-title"><b>Error</b></h4>
            </div>
            <div class="modal-body">
                <h4 style="text-align: center" id='errorLogin'><kbd class= label-danger>Acceso denegado</kbd></h2>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
            </div>
        </div>

    </div>
        
        `)
        return modalIngresar = $('#modalIngresar').modal('show');
    }
    
}
