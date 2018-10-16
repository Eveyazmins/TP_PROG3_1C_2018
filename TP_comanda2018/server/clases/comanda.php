<?php
include_once "AccesoDatos.php";

class comanda
{
    public $id;
    public $idMesa;
    public $monto;
    public $estado;
    public $foto;
    public $cliente;
    public $fecha;
    public $idMozo;


    /*ABRIR COMANDA
    Bajo este ID de comanda se van a cargar todos los pedidos y se va a calcular el precio final.  
    */
    public function InsertarComandaParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into comanda (idMesa,monto,estado,foto,cliente,fecha,idMozo)values(:idMesa,:monto,:estado,:foto,:cliente,:fecha,:idMozo)");
            $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
            $consulta->bindValue(':monto', $this->monto, PDO::PARAM_STR);
            $consulta->bindValue(':estado',$this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
            $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
            $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
            $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
            $consulta->execute();	
            return $objetoAccesoDato->RetornarUltimoIdInsertado();	
    }

    public static function CargarPrecioComanda($auxId, $monto)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE comanda set monto=:monto WHERE id=$auxId");
        $consulta->bindValue(':monto', $monto, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
        
    }

    public static function RetornarPrecioComanda($auxId)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT monto from comanda where id=$auxId");
        
        $consulta->execute();
        return $consulta->fetch();
    }

    public static function cerrarComandaParametros($auxId)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE comanda set estado=:estado where id=$auxId");
        $consulta->bindValue(':estado', "cerrado", PDO::PARAM_STR);        
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function TraerTodasLasComandas()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from comanda");
            $consulta->execute();	
            if($consulta->rowCount() == 0){
                return false;   
            }			
			return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");		
    }

    /*
    public static function TraerTodasLasComandasEstado($estado)
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from comanda where estado=:estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();	
        if($consulta->rowCount() == 0)
        {
            return false;   
        }			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");		
    }

    */

    public static function TraerTodosPedidosComanda($auxId)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos2 where idComanda=:idComanda");
        $consulta->bindValue(':idComanda', $auxId, PDO::PARAM_INT);
        $consulta->execute();	
        if($consulta->rowCount() == 0)
        {
            return false;   
        }			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");
    }

     
    public static function TraerComandaID($id) 
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("select * from comanda where id = '$id'");
		$consulta->execute();
        $comandaAux= $consulta->fetchObject('comanda');
        if($consulta->rowCount() == 0)
        {
            return false;   
        }
		return $comandaAux;		
    }


    public static function TraerMesaMasFacturoParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT co.idMesa, sum(co.monto) total FROM comanda as co GROUP by co.idMesa ORDER BY total DESC LIMIT 1");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);
        
        return $mesa;
    }

    public static function TraerMesaMenosFacturoParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT co.idmesa, sum(co.monto) total FROM comanda as co GROUP by co.idmesa ORDER BY total ASC LIMIT 1");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);

        return $mesa;
    }

    public static function TraerMesaMenorImporte()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT co.idmesa, co.monto from comanda as co where co.monto = (SELECT MIN(monto) from comanda)");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);

        return $mesa;
    }

    public static function TraerMesaMayorImporte()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT co.idmesa, co.monto from comanda as co where co.monto = (SELECT MAX(monto) from comanda)");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);

        return $mesa;
    }


    public static function FacturadoDesdeHasta($idMesa, $desde, $hasta)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        if ($hasta == ""&& $desde !="") 
        {
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT SUM(co.monto) total FROM comanda as co WHERE co.idMesa= :idMesa AND co.fecha >= :desde");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") 
        {
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT SUM(co.monto) total FROM comanda as co WHERE co.idMesa= :idMesa AND co.fecha <= :hasta");
            $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
            $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") 
        {
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT SUM(co.monto) total FROM comanda as co WHERE co.idMesa= :idMesa AND co.fecha BETWEEN :desde AND :hasta");
            $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
            $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
            $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        }

        if ($desde =="" && $hasta =="") 
        {
            $consulta = $objetoAccesoDato->RetornarConsulta("SELECT SUM(co.monto) as total FROM comanda as co WHERE co.idMesa= :idMesa");
            $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        }

        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
    }

 









}