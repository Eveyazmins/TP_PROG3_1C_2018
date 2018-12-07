var restaurante;
(function (restaurante) {
    $(function () {
        var btnListadoLogin = $("#btnListadoLogin");
        btnListadoLogin.click(mostrarLogin);
    });
    function mostrarLogin() {
        $('.navbar-collapse').collapse('hide');
        $("#filtrosEmpleados").hide();
        $("#ABMemplados").hide();
        $("#sTablaEmpleados").hide();
        $("#Hola").hide();
        $("#filtrosPendientes").hide();
        $("#filtrosLogin").hide();
        $("#filtrosNegativos").hide();
        $("#MinMaxOperaciones").hide();
        $("#filtrosCancelados").hide();
        $("#filtrosOperaciones").hide();
        $("#usoMesas").hide();
        $("#filtrosMesas").hide();
        $("#filtrosLogin").show();
        var filtrosLogin = $("#filtrosLogin").html("");
        filtrosLogin = $("#filtrosLogin").append("\n                        <br>\n                        <br>\n                        <h4 id=\"mensaje\"><kbd>Filtros</kbd></h4>\n                    \n                        <div class=\"input-group\">\n                            <label>Filtrar por</label>\n                            <label for=\"cbFiltoLoginEmpleado\"> Usuario</label>\n                            <select class=\"form-control\" id=\"cbFiltoLoginEmpleado\" name=\"cbFiltoLoginEmpleado\">\n                            </select>\n            \n                        </div> \n                        <p>\n                            <div class=\"form-group\">\n                                <label> Fecha</label>\n                                <br>\n                                <label> Desde</label>\n                                <input type=\"date\" class=\"form-control\" id=\"fechaDesde\" name=\"fechaDesde\">\n                                <label> Hasta</label>\n                                <input type=\"date\" class=\"form-control\" id=\"fechaHasta\" name=\"fechaHasta\">\n                                <input type=\"button\" class=\"btn btn-success\" value=\"Filtrar\" id=\"btnFiltrarLoginFecha\" onclick=\"TSMostrarGrillaLogin()\" >\n                            </div>  \n                        </p>\n    \n                        \n                        ");
        usuariosDisponibles();
        var cbFiltoLoginEmpleado = $("#cbFiltoLoginEmpleado");
        cbFiltoLoginEmpleado.change(TSMostrarGrillaLogin);
        TSMostrarGrillaLogin();
    }
    function TSMostrarGrillaLogin() {
        var fechaDesde = String($("#fechaDesde").val());
        var fechaHasta = String($("#fechaHasta").val());
        console.log("fechaDesde: ", fechaDesde);
        console.log("fechaHasta: ", fechaHasta);
        //console.log("email :",emailFiltrado);
        $.ajax({
            url: servidor + "listados/empleados/login/",
            type: 'GET',
            headers: { "token": localStorage.getItem('token') },
            beforeSend: function () {
                var tCuerpo = $("#tCuerpo");
                var tCabeza = $("#tCabeza");
                tCabeza.html("");
                tCuerpo.html("");
                $('#tCuerpo').html('<img src="IMG/5.gif">');
            }
        }).then(function (listaPendientes) {
            console.log(listaPendientes);
            $("#sTablaEmpleados").show();
            var emailFiltrado = String($("#cbFiltoLoginEmpleado").val());
            console.log("Login: ", listaPendientes);
            //let empleadosFiltrados = listaPendientes;
            if (emailFiltrado != "todos") {
                listaPendientes = listaPendientes.filter(function (emp) {
                    return emp.idEmpleado === emailFiltrado;
                });
            }
            if (fechaDesde != "") {
                listaPendientes = listaPendientes.filter(function (emp) {
                    return emp.fecha >= fechaDesde;
                });
            }
            if (fechaHasta != "") {
                listaPendientes = listaPendientes.filter(function (emp) {
                    return emp.fecha <= fechaHasta;
                });
            }
            if (fechaDesde != "" && fechaHasta != "") {
                listaPendientes = listaPendientes.filter(function (emp) {
                    return emp.fecha >= fechaDesde && emp.fecha <= fechaHasta;
                });
            }
            var tCuerpo = $("#tCuerpo");
            var tCabeza = $("#tCabeza");
            tCabeza.html("");
            tCuerpo.html("");
            var msjVehiculo = $("#msjVehiculo").html("br<h4 id='msjVehiculo'><kbd>Pendientes </kbd></h2>");
            tCabeza.append("<tr class='success'>" +
                "<th> ID</th>" +
                "<th> Empleado</th>" +
                "<th> Fecha</th>" +
                "<th> Hora</th>" +
                "</tr>");
            if (tipo == "socio") {
                var msjVehiculo_1 = $("#msjVehiculo").html("<h4 id='msjVehiculo'><kbd>Listado de login </kbd></h2>");
                for (var index = 0; index < listaPendientes.length; index++) {
                    tCuerpo.append("\n                            <tr>\n                            <td> " + listaPendientes[index].id + "</td>\n                            <td> " + listaPendientes[index].idEmpleado + "</td>\n                            <td>" + listaPendientes[index].fecha + "</td>\n                            <td>" + listaPendientes[index].hora + "</td>\n                            </tr>");
                }
            }
        }, function (error) {
            console.info("error mostrar grilla", error);
            var msjError = $("#msjError").html("<h4 id='msjError'><kbd class= label-danger>no tiene permisos para realizar esta tarea</kbd></h4>");
            var modalError = $('#modalError').modal('show');
        });
    }
    function usuariosDisponibles() {
        $.ajax({
            type: "GET",
            url: servidor + "empleado/",
            headers: { "token": localStorage.getItem('token') }
        })
            .then(function (retorno) {
            console.info("bien:", retorno);
            var cbFiltoLoginEmpleado = $("#cbFiltoLoginEmpleado");
            cbFiltoLoginEmpleado.html("");
            cbFiltoLoginEmpleado.html("\n                <option value=\"todos\">Todos</option>\n        \n                ");
            for (var index = 0; index < retorno.length; index++) {
                cbFiltoLoginEmpleado.append("\n                    <option value=\"" + retorno[index].email + "\">" + retorno[index].email + "</option>\n                    ");
            }
            //return retorno;
        });
    }
})(restaurante || (restaurante = {}));
