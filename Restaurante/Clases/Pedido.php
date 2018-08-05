<?php

include_once "Empleado.php";

class Pedido
{
    #ATRIBUTOS -----------------------------------------------------------------------------------
    public $idPedido;
    public $idMesa;
    public $fotoMesa;
    public $horaPedido;
    public $codigoAlfanum;
    public $nombreCliente;

    #FUNCIONES DB --------------------------------------------------------------------------------
    //VER SI FUNCIONA
    
    public function ObtenerCodigoAlfanum()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * FROM CodigosAlfanum
        WHERE estado = D LIMIT 1");  
        $consulta->execute();
        $codigo = $consulta->fetchAll(PDO::FETCH_CLASS, "Codigo");
        return $codigo;
    }
    
    public function GuardarPedido()
    {
        $this->codigocodigoAlfanum = ObtenerCodigoAlfanum();
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        INSERT INTO Pedidos (idMesa, fotoMesa, horaPedido, codigoAlfanum, nombreCliente
        VALUES(:idMesa, :fotoMesa, :horaPedido, :codigoAlfanum, nombreCliente");

        $consulta->bindValue(':idMesa',$this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':fotoMesa', $this->fotoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':horaPedido', $this->horaPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoAlfanum', $this->codigoAlfanum, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();	
    }

    public function TraerTodosPedidos()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * FROM Pedidos");
        $consulta->execute();
        $pedidos= $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        return $pedidos;
    } 

}