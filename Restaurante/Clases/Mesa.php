<?php

class Mesa
{
    public $idMesa;
    public $estado;
    public $cantidad;

    #FUNCIONES DB ------------------------------------------------------------------------------------

    public function GuardarMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
        INSERT into Mesas (idMesa, estado, cantidad)
        VALUES(:idMesa, :estado, :cantidad)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function ModificarMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE Mesas 
        SET estado=:estado, cantidad=:cantidad WHERE idMesa=:idMesa");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
		$consulta->execute();
        return $consulta->rowCount();		
    }

    public function BorrarMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
		DELETE FROM Mesas WHERE idMesa=:idMesa");	
		$consulta->bindValue(':idMesa',$this->idMesa, PDO::PARAM_INT);		
		$consulta->execute();
		return $consulta->rowCount();
    }

    public static function TraerUnaMesa($idMesa)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * FROM Mesas WHERE idMesa=:idMesa");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();
        $mesa=$consulta->fetchObject("Mesa");
        
        if($mesa)
        {
            return $mesa;
        }
        else{
            throw new Exception("La mesa no existe");
        }
    }

    public static function TraerTodasLasMesas()
    {
        $objetoAccesoDato = AccesoDatos::dameunObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * from Mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function TraerMesaVacia()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * from Mesas WHERE estado='vacia'");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        $mesa=$consulta->fetchObject("Mesa");
        if($mesa)
        {
            return $mesa;
        }
        else
        {
            throw new Exception("No hay mesas disponibles");
        }
    }

    public static function TraerMasUtilizada()
    {//ver si la query esta bien
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * FROM Mesas WHERE cantidad=(SELECT MAX(cantidad) FROM Mesas)");
        $consulta->execute();
        $cochera=$consulta->fetchObject("Mesa");
    
        return $mesa;
    }

    public static function MesaSinUsar()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * from Mesas WHERE cantidad=0");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
        if($mesa)
        {
            return $mesa;
        }
        else
        {
            throw new Exception("No hay mesas sin usar");
        }
    }
    
    public static function TraerMenosUtilizada()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * FROM Mesas 
        WHERE canUsos=(SELECT MIN(cantidad) FROM mesas WHERE cantidad != 0) ");
        $consulta->execute();
        $mesa=$consulta->fetchObject("Mesa");
        return $mesa;
    }

  //FALTA FACTURACIÓN

}
