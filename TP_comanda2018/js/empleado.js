"use strict";
var restaurante;
(function (restaurante) {
    var empleado = /** @class */ (function () {
        function empleado(id, email, clave, usuario, tipo, estado) {
            this.id = id;
            this.email = email;
            this.clave = clave;
            this.usuario = usuario;
            this.tipo = tipo;
            this.estado = estado;
        }
        return empleado;
    }());
    restaurante.empleado = empleado;
})(restaurante || (restaurante = {}));
