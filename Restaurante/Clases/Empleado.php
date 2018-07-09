<?php

class Empleado
{
        #ATRIBUTOS-----------------------------------------------------------------------------------
        public $_id;
        public $_usuario;
        public $_clave;
        public $_perfil;
        public $_sector;
        public $_estado;
    

        #FUNCIONES DB ---------------------------------------------------------------------------------

        public function InsertarEmpleado()
        {
            
           $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
           $consulta =$objetoAccesoDato->RetornarConsulta("
           INSERT INTO Empleados (usuario, clave, sector, perfil, estado) 
           VALUES(:usuario, :clave, :sector, :perfil, :estado)");
   
           $consulta->bindValue(':usuario',$this->_usuario, PDO::PARAM_STR);
           $consulta->bindValue(':clave', $this->_password, PDO::PARAM_STR);
           $consulta->bindValue(':sector', $this->_sector, PDO::PARAM_STR);
           $consulta->bindValue(':perfil', $this->_perfil, PDO::PARAM_STR);
           $consulta->bindValue(':estado', $this->_estado, PDO::PARAM_STR);
           $consulta->execute();
   
            return $objetoAccesoDato->RetornarUltimoIdInsertado();			
        }

        public function BorrarEmpleado()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            DELETE from Empleados WHERE id=:id");
            
            $consulta->bindValue(':id',$this->id, PDO::PARAM_INT);		
            $consulta->execute();
            
            return $consulta->rowCount();
        }

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
            WHERE id='$this->id'");
                   
            return $consulta->execute();
   
        }


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
            
            $consulta->bindValue(':id',$this->id, PDO::PARAM_INT);
            $consulta->bindValue(':usuario',$this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
            $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
               
            return $consulta->execute();
        }


        public static function TraerTodosEmpleados()
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * FROM Empleados");
            $consulta->execute();			
                
            return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");		
        }


        public static function TraerUnEmpleado($id) 
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * from Empleados where id = $id");
            $consulta->execute();
            $empleadoBuscado= $consulta->fetchObject('Empleado');
                
            return $empleadoBuscado;				
        }


        public static function ValidarEmpleado($usuario, $clave) 
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT  * from Empleados WHERE usuario=:usuario and clave=:clave");
            
            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
            $consulta->execute();
            $busqueda= $consulta->fetchObject('Empleado');			
    
            return $busqueda;      
        }


        public static function CambiarEstadoEmpleado($id, $estado)
        {
            if($estado=="activo")
            {
                $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
                $consulta =$objetoAccesoDato->RetornarConsulta("
                UPDATE Empleados 
                SET estado='suspendido' WHERE id=:id");
                $consulta->bindValue(':id',$id, PDO::PARAM_INT);
   
                $consulta->execute();
                return "Suspendido";
   
            }
            else
            {
               $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
               $consulta =$objetoAccesoDato->RetornarConsulta("
               UPDATE Empleados
               SET estado='activo' WHERE id=:id");
               $consulta->bindValue(':id',$id, PDO::PARAM_INT);
   
                $consulta->execute();
                return "activo";
            }
   
        }

        public static function CantidadOperacionesEmpleado($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * FROM Operaciones WHERE idEmpleado=:id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            
            return $consulta->rowCount();
        }


}