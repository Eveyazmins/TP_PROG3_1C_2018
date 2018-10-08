<?php

include_once "AccesoDatos.php";

class Sesion
{
    public $id_sesion;
    public $id_Empleado;
    public $hora_inicio;
    public $hora_final;


    //Sin parámetros ya que obtengo los datos de la validación de usuario y clave sobre el objeto Empleado.

    public function IniciarSesion()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
        INSERT into Sesiones (id_empleado,hora_inicio)
        VALUES(:id_empleado,:hora_inicio)");

		$consulta->bindValue(':id_empleado',$this->id_empleado, PDO::PARAM_INT);
		$consulta->bindValue(':hora_inicio',$this->hora_inicio, PDO::PARAM_STR);
        $consulta->execute();		
        
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    //Con parámetros ya que no tengo objeto Empleado ni requiere validación previa

    public static function CerrarSesion($id,$horafinal)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE Sesiones 
        SET horaFinal=:horaFinal WHERE id=:id");
        
        $consulta->bindValue(':horaFinal',$horafinal, PDO::PARAM_STR);
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $cantidadFilas=$consulta->execute();
        
        if($cantidadFilas > 0)
        {
            return true;
        }
        else
        {
            throw new Exception("No se pudo cerrar la sesion!!!");
        }
    }


}
?>