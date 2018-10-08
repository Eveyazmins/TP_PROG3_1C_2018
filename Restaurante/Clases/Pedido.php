<?php

include_once "Empleado.php";

class Pedido{

public $id;
public $idMesa;
public $idMozo;
public $estado;
public $fotoMesa;

    public function GuardarPedido()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
	    $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into Pedidos (idMesa, idMozo, estado, fotoMesa)
        values(:idMesa, :idMozo, estado, :fotoMesa)");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado);
        $consulta->bindValue(':fotoMesa', $this->fotoMesa, PDO::PARAM_STR);
        $consulta->execute();
	    return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosPedidos() 
    {
	    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
	    $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from pedidos ");  
	    $consulta->execute();
	    $pedidos= $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");        
        return $pedidos;							
    }

    

}

