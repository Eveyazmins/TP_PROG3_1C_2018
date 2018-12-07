namespace restaurante
{

    export class pedido  {

        public id:number;
        public idComanda:number;
        public idProducto:number;
        public cantidad:number;
        public tipo:string;
        public monto:number;
        public horaEstimada:string;
        public horaFinal:string;
        public estado:string;
        public idEmpleado:number;
        public fecha:string;

        
        constructor(id:number,idComanda:number,idProducto:number,cantidad:number,tipo:string,monto:number, horaEstimada:string, horaFinal:string,estado:string,idEmpleado:number, fecha:string) 
        {
            this id = id;
            this idComanda = idComanda;
            this idProducto = idProducto;
            this cantidad = cantidad;
            this tipo = tipo;
            this monto = monto;
            this horaEstimada = horaEstimada;
            this horaFinal = horaFinal;
            this estado = estado;
            this idEmpleado = idEmpleado;
            this fecha = fecha;
        }

    }
    
}