<?php
include_once "AccesoDatos.php";
class pedido
{
    public $id;
    public $idComanda;
    public $idProducto;
    public $cantidad;
    public $tipo;
    public $monto;
    public $horaEstimada;
    public $horaFinal;
    public $estado;
    public $idEmpleado;
    public $fecha;

    public function InsertarPedidoParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into pedidos2 (idComanda,idProducto,cantidad,tipo,horaEstimada,horaFinal,estado,idEmpleado,monto,fecha)values(:idComanda,:idProducto,:cantidad,:tipo,:horaEstimada,:horaFinal,:estado,:idEmpleado,:monto,:fecha)");
            $consulta->bindValue(':idComanda',$this->idComanda, PDO::PARAM_INT);
            $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':horaEstimada',$this->horaEstimada, PDO::PARAM_STR);
            $consulta->bindValue(':horaFinal', $this->horaFinal, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':idEmpleado',$this->idEmpleado, PDO::PARAM_INT);
            $consulta->bindValue(':monto', $this->monto, PDO::PARAM_STR);
            $consulta->bindValue(':fecha',$this->fecha, PDO::PARAM_STR);
			$consulta->execute();	
			return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");	
    }

    public static function TraerTodoLosPedidos()
	{	
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos2");
        $consulta->execute();	
        if($consulta->rowCount() == 0){
            return false;   
        }			
        return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");		
    }

    public static function TraerTodoLosPedidosCancelados()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos2 where estado =:estado");
        $consulta->bindValue(':estado', "cancelado", PDO::PARAM_STR);
        $consulta->execute();	
        if($consulta->rowCount() == 0){
            return false;   
        }			
        return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");	
    }

    public static function TraerTodoLosPedidosDemorados()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos2 where horaEstimada < horaFinal");
        $consulta->execute();	
        if($consulta->rowCount() == 0){
            return false;   
        }			
        return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");	
    }


    public static function TraerPedidoID($idPedido) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos2 where id = '$idPedido'");
			$consulta->execute();
            $EmpAux= $consulta->fetchObject('pedido');
            if($consulta->rowCount() == 0){
                return false;   
            }
			return $EmpAux;		
    }

    public function TomarPedidoParametros($auxID)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE pedidos2 set estado=:estado,horaEstimada=:horaEstimada,idEmpleado=:idEmpleado WHERE id=$auxID");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':horaEstimada', $this->horaEstimada, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function FinalizarPedidoParametros($auxID)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE pedidos2 set estado=:estado,horaFinal=:horaFinal WHERE id=$auxID");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':horaFinal', $this->horaFinal, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function CancelarPedidoParametros($auxID)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE pedidos2 set estado=:estado WHERE id=$auxID");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->rowCount();
    }


    public static function TraerCantidadOperacionesSectorFechas($tipo,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();

        if ($hasta == "" && $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 where tipo = :tipo AND estado = 'listo para servir' AND fecha >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE tipo = :tipo AND estado = 'listo para servir' AND fecha <=:hasta ");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE tipo = :tipo AND estado = 'listo para servir' AND fecha BETWEEN :desde AND :hasta ");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde =="" && $hasta =="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE tipo =:tipo AND estado = 'listo para servir'");
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }  

        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
    }

    //OPERACIONES POR EMPLEADO Y SECTOR

    public static function TraerCantidadOperacionesSectorEmpleadoFechas($tipo, $desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();

        if ($hasta == "" && $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idEmpleado, count(*) as cantidad FROM pedidos2 where tipo = :tipo AND estado = 'Listo para servir' AND fecha >=:desde GROUP BY idEmpleado");
            
            
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idEmpleado, count(*) as cantidad FROM pedidos2 where tipo = :tipo AND estado = 'Listo para servir' AND fecha <=:hasta GROUP BY idEmpleado");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idEmpleado, count(*) as cantidad FROM pedidos2 where tipo = :tipo AND estado = 'Listo para servir' AND fecha BETWEEN :desde AND :hasta GROUP BY idEmpleado");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde =="" && $hasta =="") {
            //$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant, idEmpleado FROM pedidos2 WHERE tipo =:tipo AND estado = 'Listo para servir'");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idEmpleado, count(idEmpleado) as cantidad FROM pedidos2 where tipo = :tipo AND estado = 'Listo para servir' GROUP BY idEmpleado");
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }  

        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
    }

    //CANTIDAD OPERACIONES POR EMPLEADO

    public static function TraerCantidadOperacionesEmpleadoFechas($auxId, $desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();

        if ($hasta == "" && $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 where idEmpleado = $auxId AND estado = 'Listo para servir' AND fecha >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
           
        }

        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE idEmpleado = $auxId AND estado = 'Listo para servir' AND fecha <=:hasta ");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
         
        }

        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE idEmpleado = $auxId AND estado = 'Listo para servir' AND fecha BETWEEN :desde AND :hasta ");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            
        }

        if ($desde =="" && $hasta =="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE idEmpleado = $auxId AND estado = 'Listo para servir'");
        }  

        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
      
    }

    
	


    public static function TraerProductoMasVendidoSector($tipo,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT pe.idProducto AS idProducto, pr.nombreProducto AS nombre, COUNT (pe.id) FROM `pedidos2` pe, producto pr WHERE pe.estado = 'Listo para servir' GROUP BY `p.id`");
        $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    public static function TraerPedidoMasTarde($idComanda)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id, max(horaEstimada) AS hora FROM pedidos2 WHERE idComanda =:idComanda");
        $consulta->bindValue(":idComanda", $idComanda, PDO::PARAM_INT);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $pedido=$consulta->fetchObject("pedido");
    
        return $pedido;
    }


}