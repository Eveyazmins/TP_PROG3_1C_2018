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
        INSERT INTO PedidoDetalle(idPedido, producto, estado, sector)
        VALUES(:idPedido, :producto, :estado, :sector)");
        
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':producto', $this->producto, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();
        
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    //ACA ME QUEDE

    public static function TraerTodosLosDetallesPorPedido($idPedido) 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from pedidodetalle
        WHERE idPedido=:idPedido");  
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $detalles= $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");      
    }

    public static function TraerTodosLosDetalles() 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from pedidodetalle");  
        $consulta->execute();
        $detalles= $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");
        
        return $detalles;        
    }

    public static function TraerUnDetalle($idDetalle) 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from pedidodetalle WHERE idDetalle=:idDetalle");  
        $consulta->bindValue(':idDetalle', $idDetalle, PDO::PARAM_INT);
        $consulta->execute();
        $detalle= $consulta->fetchAll(PDO::FETCH_CLASS, "Detalle");
        
        return $detalle;                        
    }

    public static function TraerPendientesEmpleado($idEmpleado)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        SELECT * from pedidodetalle where idEmpleado = $idEmpleado 
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
        UPDATE pedidodetalle 
        SET 
        idPedido=:idPedido, 
        producto=:producto, 
        horaEstimada=:horaEstimada,
        idEmpleado=:idEmpleado,
        estado=:estado,
        sector=:sector,
        WHERE idDetalle=:id");
        $consulta->bindValue(':idPedido',$this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':producto',$this->producto, PDO::PARAM_STR);
        $consulta->bindValue(':horaEstimada',$this->horaEstimada, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado',$this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado',$this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':sector',$this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':id',$this->idDetalle, PDO::PARAM_INT);
       
        return $consulta->execute();
    }

    public function EntregarDetalle()
    {
        //AGREGAR UN IF QUE ENTREGUE EL DETALLE CUANDO TODOS LOS DETALLES DEL PEDIDO ESTAN LISTOS
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE pedidodetalle 
        SET 
        horaFinal=:horaFinal, 
        estado=:estado 
        WHERE idDetalle=:id");
        $consulta->bindValue(':horaFinal',$this->horaFinal, PDO::PARAM_STR);
        $consulta->bindValue(':estado',"listo para servir", PDO::PARAM_STR);
        $consulta->bindValue(':id',$this->idDetalle, PDO::PARAM_INT);
        
        return $consulta->execute();
    }

    public static function TiempoRestante($idPedido)
    {
        //SELECCIONAR EL TIEMPO MAXIMO DE DETALLES DEL PEDIDO
     
       
    }











}