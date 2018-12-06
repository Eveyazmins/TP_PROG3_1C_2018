var restaurante;
(function (restaurante) {
    var tipoEmpleado;
    (function (tipoEmpleado) {
        tipoEmpleado["cocinero"] = "cocinero";
        tipoEmpleado["bartender"] = "bartender";
        tipoEmpleado["mozo"] = "mozo";
        tipoEmpleado["cervecero"] = "cervecero";
        tipoEmpleado["socio"] = "socio";
    })(tipoEmpleado = restaurante.tipoEmpleado || (restaurante.tipoEmpleado = {}));
    var tipoEstado;
    (function (tipoEstado) {
        tipoEstado["suspendido"] = "suspendido";
        tipoEstado["activo"] = "activo";
    })(tipoEstado = restaurante.tipoEstado || (restaurante.tipoEstado = {}));
    var tipoTurno;
    (function (tipoTurno) {
        tipoTurno["ma\u00F1ana"] = "ma\u00F1ana";
        tipoTurno["tarde"] = "tarde";
        tipoTurno["noche"] = "noche";
    })(tipoTurno = restaurante.tipoTurno || (restaurante.tipoTurno = {}));
    var tipoPerfil;
    (function (tipoPerfil) {
        tipoPerfil["admin"] = "admin";
        tipoPerfil["user"] = "user";
    })(tipoPerfil = restaurante.tipoPerfil || (restaurante.tipoPerfil = {}));
})(restaurante || (restaurante = {}));
