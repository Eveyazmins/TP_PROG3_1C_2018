<?php

class Empleado
{
        #ATRIBUTOS-----------------------------------------------------------------------------------
        public $id;
        public $usuario;
        public $clave;
        public $sector;
        public $perfil;
        public $estado;
    

        #FUNCIONES DB ---------------------------------------------------------------------------------

        public function InsertarEmpleado()
        {
            
           $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
           $consulta =$objetoAccesoDato->RetornarConsulta("
           INSERT INTO Empleados (usuario, clave, sector, perfil, estado) 
           VALUES(:usuario, :clave, :sector, :perfil, :estado)");
   
           $consulta->bindValue(':usuario',$this->usuario, PDO::PARAM_STR);
           $consulta->bindValue(':clave', $this->password, PDO::PARAM_STR);
           $consulta->bindValue(':sector', $this->tipo, PDO::PARAM_STR);
           $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
           $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
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

        public static function CantOperacionesEmpleado($id)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("
            SELECT * FROM Operaciones WHERE idEmpleado=:id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            
            return $consulta->rowCount();
        }

        public static function SesionesEmpleados()
        {
            $objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
            $consulta=$objetoAccesoDato->RetornarConsulta("
            SELECT e.usuario, s.horaInicio 
            from empleados as e, sesiones as s 
            where s.idEmpleado=e.id 
            ORDER by e.usuario");
            $consulta->execute();
            $sesiones= $consulta->fetchAll(PDO::FETCH_CLASS);
            return $sesiones;
        }

        
        public static function CantidadOperacionesunSector($sector)
        {
            var_dump($sector);
            $objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
            $consulta=$objetoAccesoDato->RetornarConsulta("SELECT e.usuario, COUNT(*) as operaciones FROM empleados as e, pedidodetalle as pd WHERE pd.idEmpleado= e.id and pd.sector=:sector GROUP by e.usuario");
            $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
            
            $consulta->execute();
            $operaciones= $consulta->fetchAll(PDO::FETCH_CLASS);
            return $operaciones;
            
        }

        public static function CantidadOperacionesTodosSectores()
        {
            $objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
            $consulta=$objetoAccesoDato->RetornarConsulta("
            SELECT sector as sector, COUNT(*) as operaciones 
            FROM pedidodetalle 
            GROUP by sector");
            $consulta->execute();
            $operaciones= $consulta->fetchAll(PDO::FETCH_CLASS);
            return $operaciones;
        }
    
        public static function CantidadOperacionesUnEmpleado($idEmpleado)
        {
            $objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
            $consulta=$objetoAccesoDato->RetornarConsulta("SELECT e.usuario, COUNT(*) as operaciones from empleados as e, pedidodetalle as pd where pd.idEmpleado in (SELECT e.id from empleados WHERE e.id= :idEmpleado)");
            $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
            
            $consulta->execute();
            $operaciones= $consulta->fetchAll(PDO::FETCH_CLASS);
            return $operaciones;
            
        }
        public static function CantidadOperacionesTodosEmpleados()
        {
            $objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
            $consulta=$objetoAccesoDato->RetornarConsulta("
            SELECT e.usuario as empleado, COUNT(*) as operaciones 
            FROM empleados as e, pedidodetalle as pd 
            WHERE pd.idEmpleado= e.id 
            GROUP by e.usuario");
            $consulta->execute();
            $operaciones= $consulta->fetchAll(PDO::FETCH_CLASS);
            return $operaciones;   
        }

}