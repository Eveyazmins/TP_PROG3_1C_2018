<?php

include_once "Empleado.php";

class Pedido
{
    #ATRIBUTOS -----------------------------------------------------------------------------------
    public $idPedido;
    public $idMesa;
    public $fotoMesa;
    public $horaPedido;

    #FUNCIONES DB --------------------------------------------------------------------------------
    //VER LO DEL ID ALFANUM
    public function GuardarPedido()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        INSERT INTO Pedidos (idMesa, fotoMesa, horaPedido
        VALUES(:idMesa, :fotoMesa, :horaPedido");

        $consulta->bindValue(':idMesa',$this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':fotoMesa', $this->fotoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':horaPedido', $this->horaPedido, PDO::PARAM_STR);
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