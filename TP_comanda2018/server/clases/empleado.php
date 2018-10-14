<?php
//include_once "AccesoDatos.php";
require_once 'AccesoDatos.php';
class empleado
{
    public $id;
    public $email;
    public $clave;
    public $usuario;
    public $tipo;
    public $estado;
    public $foto;
    

    
    public function InsertarEmpleadoParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into empleado (email,clave,usuario,tipo,estado,foto)values(:email,:clave,:usuario,:tipo,:estado,foto)");
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':usuario',$this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
            //$consulta->bindValue(':alta', $this->alta, PDO::PARAM_STR);
            $consulta->execute();	
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
			//return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");	
    }

    public static function TraerTodoLosEmpleadosSuspendidos()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from empleado where estado='suspendido'");
            $consulta->execute();
            if($consulta->rowCount() == 0){
                return false;   
            }		
			return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");		
    }
    public static function TraerTodoLosEmpleados()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from empleado");
            $consulta->execute();	
            if($consulta->rowCount() == 0){
                return false;   
            }			
			return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");		
    }

    public static function TraerEmpleadoEmail($email) 
	{//email,clave,nombre,tipo,estado
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select id,email,clave,usuario,tipo,estado from empleado where email = '$email'");
			$consulta->execute();
            $EmpAux= $consulta->fetchObject('empleado');
            if($consulta->rowCount() == 0){
                return false;   
            }
			return $EmpAux;		
    }

    public static function TraerEmpleadoEmailClave($email,$clave){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,usuario,sexo,email,turno,perfil,foto,alta,estado FROM empleado WHERE email=:email AND clave=:clave");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
		$consulta->setFetchMode(PDO::FETCH_CLASS, "empleado");
		$consulta->execute();
        return $consulta->fetchObject('empleado');
    }

    public static function TraerEmpleadoEmailClave2($email,$clave){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,usuario,sexo,email,turno,perfil,foto,alta,estado FROM empleado WHERE email=:email AND clave=:clave");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
		$EmpAux= $consulta->fetchObject('empleado');
            if($consulta->rowCount() == 0){
                return false;   
            }
		return $EmpAux;	
	}
    
    public static function TraerEmpleadoID($id) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from empleado where id = '$id'");
			$consulta->execute();
            $EmpAux= $consulta->fetchObject('empleado');
            if($consulta->rowCount() == 0){
                return false;   
            }
			return $EmpAux;		
    }
    
    public static function TraerEmail($auxEmail) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select email from empleado where email = '$auxEmail'");
			$consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            if($consulta->rowCount() == 0){
                return false;   
            }
            return $consulta->fetchAll();	
	}

   
    public static function BorrarEmpleadoID($id)
    {
       $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
       $consulta =$objetoAccesoDato->RetornarConsulta("
           delete 
           from empleado 				
           WHERE id=:id");	
       $consulta->bindValue(':id',$id, PDO::PARAM_INT);		
       $consulta->execute();
       return $consulta->rowCount();
    }

    public function ModificarEmpleadoID($auxID)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE empleado set usuario=:usuario,email=:email,clave=:clave,tipo=:tipo,estado=:estado WHERE id=$auxID");
            //$consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
    }

    public static function CambiarEstadoEmpleadoParametros($auxID,$auxEST)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE empleado set estado=:estado where id=$auxID");
        $consulta->bindValue(':estado', $auxEST, PDO::PARAM_STR);
        return $consulta->execute();

    }


    public static function operacionesUsuarioEntrada($auxID){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT count(*) as Num FROM estacionados WHERE idEmpleadoIngreso = $auxID");
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetch();
    }

    
    public static function operacionesUsuarioFecha($auxID,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($hasta == ""&& $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) as Num FROM pedidos WHERE idEmpleado = $auxID AND fecha >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) as Num FROM pedidos WHERE idEmpleado = $auxID AND fecha <=:hasta ");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) as Num FROM estacionados WHERE idEmpleado = $auxID AND fecha BETWEEN :desde AND :hasta ");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            return empleado::operacionesUsuarioEntrada($auxID);
        }  
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetch();
	}
    
    public static function operacionesUsuarioSalida($auxID){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
		$consulta = $objetoAccesoDato->RetornarConsulta("SELECT count(*) as Num FROM estacionados WHERE idEmpleadoSalida = $auxID");
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetch();
		/*
		if($consulta->rowCount() == 0){
			return false;   
		}
        return $consulta->fetchAll();
        */
    }
    public static function operacionesUsuarioSalidaFecha($auxID,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($hasta == ""&& $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) as Num FROM estacionados WHERE idEmpleadoSalida = $auxID AND fechaHoraSalida >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) as Num FROM estacionados WHERE idEmpleadoSalida = $auxID AND fechaHoraSalida <=:hasta ");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) as Num FROM estacionados WHERE idEmpleadoSalida = $auxID AND fechaHoraSalida BETWEEN :desde AND :hasta ");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            return empleado::operacionesUsuarioSalida($auxID);
        }  
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
	}



}
?>