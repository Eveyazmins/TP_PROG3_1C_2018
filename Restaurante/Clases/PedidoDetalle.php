<?php

include_once "Empleado.php";

class PedidoDetalle
{
    #ATRIBUTOS -----------------------------------------------------------------------------------
    public $idDetalle;
    public $idPedido;
    public $producto;
    public $tiempoPreparacion;
    public $tiempoEntrega;
    public $estado;
    public $idEmpleado;
    public $sector;

    #FUNCIONES DB-----------------------------------------------------------------------------------

    public function GuardarDetalle()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        INSERT INTO Pedidodetalle(idPedido, producto, estado, sector)
        VALUES(:idPedido, :producto, :estado, :sector)");
        
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':producto', $this->producto, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();
        
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }


    public static function TraerDetallesPorPedido($idPedido) 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from Pedidodetalle
        WHERE idPedido=:idPedido");  
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $detalles= $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");      
    }

    public static function TraerTodosLosDetalles() 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from Pedidodetalle");  
        $consulta->execute();
        $detalles= $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");
        
        return $detalles;        
    }

    public static function TraerUnDetalle($idDetalle) 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from Pedidodetalle WHERE idDetalle=:idDetalle");  
        $consulta->bindValue(':idDetalle', $idDetalle, PDO::PARAM_INT);
        $consulta->execute();
        $detalle= $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");
        
        return $detalle;                        
    }

    public static function TraerPendientesEmpleado($idEmpleado)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from Pedidodetalle where idEmpleado = $idEmpleado 
        AND estado=:estado");  
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);
        $consulta->execute();
        $pedidos= $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");
        
        return $pedidos;
    }

    public function ModificarDetalle()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE Pedidodetalle 
        SET 
        idPedido=:idPedido, 
        producto=:producto, 
        tiempoPreparacion=:tiempoPreparacion, 
        idEmpleado=:idEmpleado, 
        estado=:estado,
        sector=:sector, 
        tiempoEntrega=:tiempoEntrega 
        WHERE idDetalle=:id");

        $consulta->bindValue(':idPedido',$this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':producto',$this->producto, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoPreparacion',$this->tiempoPreparacion, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado',$this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado',$this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':sector',$this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEntrega',$this->tiempoEntrega, PDO::PARAM_STR);
        $consulta->bindValue(':id',$this->idDetalle, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public function PrepararDetalle()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE Pedidodetalle 
        SET 
        tiempoPreparacion=:tiempoPreparacion, 
        idEmpleado=:idEmpleado, 
        estado=:estado 
        WHERE idDetalle=:id");
        $consulta->bindValue(':tiempoPreparacion',$this->tiempoPreparacion, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado',$this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado',$this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id',$this->idDetalle, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public function EntregarDetalle()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE Pedidodetalle 
        SET tiempoEntrega=:tiempoEntrega, 
        estado=:estado
        WHERE idDetalle=:id");
        $consulta->bindValue(':tiempoEntrega',$this->tiempoEntrega, PDO::PARAM_STR);
        $consulta->bindValue(':estado',"listo para servir", PDO::PARAM_STR);
        $consulta->bindValue(':id',$this->idDetalle, PDO::PARAM_INT);
        return $consulta->execute();
    }

    //VER SI SACAR MAXIMO
    //VER SI A LA HS DE ENTREGA RESTAR EL TIEMPO DE PREPARACION
    //DESPUES SE INVOCARÁ ESTA FUNCION PARA CONSULTAR POR CODIGO ALFA
    public static function TiempoRestante($idPedido)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT tiempoPreparacion 
        FROM pedidodetalle 
        WHERE idpedido=$idPedido");  
        $consulta->execute();
        return $consulta->fetch();
    }
    
    public static function CerrarMesa($idMesa)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE Pedidodetalle as pd 
        SET pd.estado ='facturado' 
        WHERE pd.idPedido IN (SELECT p.id from pedidos as p where p.idMesa=$idMesa)");  
        $consulta->execute();
        
        return $consulta->fetch();
    }

}