namespace restaurante
{

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
        let filtrosLogin = $("#filtrosLogin").html("");
        filtrosLogin = $("#filtrosLogin").append(`
                        <br>
                        <br>
                        <h4 id="mensaje"><kbd>Filtros</kbd></h4>
                    
                        <div class="input-group">
                            <label>Filtrar por</label>
                            <label for="cbFiltoLoginEmpleado"> Usuario</label>
                            <select class="form-control" id="cbFiltoLoginEmpleado" name="cbFiltoLoginEmpleado">
                            </select>
            
                        </div> 
                        <p>
                            <div class="form-group">
                                <label> Fecha</label>
                                <br>
                                <label> Desde</label>
                                <input type="date" class="form-control" id="fechaDesde" name="fechaDesde">
                                <label> Hasta</label>
                                <input type="date" class="form-control" id="fechaHasta" name="fechaHasta">
                                <input type="button" class="btn btn-success" value="Filtrar" id="btnFiltrarLoginFecha" onclick="TSMostrarGrillaLogin()" >
                            </div>  
                        </p>
    
                        
                        `);
    
        usuariosDisponibles();
        let cbFiltoLoginEmpleado = $("#cbFiltoLoginEmpleado")
        cbFiltoLoginEmpleado.change(TSMostrarGrillaLogin);
        TSMostrarGrillaLogin();
    
    }
    
    function TSMostrarGrillaLogin() {
        let fechaDesde: any = String($("#fechaDesde").val());
        let fechaHasta: any = String($("#fechaHasta").val());
        
        console.log("fechaDesde: ",fechaDesde);
        console.log("fechaHasta: ",fechaHasta);
        //console.log("email :",emailFiltrado);
        
        $.ajax({
            url: servidor + "listados/empleados/login/",
            type: 'GET',
            headers: { "token": localStorage.getItem('token') },
            beforeSend: function () {
                let tCuerpo = $("#tCuerpo");
                let tCabeza = $("#tCabeza");
                tCabeza.html("");
                tCuerpo.html("");
                $('#tCuerpo').html('<img src="IMG/5.gif">')
            }
        }).then(function (listaPendientes) {
            console.log(listaPendientes);
            $("#sTablaEmpleados").show();
            let emailFiltrado: any = String($("#cbFiltoLoginEmpleado").val());
            console.log("Login: ", listaPendientes);
            //let empleadosFiltrados = listaPendientes;
    
            if (emailFiltrado != "todos") {
                
                listaPendientes = listaPendientes.filter(function (emp: any) {
                    return emp.idEmpleado === emailFiltrado;
                });
            }
    
            if (fechaDesde != "" ) {
                listaPendientes = listaPendientes.filter(function (emp: any) {
                    return emp.fecha >= fechaDesde;
                });
            }
            if (fechaHasta != "" ) {
                listaPendientes = listaPendientes.filter(function (emp: any) {
                    return emp.fecha <= fechaHasta;
                });
            }
            if (fechaDesde != "" && fechaHasta != ""  ) {
                listaPendientes = listaPendientes.filter(function (emp: any) {
                    return emp.fecha >= fechaDesde && emp.fecha <= fechaHasta;
                });
            }
    
            let tCuerpo = $("#tCuerpo");
            let tCabeza = $("#tCabeza");
            tCabeza.html("");
            tCuerpo.html("");
            let msjVehiculo = $("#msjVehiculo").html("br<h4 id='msjVehiculo'><kbd>Pendientes </kbd></h2>");
            tCabeza.append("<tr class='success'>" +
                "<th> ID</th>" +
                "<th> Empleado</th>" +
                "<th> Fecha</th>" +
                "<th> Hora</th>" +
                "</tr>")
            if (tipo == "socio") {
                let msjVehiculo = $("#msjVehiculo").html("<h4 id='msjVehiculo'><kbd>Listado de login </kbd></h2>");
                for (var index = 0; index < listaPendientes.length; index++) {
                    tCuerpo.append(
                        `
                            <tr>
                            <td> ${listaPendientes[index].id}</td>
                            <td> ${listaPendientes[index].idEmpleado}</td>
                            <td>${listaPendientes[index].fecha}</td>
                            <td>${listaPendientes[index].hora}</td>
                            </tr>`);
                }
            }
    
        }, function (error) {
            console.info("error mostrar grilla", error);
            let msjError = $("#msjError").html("<h4 id='msjError'><kbd class= label-danger>no tiene permisos para realizar esta tarea</kbd></h4>");
            let modalError = $('#modalError').modal('show');
        });
        
    }
    
    function usuariosDisponibles() {
        $.ajax({
            type: "GET",
            url: servidor + "empleado/",
            headers: { "token": localStorage.getItem('token') },
        })
            .then(function (retorno) {
                console.info("bien:", retorno);
    
                let cbFiltoLoginEmpleado = $("#cbFiltoLoginEmpleado");
                cbFiltoLoginEmpleado.html("");
                cbFiltoLoginEmpleado.html(`
                <option value="todos">Todos</option>
        
                `);
                for (var index = 0; index < retorno.length; index++) {
                    cbFiltoLoginEmpleado.append(`
                    <option value="${retorno[index].email}">${retorno[index].email}</option>
                    `);
    
                }
                //return retorno;
            }
    
    
    }
    
}
    



}
