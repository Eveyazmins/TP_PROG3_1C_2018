<?php

include_once "Empleado.php";
include_once "Pedido.php";

class Subpedido{

public $id;
public $idPedido;
public $idEmpleado;
public $sector;
public $idProducto;
public $estado;
public $TiempoEstimado;
public $horaFinal;


    public function GuardarSubpedido()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into Subpedidos 
        (idPedido, idProducto, estado, sector)values(:idPedido, :idProducto, :estado, :sector)");
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->producto, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosSubpedidos() 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from Subpedidos");  
		$consulta->execute();
		$subpedidos= $consulta->fetchAll(PDO::FETCH_CLASS, "Subpedido");
    
        return $subpedidos;					
    }

    public static function TraerUnSubpedido($idSubpedido)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from Subpedidos 
        WHERE id=:id");  
        $consulta->bindValue(':id', $idSubpedido, PDO::PARAM_INT);
        $consulta->execute();
        $subpedido= $consulta->fetchAll(PDO::FETCH_CLASS, "Subpedido");

        return $subpedido;
    }

    public static function TraerPendientes($sector)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from Subpedidos where sector=:sector 
        and estado=:estado");    
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);
        $consulta->execute();
        $subpedidos= $consulta->fetchAll(PDO::FETCH_CLASS, "Subpedido");

        return $subpedidos;
    }

    public function TomarSubpedido()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE Subpedidos 
        SET idEmpleado=:idEmpleado, estado=:estado, tiempoEstimado=:tiempoEstimado 
        WHERE id=:id"); 
        $consulta->bindValue(':idEmpleado',$this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado',"En preparación", PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado',$this->tiempoEstimado, PDO::PARAM_STR);
        $consulta->bindValue(':id',$this->id, PDO::PARAM_INT);
        
        return $consulta->execute();
    }

    public function EntregarSubpedido()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE Subpedidos 
        SET horaFinal=:horaFinal, estado=:estado WHERE id=:id");
        $consulta->bindValue(':horaFinal',$this->horaFinal, PDO::PARAM_STR);
        $consulta->bindValue(':estado',"listo para servir", PDO::PARAM_STR);
        $consulta->bindValue(':id',$this->id, PDO::PARAM_INT);
        return $consulta->execute();
    }
    
    public static function TraerPendientesEmpleado($idEmpleado)
    {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
    $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from Subpedidos where 
    idEmpleado=:idEmpleado");    
    $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
    $consulta->execute();
    $subpedidos= $consulta->fetchAll(PDO::FETCH_CLASS, "Subpedido");

    return $subpedidos;
    }

    //TIEMPO ESTIMADO Y MAS CONSULTAS

    public static function TiempoRestante($idMesa, $idPedido)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT tiempoEstimado 
        FROM Subpedidos WHERE idpedido=$idPedido");  
        $consulta->execute();
        return $consulta->fetch();
    }














}