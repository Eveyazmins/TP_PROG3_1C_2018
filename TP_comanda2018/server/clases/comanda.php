<?php
include_once "AccesoDatos.php";

class comanda
{
    public $idComanda;
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
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into comanda (id_mesa,monto,estado,foto,cliente,fecha,id_mozo)values(:idMesa,:monto,:estado,:foto,:cliente,:fecha,:idMozo)");
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

    public function CargarPrecioComanda($auxId, $monto)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE comanda set monto=:monto WHERE id=$auxID");
        $consulta->bindValue(':monto', $monto, PDO::PARAM_STR);
        
        return $consulta->rowCount();
    }

    public static function CerrarComanda($auxId)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE comanda set estado=:estado where id=$auxId");
        $consulta->bindValue(':estado', "cerrado", PDO::PARAM_STR);
        
        return $consulta->execute();
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

    
    public static function TraerTodasLasComandasEstado($estado)
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("select * from comanda where estado=:estado");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->execute();	
            if($consulta->rowCount() == 0){
                return false;   
            }			
			return $consulta->fetchAll(PDO::FETCH_CLASS, "comanda");		
    }

    /* AGREGAR FUNCIONES PARA TRAER MESA MAS FACTURO, 
        MESA QUE MENOS FACTURO,
       MESA CON COMANDA DE MAYOR VALOR, 
       MESA CON COMANDA DE MENOR VALOR,
       MESA MAS USADA
       MESA MENOS USADA
       TRAER COMANDAS ENTRE FECHAS
    */









}