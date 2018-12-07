//let servidor= "https://santiagolo902.000webhostapp.com/server/";
//let servidor= "https://comanda2018tp.000webhostapp.com/server/";
var servidor = "http://localhost:8080/TP_comanda2018/server/";
var titulo;
var fotoTitulo;
var md;
var claveTemp;
var emailTemp;
var data = datosToken();
var tipo;
var idGlobal;
//FUNCIONES LOGIN
$(function () {
    $('.forgot-pass').click(function (event) {
        $(".pr-wrap").toggleClass("show-pass-reset");
    });
    $('.pass-reset-submit').click(function (event) {
        $(".pr-wrap").removeClass("show-pass-reset");
    });
    /*
    Muestra botones con los diferentes tipos de usuario a loguear.
    Al seleccionar un tipo de usuario, carga datos para login mediante los manejadores.
    */
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
    var btnLimpiar = $("#btnLimpiar"); //document.getElementByIdmismos selectors que css . o #
    btnLimpiar.click(function () {
        localStorage.clear();
        window.location.replace("./index.html");
    });
    var btnCerrarNav = $("#btnCerrarNav");
    btnCerrarNav.click(function () {
        $('.navbar-collapse').collapse('hide');
    });
    var btnABMempleados = $("#btnABMempleados");
    btnABMempleados.click(volverAdmin);
    var btnListaPedidos = $("#btnPendientes");
    btnListaPedidos.click(filtrosPendientes);
    var btnAgregar = $("#btnAgregar");
    btnAgregar.click(agregarEmpleado);
    var cbFilto = $("#cbFilto");
    cbFilto.change(filtrarAdmin);
    var btnMenuListado = $("#btnMenuListado");
    btnMenuListado.click(sosSocio);
});
function esconderNav() {
    $('.navbar-collapse').collapse('hide');
}
//SETEA LOS DATOS DE USUARIOS
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
/*
Validación de campos logIn.
*/
function Login() {
    console.log("ingresar");
    var datosLogin = { email: $("#email").val(), clave: $('#clave').val() };
    if (datosLogin.email == "") {
        var errorEmail = $("#errorEmail").html("<h4 id='errorEmail'><kbd class= label-warning>Debe ingresar email</kbd></h2>").fadeToggle('slow');
        return;
    }
    else {
        //$("#email").addClass("sinError");
        var errorEmail = $("#errorEmail").html("");
    }
    if (datosLogin.clave == "") {
        var errorclave = $("#errorClave").html("<h4 id='errorClave'><kbd class= label-warning>Debe ingresar clave</kbd></h2>").fadeToggle('slow');
        return;
    }
    else {
        var errorclave = $("#errorClave").html("").fadeToggle('slow');
    }
    /*
    Conecta con el servidor mediante un POST A /LogIn.
    Valida que los datos ingresados corresponden a un usuario registrado y obtiene el Token del mismo.
    El Token lo guarda en el LocalStorage.
    Luego, redirecciona a la pagina Home.

    En caso de error, ademas del mensaje configurado en el modal se va a visualizar el mensaje que viaja
    desde el servidor ya que el mismo posee validaciones.
    */
    $.ajax({
        type: "post",
        url: servidor + "Login/",
        data: datosLogin,
        dataType: 'json'
    })
        .then(function (retorno) {
        console.info("bien:", retorno);
        localStorage.setItem('token', retorno.Token);
        window.location.replace("./home.html");
    }, function (error) {
        console.info("error login", error.responseJSON);
        var errorLogin = $("#errorLogin").html("<h4 id='errorLogin'><kbd class= label-danger>" + error.responseJSON + "</kbd></h2>");
        var errorLoginModal = $('#modalerrorLogin').modal('show');
        // alert((error.responseJSON).error);
    });
}
//FUNCIONES TOKEN 
/*
Lee el Token almacenado en el LocalStorage
Conecta con el servidor y obtiene con el token los datos del usuario.
*/
function datosToken() {
    $.ajax({
        type: "POST",
        url: servidor + "datosToken/",
        headers: { "token": localStorage.getItem('token') }
    }).then(function (retorno) {
        console.log("datosToken: ", retorno.tipo);
        console.log("ID token: ", retorno.id);
        tipo = retorno.tipo;
        idGlobal = retorno.id;
        return retorno;
    }, function (error) {
        console.log(error.responseText);
        console.log(error);
    });
}
/*
Oculta todo lo que hay en pantalla para que al seleccionar otra acción no continuen
tablas o modals de la actividad anterior.
*/
function volverAdmin() {
    console.log("Entre!");
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
    TSMostrarGrillaEmpleados();
}
function volverAdminPedidos() {
    console.log("Entre!");
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
    //TSMostrarGrillaPedidos();
}
//VALIDACIÓN PERFIL SOCIO
function sosSocio() {
    console.log("tipo en sosSocio: ", tipo);
    if (tipo != "socio") {
        //modalIngresar
        var modalIngresar = $("#modalIngresar").html("");
        modalIngresar.append("\n        <div class=\"modal-dialog\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>\n                <h4 style=\"color: red\" class=\"modal-title\"><b>Error</b></h4>\n            </div>\n            <div class=\"modal-body\">\n                <h4 style=\"text-align: center\" id='errorLogin'><kbd class= label-danger>Acceso denegado</kbd></h2>\n            </div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Salir</button>\n            </div>\n        </div>\n\n    </div>\n        \n        ");
        return modalIngresar = $('#modalIngresar').modal('show');
    }
}
/*
Conecta con el servidor y obtiene todos los empleados existentes mediante GET obteniendo el Token del
localStorage.
Muestra una tabla con los empleados existentes.
*/
function TSMostrarGrillaEmpleados() {
    $.ajax({
        url: servidor + "empleado/",
        type: 'GET',
        headers: { "token": localStorage.getItem('token') },
        beforeSend: function () {
            var tCuerpo = $("#tCuerpo");
            var tCabeza = $("#tCabeza");
            tCabeza.html("");
            tCuerpo.html("");
            $('#tCuerpo').html('<img src="IMG/5.gif">');
        }
        //por cada dato obtenido con GET, genera un nuevo OBJ Empleado y lo agrega a la lista.
    }).then(function (aux) {
        var empAux;
        var listaEmpleados = [];
        for (var index = 0; index < aux.length; index++) {
            empAux = new restaurante.empleado(aux[index].id, aux[index].email, aux[index].clave, aux[index].usuario, aux[index].tipo, aux[index].estado);
            listaEmpleados.push(empAux);
        }
        console.log("Clase: ", listaEmpleados);
        filtrosEmpleado();
        filtrarAdmin();
        /*Se declaran las variables de filtros por CheckBox.
        Todos los checkBox se muestran marcados menos ID. al clickear un check, se va a llamar a la funcion
        Filtrar Admin para que muestre/oculte la columna seleccionada en la grilla.
        */
        var chIDUsuario = $("#chIDUsuario");
        var chUsuario = $("#chUsuario");
        var chEmail = $("#chEmail");
        var chTipo = $("#chTipo");
        var chEstado = $("#chEstado");
        chIDUsuario.click(filtrarAdmin);
        chUsuario.click(filtrarAdmin);
        chEmail.click(filtrarAdmin);
        chTipo.click(filtrarAdmin);
        chEstado.click(filtrarAdmin);
        //Combo de filtro por tipo de empleado (todos, cocinero, bartender, socio, etc) 
        var cbFilto = $("#cbFilto");
        cbFilto.change(filtrarAdmin); //--> Activa el filtro
        var Hola = $("#Hola").hide();
        var ABMemplados = $("#ABMemplados").fadeIn();
        var tCuerpo = $("#tCuerpo");
        var tCabeza = $("#tCabeza");
        tCabeza.html("");
        tCuerpo.html("");
        var msjEmpleado = $("#msjEmpleado").html("<h4 id='msjEmpleado'><kbd> Lista de Usuarios</kbd></h2>");
        tCabeza.append("<tr class='success'>" +
            "<th> ID</th>" +
            "<th> Usuario</th>" +
            "<th> Email</th>" +
            "<th> Tipo</th>" +
            "<th> Estado</th>" +
            "<th> <i class='fas fa-info-circle'></i></th>" +
            "<th> Borrar</th>" +
            "<th> Modificar</th>" +
            "</tr>");
        //Completa la grilla con los datos guardados en Lista de empleados.
        for (var index = 0; index < listaEmpleados.length; index++) {
            tCuerpo.append("\n                        <tr>\n                        <td> " + listaEmpleados[index].id + "</td>\n                        <td> " + listaEmpleados[index].usuario + "</td>\n                        <td>" + listaEmpleados[index].email + "</td>\n                        <td>" + listaEmpleados[index].tipo + "</td>\n                        <td>" + listaEmpleados[index].estado + "</td>\n                        <td><button  class=\"btn btn-xs btn-info\" onclick=mostrarDetallesAdmin(" + listaEmpleados[index].id + ") > <i class=\"fas fa-info-circle\"></i></button></td>\n                        <td><button id=btnEliminar class=\"btn btn-xs btn-danger\" onclick=eliminarEmpleado(" + listaEmpleados[index].id + ")> <i class=\"far fa-trash-alt\"></i> Borrar</button></td>\n                        <td><button id=btnModificar class=\"btn btn-xs btn-warning\" onclick=modificarEmpleado(" + listaEmpleados[index].id + ")><i class=\"fas fa-edit\"></i>Modificar</button></td>\n                        </tr>");
        }
        //Valida que el empleado sea socio. Sino no permite acceder
    }, function (error) {
        console.info("error mostrar grilla", error);
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class= label-danger>no tiene permisos para realizar esta tarea</kbd></h4>");
        var modalError = $('#modalError').modal('show');
    });
}
//APLICA LOS FILTROS A LA GRILLA DE EMPLEADOS
function TSMostrarGrillaEmpleadosFiltrada(auxEmpleados) {
    var chIDUsuario = $("#chIDUsuario");
    var chUsuario = $("#chUsuario");
    var chEmail = $("#chEmail");
    var chTipo = $("#chTipo");
    var chEstado = $("#chEstado");
    var auxID;
    var auxEmail;
    var auxTipo;
    var auxEstado;
    /*SETEA LOS VALORES A LAS VARIABLES QUE VAN A COMPLETAR LA CABECERA.
    Si el check esta tildado, setea el valor "<th> ID</th>" en la variable.
    Sino, setea vacío.
    */
    //ID
    if (chIDUsuario.is(':checked')) {
        auxID = "<th> ID</th>";
    }
    else {
        auxID = "";
    }
    // Email
    if (chEmail.is(':checked')) {
        auxEmail = "<th> Email</th>";
    }
    else {
        auxEmail = "";
    }
    //tipo
    if (chTipo.is(':checked')) {
        auxTipo = "<th>Tipo</th>";
    }
    else {
        auxTipo = "";
    }
    // Estado
    if (chEstado.is(':checked')) {
        auxEstado = "<th> Estado</th>";
    }
    else {
        auxEstado = "";
    }
    //SE DECLARAN CUERPO Y CABECERA PARA LA GRILLA FILTRADA. 
    var tCuerpo = $("#tCuerpo");
    var tCabeza = $("#tCabeza");
    tCabeza.html("");
    tCuerpo.html("");
    /*SE COMPLETA LA CABECERA CON LAS VARIABLES CON VALORES PREVIAMENTE SETEADOS EN LOS PASOS ANTERIORES
     + LOS TITULOS BORRAR Y MODIFICAR QUE SIEMPRE SON VISIBLES */
    tCabeza.append("<tr class='success'>" +
        auxID +
        "<th> Usuario</th>" +
        auxEmail +
        auxTipo +
        auxEstado +
        "<th> <span class='glyphicon glyphicon-info-sign'></th>" +
        "<th> Borrar</th>" +
        "<th> Modificar</th>" +
        "</tr>");
    /*
    Completa el cuerpo de la grilla con todos los datos de los empleados.
    Mediante las funciones MAP (TSidEmpleado, etc) se verifica si los check estan tildados, y dependiendo
    de esta verificación, se retornan los datos a completar en la grilla filtrada.
    
    .
    */
    for (var index = 0; index < auxEmpleados.length; index++) {
        var mapID = TSidEmpleado(auxEmpleados);
        var mapEmail = TSemailEmpleado(auxEmpleados);
        var mapTipo = TStipoEmpleado(auxEmpleados);
        var mapEstado = TSestadoEmpleado(auxEmpleados);
        //append agrega mas al html
        tCuerpo.append("<tr>" +
            mapID[index] +
            ("<td>" + auxEmpleados[index].usuario + "</td>") + //El Usuario no se oculta mediante check.
            mapEmail[index] +
            mapTipo[index] +
            mapEstado[index] +
            ("<td><button  class=\"btn btn-xs btn-info\" onclick=mostrarDetallesAdmin(" + auxEmpleados[index].id + ") > <i class=\"fas fa-info-circle\"></i> </button></td>\n            <td><button id=btnEliminar class=\"btn btn-xs btn-danger\" onclick=eliminarEmpleado(" + auxEmpleados[index].id + ")><i class=\"far fa-trash-alt\"></i> Borrar</button></td>\n            <td><button id=btnModificar class=\"btn btn-xs btn-warning\" onclick=modificarEmpleado(" + auxEmpleados[index].id + ")><i class=\"fas fa-edit\"></i>Modificar</button></td>\n            </tr>"));
    }
}
/*
Aplca filtro por tipo de empleado según opción del combo seleccionada.
Accede al servidor y obtiene los datos de empleados mediante GET.
Obtiene el Token del localStorage.
*/
function filtrarAdmin() {
    $.ajax({
        url: servidor + "empleado/",
        type: 'GET',
        headers: { "token": localStorage.getItem('token') },
        beforeSend: function () {
            $('#tCuerpo').html('<img src="IMG/5.gif">');
        }
        //Obtiene la opción de filtrado seleccionada y la convierte a string.
    }).then(function (aux) {
        var empAux;
        var listaEmpleados = [];
        var tipoFiltrado = String($("#cbFilto").val());
        for (var index = 0; index < aux.length; index++) {
            empAux = new restaurante.empleado(aux[index].id, aux[index].email, aux[index].clave, aux[index].usuario, aux[index].tipo, aux[index].estado);
            listaEmpleados.push(empAux);
        }
        /*Verifica que sea distinto a opción "Todos" y filtra por la opción seleccionada igualandola al
         tipo de empleado existente en cada objeto */
        if (tipoFiltrado != "todos") {
            var empleadosFiltrados = [];
            empleadosFiltrados = aux.filter(function (emp) {
                return restaurante.tipoEmpleado[emp.tipo] === restaurante.tipoEmpleado[tipoFiltrado];
            });
            TSMostrarGrillaEmpleadosFiltrada(empleadosFiltrados);
            //console.log(empleadosFiltrados);
            //Si la opción es "Todos", llama a mostrar grilla enviando todos los datos recibidos mediante GET.
        }
        else {
            if (aux != false) {
                console.info("bien -->", aux);
                TSMostrarGrillaEmpleadosFiltrada(aux);
            }
            //caso de error de obtención de datos
            else {
                var msjVehiculo = $("#msjEmpleado").html("<h4 id='msjEmpleado'><kbd>" + aux + "</kbd></h2>");
                console.log(aux);
            }
        }
    }, function (error) {
        console.info("error", error);
    });
}
//Mustra los tipos de filtro y tabla 
function filtrosEmpleado() {
    $("#sTablaEmpleados").show();
    $("#filtrosEmpleados").show();
    var filtrosEmpleados = $("#filtrosEmpleados").html("");
    filtrosEmpleados = $("#filtrosEmpleados").html("\n                    <h4 id=\"mensaje\"><kbd>Filtros</kbd></h4>\n                    <label for=\"cbFilto\">Filtrar por</label>\n                    <select class=\"form-control\" id=\"cbFilto\" name=\"cbFilto\">\n                        <option value=\"todos\">todos </option>\n                        <option value=\"cocinero\">cocinero </option>\n                        <option value=\"bartender\">bartender </option>\n                        <option value=\"mozo\">mozo </option>\n                        <option value=\"cervecero\">cervecero </option>\n                        <option value=\"socio\">socio </option>\n                    </select>\n                        \n                    <br>\n                    <br>\n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"id\" id=\"chIDUsuario\">ID</label>\n                    </div>\n                    \n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"email\" id=\"chEmail\" checked>Email</label>\n                    </div>\n\n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"tipo\" id=\"chTipo\" checked>Tipo</label>\n                    </div>\n\n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"estado\" id=\"chEstado\" checked>Estado</label>\n                    </div>\n                            \n    ");
}
//FUNCIONES MAP
/*
Verifica que el Check de ID esta tildado. Si está tildado, mapea el elemento ID del objeto empleado
recibido.
Retorna el ID del empleado entre <td><td>.
*/
function TSidEmpleado(auxEmpleados) {
    var chIDUsuario = $("#chIDUsuario");
    var lID;
    return auxEmpleados.map(function (elemento) {
        if (chIDUsuario.is(':checked')) {
            return lID = "<td>" + elemento.id + "</td>";
        }
        else {
            return lID = "";
        }
    });
}
/*
Verifica que el Check de mail esta tildado. Si está tildado, mapea el elemento Mail del objeto empleado
recibido.
Retorna el mail del empleado entre <td><td>.
*/
function TSemailEmpleado(auxEmpleados) {
    var chEmail = $("#chEmail");
    var lMail;
    return auxEmpleados.map(function (elemento) {
        if (chEmail.is(':checked')) {
            return lMail = "<td>" + elemento.email + "</td>";
        }
        else {
            return lMail = "";
        }
    });
}
/*
Verifica que el Check de Tipo esta tildado. Si está tildado, mapea el elemento Tipo del objeto empleado
recibido.
Retorna el Tipo del empleado entre <td><td>.
*/
function TStipoEmpleado(auxEmpleados) {
    var chTipo = $("#chTipo");
    var lEmpleado;
    return auxEmpleados.map(function (elemento) {
        if (chTipo.is(':checked')) {
            return lEmpleado = "<td>" + elemento.tipo + "</td>";
        }
        else {
            return lEmpleado = "";
        }
    });
}
/*
Verifica que el Check de estado esta tildado. Si está tildado, mapea el elemento estado del objeto empleado
recibido.
Retorna el estado del empleado entre <td><td>.
*/
function TSestadoEmpleado(auxEmpleados) {
    var chEstado = $("#chEstado");
    var lEstado;
    return auxEmpleados.map(function (elemento) {
        if (chEstado.is(':checked')) {
            return lEstado = "<td>" + elemento.estado + "</td>";
        }
        else {
            return lEstado = "";
        }
    });
}
//OCULTAR LABEL
function ocultarLabel(id) {
    if (id != null) {
        document.getElementById(id).style.visibility = "hidden";
    }
}
//FUNCIONES ABM EMPLEADOS
/*
AGREGAR EMPLEADO.
Valida que todos los campos obligatorios se encuentren completos.
Conecta con el servidor y hace un POST con los datos del nuevo empleado recuperados del formulario.
*/
function agregarEmpleado() {
    var usuarioTXT = $("#nombreTXT").val();
    var emailTXT = $("#emailTXT").val();
    var claveTXT = $("#claveTXT").val();
    var tipoCB = $("#tipoCB").val();
    var estadoCB = $("#estadoCB").val();
    //let file_data = $("#perfilFile").prop("files")[0];
    //console.log(file_data);
    if (usuarioTXT == "") {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe ingresar nombre</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (emailTXT == "") {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe ingresar email</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (claveTXT == "") {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe ingresar clave</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (tipoCB === null) {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe seleccionar tipo</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (estadoCB === null) {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe seleccionar estado</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    // if (file_data === undefined) {
    //     let msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe seleccionar foto</kbd></h2>");
    //     return;
    // }
    var form_data = new FormData();
    form_data.append('email', emailTXT);
    form_data.append('clave', claveTXT);
    form_data.append('usuario', usuarioTXT);
    form_data.append('tipo', tipoCB);
    form_data.append('estado', estadoCB);
    //form_data.append('foto', file_data);
    //let empleadoAux: estacionamiento.empleado = new estacionamiento.empleado(nombre,sexo,email,clave,turno,perfil,foto,estado);
    //console.log(empleadoAux);
    $.ajax({
        url: servidor + "empleado/alta",
        type: 'post',
        data: form_data,
        headers: { "token": localStorage.getItem('token') },
        contentType: false,
        processData: false,
        cache: false
    }).then(function (itemResponse) {
        limpiarCampos();
        console.info("bien -->", itemResponse);
        var msjError = $("#msjBien").html("<h4 id='msjBien'><kbd class= label-success>" + itemResponse + "</kbd></h4>");
        var modalError = $('#modalBien').modal('show');
        console.log(itemResponse);
        TSMostrarGrillaEmpleados();
    }, function (error) {
        limpiarCampos();
        console.info("error agregar usuarios", error);
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class= label-danger>" + error.responseJSON + "</kbd></h4>");
        var modalError = $('#modalError').modal('show');
    });
}
//Limpiar Campos
function limpiarCampos() {
    var usuarioTXT = $("#nombreTXT").val("");
    var emailTXT = $("#emailTXT").val("");
    var claveTXT = $("#claveTXT").val("");
    var tipoCB = $("#tipoCB").val("");
    var estadoCB = $("#estadoCB").val("");
    var clienteTXT = $("#clienteTXT").val("");
    var mesaTXT = $("#mesaTXT").val("");
    var file_data = $("#fotoFile").prop("files")[0];
    var comandaTXT = $("#comandaTXT").val("");
    var productoTXT = $("#productoTXT").val("");
    var cantidadTXT = $("#cantidadTXT").val("");
    var tipoTXT = $("#tipoTXT").val("");
    var montoTXT = $("#montoTXT").val("");
    ocultarLabel("msjErrorAlta");
}
/*
ELIMINAR EMPLEADO
Verifica que no se pueda borrar al primer usuario admin.
Conecta con el servidor y elimina un empleado mediante POST.
*/
function eliminarEmpleado(index) {
    if (index === 1) {
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>No se puede borrar a Admin</kbd></h4>");
        var modalError = $('#modalError').modal('show');
        return;
    }
    bootbox.confirm("¿Esta Seguro?", function (result) {
        if (result == true) {
            var form_data = new FormData();
            form_data.append('id', index);
            $.ajax({
                type: "POST",
                url: servidor + "empleado/borrar/",
                data: form_data,
                headers: { "token": localStorage.getItem('token') },
                contentType: false,
                processData: false,
                cache: false
            }).then(function (retorno) {
                limpiarCampos();
                console.log("borro " + retorno);
                var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>" + retorno + "</kbd></h4>");
                var modalError = $('#modalError').modal('show');
                var Hola = $("#Hola").hide();
                var ABMemplados = $("#ABMemplados").fadeIn();
                TSMostrarGrillaEmpleados();
            }, function (error) {
                limpiarCampos();
                //alert("error en cargarDatos. Contacte al administrador.");
                console.log(error.responseText);
                var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>" + error.responseJSON + "</kbd></h4>");
                var modalError = $('#modalError').modal('show');
            });
        }
        else {
            var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>No se borro nada</kbd></h4>");
            var modalError = $('#modalError').modal('show');
        }
    });
}
/*
MODIFICAR EMPLEADO

Valida que no se pueda modificar el usuario admin.
Conecta con el servidor y trae los datos del empleado a modificar mediante un POST.

Al seleccionar el boton "modificar" se guarda el ID del empleado a modificar.

Una vez que obtiene los datos, los carga en el formulario de alta para hacer las
modificaciones necesarias.
Se desactiva la operación "AgregarEmpleado" del boton agregar. Y se muestra la opción modificar.
OFF Agregar
ON Modificar
Se llama a la función modificar una vez completos los campos del formulario con los nuevos datos.
*/
//Modificar Empleado
function modificarEmpleado(index) {
    if (index === 1) {
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>No se puede modificar a Admin</kbd></h4>");
        var modalError = $('#modalError').modal('show');
        return;
    }
    var form_data = new FormData();
    form_data.append('id', index);
    //console.log(index);
    $.ajax({
        type: "POST",
        url: servidor + "empleado/traerUno/",
        data: form_data,
        headers: { "token": localStorage.getItem('token') },
        contentType: false,
        processData: false,
        cache: false
    }).then(function (retorno) {
        console.log(Object(retorno));
        //usuario
        $("#nombreTXT").val(retorno.usuario);
        //Email
        $("#emailTXT").val(retorno.email);
        //tipo
        $("#tipoCB").val(retorno.tipo);
        //estado
        $("#estadoCB").val(retorno.estado);
        //clave
        claveTemp = retorno.clave;
        //$("#claveTXT").val(retorno.clave);
        //let id = retorno.id
        var btnAgregar = $("#btnAgregar");
        btnAgregar.attr("value", "Modificar");
        btnAgregar.off("click", agregarEmpleado);
        btnAgregar.on("click", md = function () {
            Modificar(retorno.id);
        });
    }, function (error) {
        //alert("error en cargarDatos. Contacte al administrador.");
        console.log(error.responseText);
    });
}
/*
MODIFICAR
La función Modificar recupera y guarda en variables los nuevos datos ingresados del empleado a modificar
(se recibe ID del empleado como parametro).

Muestra un mensaje de Estas seguro?. Al seleccionar SI, conecta con el servidor y mediante un POST
hace los cambios correspondientes.
Luego desactiva la función "Modificar" y vuelve a activar la función "Agregar empleado" del boton agregar.

*/
function Modificar(idAux) {
    var usuarioTXT = $("#nombreTXT").val();
    var emailTXT = $("#emailTXT").val();
    var claveTXT = $("#claveTXT").val();
    var tipoCB = $("#tipoCB").val();
    var estadoCB = $("#estadoCB").val();
    if (usuarioTXT == "") {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe ingresar usuario</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (emailTXT == "") {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe ingresar email</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (claveTXT == "") {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe ingresar clave</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (tipoCB === null) {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe seleccionar tipo</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    if (estadoCB === null) {
        var msjErrorAlta = $("#msjErrorAlta").html("<h4 id='msjErrorAlta'><kbd class= label-warning>Debe seleccionar estado</kbd></h2>");
        document.getElementById("msjErrorAlta").style.visibility = "visible";
        return;
    }
    var form_data = new FormData();
    form_data.append('id', idAux);
    form_data.append('usuario', usuarioTXT);
    form_data.append('email', emailTXT);
    form_data.append('clave', claveTXT);
    form_data.append('tipo', tipoCB);
    form_data.append('estado', estadoCB);
    //form_data.append('foto', file_data);
    bootbox.confirm("¿Esta Seguro?", function (result) {
        if (result == true) {
            $.ajax({
                type: "POST",
                url: servidor + "empleado/modificar/",
                data: form_data,
                headers: { "token": localStorage.getItem('token') },
                contentType: false,
                processData: false,
                cache: false
            }).then(function (retorno) {
                console.log(retorno.responseText);
                console.log(retorno);
                var btnAgregar = $("#btnAgregar");
                btnAgregar.attr("value", "Agregar");
                btnAgregar.off("click", md);
                btnAgregar.on("click", agregarEmpleado);
                limpiarCampos();
                var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>Se modifico al empleado con exito</kbd></h4>");
                var modalError = $('#modalError').modal('show');
                TSMostrarGrillaEmpleados();
            }, function (error) {
                alert("error en cargarDatos. Contacte al administrador.");
                //console.log(error.responseText);
                //var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>" + error.responseJSON + "</kbd></h4>");
                //var modalError = $('#modalError').modal('show');
            });
        }
        else {
            var btnAgregar = $("#btnAgregar");
            btnAgregar.attr("value", "Agregar");
            btnAgregar.off("click", md);
            btnAgregar.on("click", agregarEmpleado);
            limpiarCampos();
            TSMostrarGrillaEmpleados();
            var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>No se modifico nada</kbd></h4>");
            var modalError = $('#modalError').modal('show');
        }
    });
}
/*
INFO EMPLEADOS
Muestra la info de un empleado conectando con el servidor y luego hace un POST a traerUno enviando el ID
obtenido del empleado seleccionado.

*/
function mostrarDetallesAdmin(index) {
    var form_data = new FormData();
    form_data.append('id', index);
    //console.log(index);
    $.ajax({
        type: "POST",
        url: servidor + "empleado/traerUno/",
        data: form_data,
        headers: { "token": localStorage.getItem('token') },
        contentType: false,
        processData: false,
        cache: false
    }).then(function (aux) {
        emailTemp = aux.email;
        var infoDetalles = $("#infoDetalles");
        infoDetalles.html("");
        infoDetalles.append("\n            <p><b>ID:</b> " + aux.id + "</p>\n            <p><b>Usuario:</b> " + aux.usuario + "</p>\n            <p><b>Email:</b> " + aux.email + "</p>\n            <p><b>Tipo:</b> " + aux.tipo + "</p>\n            <p><b>Estado:</b> " + aux.estado + "</p>\n");
        var modalInfo = $('#modalInfo').modal('show');
        // <p><img src="${servidor + aux.foto}"id="tableBanner width="150" height="150" /></p>
    }, function (error) {
        console.info("error", error);
    });
}
/***********PEDIDOS*************/
//FORM COMANDA
function mostrarFormComandas() {
    if (tipo == "cocinero" || tipo == "bartender" || tipo == "cervecero") {
        //modalIngresar
        var modalIngresarCom = $("#modalIngresarCom").html("");
        //let modalIngresar =  $('#modalIngresar').modal('show');
        //<h4 id='errorLogin'><kbd class= label-danger>Acceso denegado</kbd></h2>
        modalIngresarCom.append("\n        <div class=\"modal-dialog\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>\n                <h4 style=\"color: red\" class=\"modal-title\"><b>Error</b></h4>\n            </div>\n            <div class=\"modal-body\">\n                <h4 style=\"text-align: center\" id='errorLogin'><kbd class= label-danger>Acceso denegado</kbd></h2>\n            </div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Salir</button>\n            </div>\n        </div>\n\n    </div>\n        \n        ");
    }
    else {
        agregarComanda();
    }
}
function agregarComanda() {
    var clienteTXT = $("#clienteTXT").val();
    var mesaTXT = $("#mesaTXT").val();
    var file_data = $("#fotoFile").prop("files")[0];
    //console.log("clienteTXT: ",clienteTXT);
    var form_data = new FormData();
    form_data.append('cliente', clienteTXT);
    form_data.append('idMesa', mesaTXT);
    form_data.append('foto', file_data);
    $.ajax({
        type: 'POST',
        url: servidor + "comanda/alta",
        data: form_data,
        headers: { "token": localStorage.getItem('token') },
        contentType: false,
        processData: false,
        cache: false
    }).then(function (itemResponse) {
        console.info("bien -->", itemResponse);
        var msjBien = $("#msjBien").html("<h4 id='msjBien'><kbd class= label-success>" + itemResponse + "</kbd></h4>");
        var modalIngresar = $('#modalIngresarCom').modal('hide');
        var modalBien = $('#modalBien').modal('show');
        limpiarCampos();
        // $('#modalError').modal('hide');
        // window.location.replace("./home.html");
        //TSMostrarGrillaComandas();
    }, function (error) {
        console.info("error", error.responseText);
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class= label-danger>" + error.responseJSON + "</kbd></h4>");
        var modalError = $('#modalError').modal('show');
    });
}
//FORM PEDIDO
function mostrarFormPedidos() {
    if (tipo == "cocinero" || tipo == "bartender" || tipo == "cervecero") {
        //modalIngresar
        var modalIngresarPed = $("#modalIngresarPed").html("");
        //let modalIngresar =  $('#modalIngresar').modal('show');
        //<h4 id='errorLogin'><kbd class= label-danger>Acceso denegado</kbd></h2>
        modalIngresarPed.append("\n        <div class=\"modal-dialog\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>\n                <h4 style=\"color: red\" class=\"modal-title\"><b>Error</b></h4>\n            </div>\n            <div class=\"modal-body\">\n                <h4 style=\"text-align: center\" id='errorLogin'><kbd class= label-danger>Acceso denegado</kbd></h2>\n            </div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Salir</button>\n            </div>\n        </div>\n\n    </div>\n        \n        ");
    }
    else {
        agregarPedido();
    }
}
function agregarPedido() {
    var comandaTXT = $("#comandaTXT").val();
    var productoTXT = $("#productoTXT").val();
    var cantidadTXT = $("#cantidadTXT").val();
    var tipoTXT = $("#tipoTXT").val();
    var montoTXT = $("#montoTXT").val();
    //console.log("clienteTXT: ",clienteTXT);
    var form_data = new FormData();
    form_data.append('idComanda', comandaTXT);
    form_data.append('idProducto', productoTXT);
    form_data.append('cantidad', cantidadTXT);
    form_data.append('tipo', tipoTXT);
    form_data.append('monto', montoTXT);
    $.ajax({
        type: 'POST',
        url: servidor + "pedido/alta",
        data: form_data,
        headers: { "token": localStorage.getItem('token') },
        contentType: false,
        processData: false,
        cache: false
    }).then(function (itemResponse) {
        console.info("bien -->", itemResponse);
        var msjBien = $("#msjBien").html("<h4 id='msjBien'><kbd class= label-success>" + itemResponse + "</kbd></h4>");
        var modalIngresar = $('#modalIngresarPed').modal('hide');
        var modalBien = $('#modalBien').modal('show');
        limpiarCampos();
        // $('#modalError').modal('hide');
        // window.location.replace("./home.html");
        //TSMostrarGrillaComandas();
    }, function (error) {
        console.info("error", error.responseText);
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class= label-danger>" + error.responseJSON + "</kbd></h4>");
        var modalError = $('#modalError').modal('show');
    });
}
/**********************LISTADO PEDIDOS****************+ */
function filtrosPendientes() {
    $('.navbar-collapse').collapse('hide');
    $("#filtrosEmpleados").hide();
    $("#ABMemplados").hide();
    $("#filtrosLogin").hide();
    $("#filtrosOperaciones").hide();
    $("#filtrosNegativos").hide();
    $("#MinMaxOperaciones").hide();
    $("#filtrosMesas").hide();
    $("#usoMesas").hide();
    $("#sTablaEmpleados").show();
    //$("#sTablaPendientes").show();
    $("#Hola").hide();
    $("#filtrosPendientes").show();
    var filtrosPendientes = $("#filtrosPendientes").html("");
    filtrosPendientes = $("#filtrosPendientes").append("<br>\n                                                        <br>");
    TSMostrarGrillaPedidos();
}
/*
Muestra un listado con todos los pedidos existentes.
*/
function TSMostrarGrillaPedidos() {
    $.ajax({
        url: servidor + "pedido/",
        type: 'GET',
        headers: { "token": localStorage.getItem('token') },
        beforeSend: function () {
            var tCuerpo = $("#tCuerpo");
            var tCabeza = $("#tCabeza");
            tCabeza.html("");
            tCuerpo.html("");
            $('#tCuerpo').html('<img src="IMG/5.gif">');
        }
        //por cada dato obtenido con GET, genera un nuevo OBJ Empleado y lo agrega a la lista.
    }).then(function (aux) {
        var pedAux;
        var listaPedidos = [];
        for (var index = 0; index < aux.length; index++) {
            pedAux = new restaurante.pedido(aux[index].id, aux[index].idComanda, aux[index].idProducto, aux[index].cantidad, aux[index].tipo, aux[index].monto, aux[index].horaEstimada, aux[index].horaFinal, aux[index].estado, aux[index].idEmpleado, aux[index].fecha);
            listaPedidos.push(pedAux);
        }
        console.log("Clase: ", listaPedidos);
        filtrosPedidos();
        filtrarAdminPedidos();
        /*Se declaran las variables de filtros por CheckBox.
        Todos los checkBox se muestran marcados menos ID. al clickear un check, se va a llamar a la funcion
        Filtrar Admin para que muestre/oculte la columna seleccionada en la grilla.
        */
        var chIDComanda = $("#chIDComanda");
        var chHoraEstimada = $("#chHoraEstimada");
        var chHoraFinal = $("#chHoraFinal");
        var chIDEmpleadop = $("#chIDEmpleadop");
        var chMonto = $("#chMonto");
        var chFecha = $("#chFecha");
        chIDComanda.click(filtrarAdminPedidos);
        chHoraEstimada.click(filtrarAdminPedidos);
        chHoraFinal.click(filtrarAdminPedidos);
        chIDEmpleadop.click(filtrarAdminPedidos);
        chMonto.click(filtrarAdminPedidos);
        chFecha.click(filtrarAdminPedidos);
        //Combo de filtro por tipo de empleado (todos, cocinero, bartender, socio, etc) 
        var cbFiltop = $("#cbFiltop");
        cbFiltop.change(filtrarAdminPedidos); //--> Activa el filtro
        var Hola = $("#Hola").hide();
        var tCuerpo = $("#tCuerpo");
        var tCabeza = $("#tCabeza");
        tCabeza.html("");
        tCuerpo.html("");
        var msjEmpleado = $("#msjEmpleado").html("<h4 id='msjEmpleado'><kbd> Lista de Pedidos</kbd></h2>");
        tCabeza.append("<tr class='success'>" +
            "<th> ID</th>" +
            "<th> ID Comanda</th>" +
            "<th> ID Producto</th>" +
            "<th> Cantidad</th>" +
            "<th> Tipo </th>" +
            "<th> Hora Estimada </th>" +
            "<th> Hora Final </th>" +
            "<th> Estado </th>" +
            "<th> ID Empleado </th>" +
            "<th> Monto </th>" +
            "<th> Fecha </th>" +
            "<th> <i class='fas fa-info-circle'></i></th>" +
            "<th> Borrar</th>" +
            "<th> Modificar</th>" +
            "</tr>");
        //Completa la grilla con los datos guardados en Lista de empleados.
        for (var index = 0; index < listaPedidos.length; index++) {
            tCuerpo.append("\n                    <tr>\n                    <td> " + listaPedidos[index].id + "</td>\n                    <td> " + listaPedidos[index].idComanda + "</td>\n                    <td> " + listaPedidos[index].idProducto + "</td>\n                    <td>" + listaPedidos[index].cantidad + "</td>\n                    <td>" + listaPedidos[index].tipo + "</td>\n                    <td>" + listaPedidos[index].horaEstimada + "</td>\n                    <td>" + listaPedidos[index].horaFinal + "</td>\n                    <td>" + listaPedidos[index].estado + "</td>\n                    <td>" + listaPedidos[index].idEmpleado + "</td>\n                    <td>" + listaPedidos[index].monto + "</td>\n                    <td>" + listaPedidos[index].fecha + "</td>\n                    <td><button  class=\"btn btn-xs btn-info\" onclick=mostrarDetallesAdmin(" + listaPedidos[index].id + ") > <i class=\"fas fa-info-circle\"></i></button></td>\n                    <td><button id=btnModificar class=\"btn btn-xs btn-success\" onclick=ModificarPedido(" + listaPedidos[index].id + ",\"estado\")><i class=\"fas fa-edit\"></i>Modificar</button></td>\n                    </tr>");
        }
        //Valida que el empleado sea socio. Sino no permite acceder
    }, function (error) {
        console.info("error mostrar grilla", error);
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class= label-danger>no tiene permisos para realizar esta tarea</kbd></h4>");
        var modalError = $('#modalError').modal('show');
    });
}
//APLICA LOS FILTROS A LA GRILLA DE PEDIDOS
function TSMostrarGrillaPedidosFiltrada(auxPedidos) {
    var chIDComanda = $("#chIDComanda");
    var chHoraEstimada = $("#chHoraEstimada");
    var chHoraFinal = $("#chHoraFinal");
    var chIDEmpleadop = $("#chIDEmpleadop");
    var chMonto = $("#chMonto");
    var chFecha = $("#chFecha");
    var auxIDComanda;
    var auxHoraEstimada;
    var auxHoraFinal;
    var auxIDEmpleadop;
    var auxMonto;
    var auxFecha;
    /*SETEA LOS VALORES A LAS VARIABLES QUE VAN A COMPLETAR LA CABECERA.
    Si el check esta tildado, setea el valor "<th> ID</th>" en la variable.
    Sino, setea vacío.
    */
    //ID COMANDA
    if (chIDComanda.is(':checked')) {
        auxIDComanda = "<th>ID Comanda</th>";
    }
    else {
        auxIDComanda = "";
    }
    // HoraEstimada
    if (chHoraEstimada.is(':checked')) {
        auxHoraEstimada = "<th>Hora Estimada</th>";
    }
    else {
        auxHoraEstimada = "";
    }
    //HoraFinal
    if (chHoraFinal.is(':checked')) {
        auxHoraFinal = "<th>Hora Final</th>";
    }
    else {
        auxHoraFinal = "";
    }
    // IDEmpleado
    if (chIDEmpleadop.is(':checked')) {
        auxIDEmpleadop = "<th>ID Empleado</th>";
    }
    else {
        auxIDEmpleadop = "";
    }
    // Monto
    if (chMonto.is(':checked')) {
        auxMonto = "<th>Monto</th>";
    }
    else {
        auxMonto = "";
    }
    // Fecha
    if (chFecha.is(':checked')) {
        auxFecha = "<th>Fecha</th>";
    }
    else {
        auxFecha = "";
    }
    //SE DECLARAN CUERPO Y CABECERA PARA LA GRILLA FILTRADA. 
    var tCuerpo = $("#tCuerpo");
    var tCabeza = $("#tCabeza");
    tCabeza.html("");
    tCuerpo.html("");
    /*SE COMPLETA LA CABECERA CON LAS VARIABLES CON VALORES PREVIAMENTE SETEADOS EN LOS PASOS ANTERIORES
     + LOS TITULOS BORRAR Y MODIFICAR QUE SIEMPRE SON VISIBLES */
    tCabeza.append("<tr class='success'>" +
        "<th> ID</th>" +
        auxIDComanda +
        "<th> ID Producto</th>" +
        "<th> Cantidad</th>" +
        "<th> Tipo</th>" +
        auxMonto +
        auxHoraEstimada +
        auxHoraFinal +
        "<th> Estado</th>" +
        auxIDEmpleadop +
        auxFecha +
        "<th> <span class='glyphicon glyphicon-info-sign'></th>" +
        "<th> Borrar</th>" +
        "<th> Modificar</th>" +
        "</tr>");
    /*
    Completa el cuerpo de la grilla con todos los datos de los empleados.
    Mediante las funciones MAP (TSidEmpleado, etc) se verifica si los check estan tildados, y dependiendo
    de esta verificación, se retornan los datos a completar en la grilla filtrada.
    
    .
    */
    for (var index = 0; index < auxPedidos.length; index++) {
        var mapIDComanda = TSidComanda(auxPedidos);
        var mapHoraEstimada = TShoraEstimada(auxPedidos);
        var mapHoraFinal = TShoraFinal(auxPedidos);
        var mapIDEmpleadop = TSidEmpleadop(auxPedidos);
        var mapMonto = TSMonto(auxPedidos);
        var mapFecha = TSFecha(auxPedidos);
        //append agrega mas al html
        tCuerpo.append("<tr>" +
            ("<td>" + auxPedidos[index].id + "</td>") +
            mapIDComanda[index] +
            ("<td>" + auxPedidos[index].idProducto + "</td>") +
            ("<td>" + auxPedidos[index].cantidad + "</td>") +
            ("<td>" + auxPedidos[index].tipo + "</td>") +
            mapMonto[index] +
            mapHoraEstimada[index] +
            mapHoraFinal[index] +
            ("<td>" + auxPedidos[index].estado + "</td>") +
            mapIDEmpleadop[index] +
            mapFecha[index] +
            ("<td><button  class=\"btn btn-xs btn-info\" onclick=mostrarDetallesAdmin(" + auxPedidos[index].id + ") > <i class=\"fas fa-info-circle\"></i> </button></td>\n            <td><button id=btnModificar class=\"btn btn-xs btn-warning\" onclick=modificarPedido(" + auxPedidos[index].id + ")><i class=\"fas fa-edit\"></i>Modificar</button></td>\n            <td><button id=btnModificar class=\"btn btn-xs btn-warning\" onclick=finalizarPedido(" + auxPedidos[index].id + ")><i class=\"fas fa-edit\"></i>Finalizar</button></td>\n            </tr>"));
    }
}
/*
Aplca filtro por tipo de empleado según opción del combo seleccionada.
Accede al servidor y obtiene los datos de empleados mediante GET.
Obtiene el Token del localStorage.
*/
function filtrarAdminPedidos() {
    $.ajax({
        url: servidor + "pedido/",
        type: 'GET',
        headers: { "token": localStorage.getItem('token') },
        beforeSend: function () {
            $('#tCuerpo').html('<img src="IMG/5.gif">');
        }
        //Obtiene la opción de filtrado seleccionada y la convierte a string.
    }).then(function (aux) {
        var pedAux;
        var listaPedidos = [];
        var tipoFiltrado = String($("#cbFiltop").val());
        for (var index = 0; index < aux.length; index++) {
            pedAux = new restaurante.pedido(aux[index].id, aux[index].idComanda, aux[index].idProducto, aux[index].cantidad, aux[index].tipo, aux[index].monto, aux[index].horaEstimada, aux[index].horaFinal, aux[index].estado, aux[index].idEmpleado, aux[index].fecha);
            listaPedidos.push(pedAux);
        }
        /*Verifica que sea distinto a opción "Todos" y filtra por la opción seleccionada igualandola al
         tipo de empleado existente en cada objeto */
        if (tipoFiltrado != "todoss") {
            var pedidosFiltrados = [];
            pedidosFiltrados = aux.filter(function (ped) {
                return restaurante.tipoPedido[ped.tipo] === restaurante.tipoPedido[tipoFiltrado];
            });
            TSMostrarGrillaPedidosFiltrada(pedidosFiltrados);
            //console.log(empleadosFiltrados);
            //Si la opción es "Todos", llama a mostrar grilla enviando todos los datos recibidos mediante GET.
        }
        else {
            if (aux != false) {
                console.info("bien -->", aux);
                TSMostrarGrillaEmpleadosFiltrada(aux);
            }
            //caso de error de obtención de datos
            else {
                var msjVehiculo = $("#msjEmpleado").html("<h4 id='msjEmpleado'><kbd>" + aux + "</kbd></h2>");
                console.log(aux);
            }
        }
    }, function (error) {
        console.info("error", error);
    });
}
//Mustra los tipos de filtro y tabla 
function filtrosPedidos() {
    $("#sTablaEmpleados").show();
    $("#filtrosEmpleados").show();
    var filtrosPedidos = $("#filtrosEmpleados").html("");
    filtrosPedidos = $("#filtrosEmpleados").html("\n                    <h4 id=\"mensaje\"><kbd>Filtros</kbd></h4>\n                    <label for=\"cbFiltop\">Filtrar por</label>\n                    <select class=\"form-control\" id=\"cbFiltop\" name=\"cbFiltop\">\n                        <option value=\"todoss\">todos </option>\n                        <option value=\"cocina\">cocina </option>\n                        <option value=\"bar\">bar </option>\n                        <option value=\"cerveza\">cerveza </option>\n                    </select>\n                        \n                    <br>\n                    <br>\n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"idComanda\" id=\"chIDComanda\">ID Comanda</label>\n                    </div>\n                    \n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"horaEstimada\" id=\"chHoraEstimada\" checked>Hora Estimada</label>\n                    </div>\n\n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"horaFinal\" id=\"chHoraFinal\" checked>Hora Final</label>\n                    </div>\n\n                    <div class=\"checkbox-inline\">\n                        <label><input type=\"checkbox\" name=\"idEmpleado\" id=\"chIDEmpleado\" checked>ID Empleado</label>\n                    </div>\n\n                    <div class=\"checkbox-inline\">\n                    <label><input type=\"checkbox\" name=\"monto\" id=\"chMonto\" checked>Monto</label>\n                    </div>\n\n                    <div class=\"checkbox-inline\">\n                    <label><input type=\"checkbox\" name=\"fecha\" id=\"chFecha\" checked>Fecha</label>\n                    </div>\n                \n    ");
    console.log("termino!");
}
//FUNCIONES MAP
function TSidComanda(auxPedidos) {
    var chIDComanda = $("#chIDComanda");
    var lIDComanda;
    return auxPedidos.map(function (elemento) {
        if (chIDComanda.is(':checked')) {
            return lIDComanda = "<td>" + elemento.idComanda + "</td>";
        }
        else {
            return lIDComanda = "";
        }
    });
}
function TShoraEstimada(auxPedidos) {
    var chHoraEstimada = $("#chHoraEstimada");
    var lHoraEstimada;
    return auxPedidos.map(function (elemento) {
        if (chHoraEstimada.is(':checked')) {
            return lHoraEstimada = "<td>" + elemento.horaEstimada + "</td>";
        }
        else {
            return lHoraEstimada = "";
        }
    });
}
function TShoraFinal(auxPedidos) {
    var chHoraFinal = $("#chHoraFinal");
    var lHoraFinal;
    return auxPedidos.map(function (elemento) {
        if (chHoraFinal.is(':checked')) {
            return lHoraFinal = "<td>" + elemento.horaFinal + "</td>";
        }
        else {
            return lHoraFinal = "";
        }
    });
}
function TSidEmpleadop(auxPedidos) {
    var chIDEmpleadop = $("#chIDEmpleadop");
    var lidEmpleadop;
    return auxPedidos.map(function (elemento) {
        if (chIDEmpleadop.is(':checked')) {
            return lidEmpleadop = "<td>" + elemento.idEmpleado + "</td>";
        }
        else {
            return lidEmpleadop = "";
        }
    });
}
function TSMonto(auxPedidos) {
    var chMonto = $("#chMonto");
    var lMonto;
    return auxPedidos.map(function (elemento) {
        if (chMonto.is(':checked')) {
            return lMonto = "<td>" + elemento.monto + "</td>";
        }
        else {
            return lMonto = "";
        }
    });
}
function TSFecha(auxPedidos) {
    var chFecha = $("#chFecha");
    var lFecha;
    return auxPedidos.map(function (elemento) {
        if (chFecha.is(':checked')) {
            return lFecha = "<td>" + elemento.Fecha + "</td>";
        }
        else {
            return lFecha = "";
        }
    });
}
function modificarPedido(idPedido) {
    bootbox.confirm("¿Esta Seguro?", function (result) {
        if (result == true) {
            bootbox.prompt({
                title: "Ingrese hora estimada",
                inputType: 'time',
                callback: function (horaEstimada) {
                    console.log("horaEstimada:", horaEstimada);
                    var form_data = new FormData();
                    form_data.append('idPedido', idPedido);
                    //form_data.append('estadoCoc', "en preparación");
                    form_data.append('horaEstimada', horaEstimada);
                    console.log("form_data: ", form_data.get('idPedido'));
                    //console.log("form_data: ", form_data.get('estadoCoc'));
                    console.log("form_data: ", form_data.get('horaEstimada'));
                    $.ajax({
                        type: "POST",
                        url: servidor + "pedido/modificar/",
                        data: form_data,
                        headers: { "token": localStorage.getItem('token') },
                        contentType: false,
                        processData: false,
                        cache: false
                    }).then(function (retorno) {
                        console.log(retorno.responseText);
                        console.log(retorno);
                        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>Se tomo el pedido</kbd></h4>");
                        var modalError = $('#modalError').modal('show');
                        //TSMostrarGrillaPedidos();
                    }, function (error) {
                        //alert("error en cargarDatos. Contacte al administrador.");
                        console.log(error.responseText);
                        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-warning'>" + error.responseJSON + "</kbd></h4>");
                        var modalError = $('#modalError').modal('show');
                    });
                }
            });
        }
        else {
            var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-warning'>No se tomo el pedido</kbd></h4>");
            var modalError = $('#modalError').modal('show');
        }
    });
}
function finalizarPedido(idPedido) {
    var form_data = new FormData();
    form_data.append('idPedido', idPedido);
    $.ajax({
        type: "POST",
        url: servidor + "pedido/finalizar/",
        data: form_data,
        headers: { "token": localStorage.getItem('token') },
        contentType: false,
        processData: false,
        cache: false
    }).then(function (retorno) {
        console.log(retorno.responseText);
        console.log(retorno);
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-success'>El pedido esta listo</kbd></h4>");
        var modalError = $('#modalError').modal('show');
    }, function (error) {
        //alert("error en cargarDatos. Contacte al administrador.");
        console.log(error.responseText);
        var msjError = $("#msjError").html("<h4 id='msjError'><kbd class='label-warning'>" + error.responseJSON + "</kbd></h4>");
        var modalError = $('#modalError').modal('show');
    });
}
