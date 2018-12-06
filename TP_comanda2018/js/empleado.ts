namespace restaurante
{

    export class empleado  {

        public id:number;
        public email:string;
        public clave:string;
        public usuario:string;
        public tipo:tipoEmpleado;
        public estado:string;


        constructor(id:number,email:string,clave:string,usuario:string,tipo:tipoEmpleado,estado:string) 
        {
            this.id = id;
            this.email = email;
            this.clave = clave;
            this.usuario = usuario;
            this.tipo = tipo;
            this.estado = estado;
        }

    }
    
}