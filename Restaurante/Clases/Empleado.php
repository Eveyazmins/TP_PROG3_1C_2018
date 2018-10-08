<?php

class Empleado
{
        #ATRIBUTOS-----------------------------------------------------------------------------------
        public $id_empleado;
        public $usuario;
        public $clave;
        public $sector;
        public $perfil;
        public $estado;
    

        #FUNCIONES DB ---------------------------------------------------------------------------------


        //ALTA EMPLEADO

        public function InsertarEmpleado()
        {   
           $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
           $consulta =$objetoAccesoDato->RetornarConsulta("
           INSERT INTO Empleados (usuario, clave, sector, perfil, estado) 
           VALUES(:usuario, :clave, :sector, :perfil, :estado)");
   
           $consulta->bindValue(':usuario',$this->usuario, PDO::PARAM_STR);
           $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
           $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
           $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
           $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
           $consulta->execute();
   
            return $objetoAccesoDato->RetornarUltimoIdInsertado();			
        }

        //BAJA EMPLEADO

        public function BorrarEmpleado()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            DELETE from Empleados WHERE id_empleado=:id");
            
            $consulta->bindValue(':id',$this->id_empleado, PDO::PARAM_INT);		
            $consulta->execute();
            
            return $consulta->rowCount();
        }

        //MODIFICAR DATOS EMPLEADO

        public function ModificarEmpleado()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            UPDATE Empleados 
            SET 
            usuario='$this->usuario',
            clave='$this->clave',
            perfil='$this->perfil',
            sector='$this->sector'
            WHERE id_empleado='$this->id_empleado'");
                   
            return $consulta->execute();
        }

        //SUSPENDER EMPLEADO

        public function SuspenderEmpleado()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("
            UPDATE Empleados 
            SET estado='suspendido' WHERE id_empleado='$this->id_empleado'");

            return $consulta->execute();
        }

        //REANUDAR EMPLEADO

        public function ReanudarEmpleado()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("
            UPDATE Empleados 
            SET estado='activo' WHERE id_empleado=:id");
            $consulta->bindValue(':id',$id_empleado, PDO::PARAM_INT);
            $consulta->execute();

            return "Activo";
        }

        //CANTIDAD OPERACIONES POR EMPLEADO

        public static function CantidadOperacionesEmpleado($id_empleado)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * FROM Operaciones WHERE id=:id");
            $consulta->bindValue(':id', $id_empleado, PDO::PARAM_INT);
            $consulta->execute();
            
            return $consulta->rowCount();
        }

        //CANTIDAD OPERACIONES POR SECTOR

        public static function CantidadOperacionesSector($sector)
        {
            $objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
            $consulta=$objetoAccesoDato->RetornarConsulta("
            SELECT * from Pedidodetalle as pd 
            WHERE pd.sector=:sector");  
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
            $consulta->execute();

            return $consulta->rowCount();
        }

        //CANTIDAD OPERACIONES POR SECTOR Y EMPLEADO

        public static function CantidadOperacionesEmpleadoSector($id_empleado, $sector)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * FROM Operaciones WHERE idEmpleado=:id and sector=:sector");
            $consulta->bindValue(':id', $id_empleado, PDO::PARAM_INT);
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
            $consulta->execute();
            
            return $consulta->rowCount();
        }

        //VALIDAR LOGIN

        public static function ValidarLogIn($usuario, $clave) 
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT  * from Empleados WHERE usuario=:usuario and clave=:clave");
            
            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
            $consulta->execute();
            $empleado_b= $consulta->fetchObject('Empleado');			
    
            return $empleado_b;      
        }

        //INGRESO Y EGRESO DEL SISTEMA

        public static function SesionesEmpleados()
        {
            $objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
            $consulta=$objetoAccesoDato->RetornarConsulta("
            SELECT e.usuario, s.horaInicio, s.horaFin 
            from Empleados as e, Sesiones as s 
            where s.id_empleado=e.id");

            $consulta->execute();
            $sesiones= $consulta->fetchAll(PDO::FETCH_CLASS);
            return $sesiones;
        }


        //OTRAS FUNCIONES

        public static function TraerTodosEmpleados()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * FROM Empleados");
            $consulta->execute();			
                
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");		
        }


        public static function TraerUnEmpleado($id_empleado) 
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * from Empleados where id = $id_empleado");
            $consulta->execute();
            $empleado_b= $consulta->fetchObject('Empleado');
                
            return $empleado;				
        }


         //METODO CON PARÁMETROS POR POSTMAN (NO LO USO)
         public function ModificarEmpleadoParametros()
         {
             $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
             $consulta =$objetoAccesoDato->RetornarConsulta("
             UPDATE Empleados 
             SET
             usuario=:usuario,
             clave=:clave,
             perfil=:perfil,
             sector=:sector
             WHERE id=:id");
             
             $consulta->bindValue(':id',$this->id_empleado, PDO::PARAM_INT);
             $consulta->bindValue(':usuario',$this->usuario, PDO::PARAM_STR);
             $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
             $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
             $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
                
             return $consulta->execute();
         }     

}