<?php
include_once "AccesoDatos.php";
class pedido
{
    public $idPedido;
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

    public function InsertarVehiculoParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into pedidos2 (id_comanda,id_producto,cantidad,tipo,monto,hora_estimada,hora_final,estado,id_empleado,fecha)values(:idComanda,:idProducto,:cantidad,:tipo,:monto,:horaEstimada,:horaFinal,:estado,:idEmpleado)");
            $consulta->bindValue(':idComanda',$this->idComanda, PDO::PARAM_INT);
            $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':monto', $this->monto, PDO::PARAM_STR);
            $consulta->bindValue(':horaEstimada',$this->horaEstimada, PDO::PARAM_STR);
            $consulta->bindValue(':horaFinal', $this->horaFinal, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':idEmpleado',$this->idEmpleado, PDO::PARAM_INT);
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


    public static function TraerPedidoID($idPedido) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos2 where idPedido = '$idPedido'");
			$consulta->execute();
            $EmpAux= $consulta->fetchObject('pedido');
            if($consulta->rowCount() == 0){
                return false;   
            }
			return $EmpAux;		
    }

    public function TomarPedido($auxID)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE pedidos2 set estado=:estado,hora_estimada=:horaEstimada,id_empleado=:idEmpleado WHERE id=$auxID");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':horaEstimada', $this->tiempoEstimado, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function FinalizarPedido($auxID)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE pedidos2 set estado=:estado,hora_final=:horaFinal WHERE id=$auxID");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':horaFinal', $this->horaFinal, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->rowCount();
    }


    public static function TraerCantidadOperacionesSectorFechas($tipo,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();

        if ($hasta == "" && $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 where tipo = :tipo AND estado = 'Listo para servir' AND fecha >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE tipo = :tipo AND estado = 'Listo para servir' AND fecha <=:hasta ");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE tipo = :tipo AND estado = 'Listo para servir' AND fecha BETWEEN :desde AND :hasta ");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }

        if ($desde =="" && $hasta =="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM pedidos2 WHERE tipo =:tipo AND estado = 'Listo para servir'");
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }  

        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
    }

    //ACA ME QUEDE

    public static function TraerMasVendidosSectorFechas($tipo,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `$tipo`,COUNT(`$tipo`) cant FROM `pedidos` WHERE fecha >= '$desde' GROUP BY `$tipo` HAVING `$tipo` != 'nada' AND `$tipo` != 'Sin pedido' AND `$tipo` != 'Finalizado' ORDER BY cant DESC LIMIT 1");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `$tipo`,COUNT(`$tipo`) cant FROM `pedidos` WHERE fecha <= '$hasta' GROUP BY `$tipo` HAVING `$tipo` != 'nada' AND `$tipo` != 'Sin pedido' AND `$tipo` != 'Finalizado' ORDER BY cant DESC LIMIT 1");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `$tipo`,COUNT(`$tipo`) cant FROM `pedidos` WHERE fecha BETWEEN '$desde' AND '$hasta' GROUP BY `$tipo` HAVING `$tipo` != 'nada' AND `$tipo` != 'Sin pedido' AND `$tipo` != 'Finalizado' ORDER BY cant DESC LIMIT 1");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_INT);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_INT);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `$tipo`,COUNT(`$tipo`) cant FROM `pedidos` WHERE fecha <= '$ahora' GROUP BY `$tipo` HAVING `$tipo` != 'nada' AND `$tipo` != 'Sin pedido' AND `$tipo` != 'Finalizado' ORDER BY cant DESC LIMIT 1");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    public static function TraerUsosMesasFechas($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,COUNT(`nroMesa`) cant FROM `pedidos` WHERE fecha >= '$desde' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);

        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,COUNT(`nroMesa`) cant FROM `pedidos` WHERE fecha <= '$hasta' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,COUNT(`nroMesa`) cant FROM `pedidos` WHERE fecha BETWEEN '$desde' AND '$hasta' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_INT);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_INT);

        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,COUNT(`nroMesa`) cant FROM `pedidos` WHERE fecha <= '$ahora' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);

        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    public static function TraerFacturacionMesasFechas($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
                                                            //SELECT `nroMesa`,COUNT(`nroMesa`) cant FROM `pedidos` GROUP BY `nroMesa` ORDER BY cant DESC
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,SUM(`importe`) cant FROM `pedidos` WHERE fecha >= '$desde' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);

        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,SUM(`importe`) cant FROM `pedidos` WHERE fecha <= '$hasta' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,SUM(`importe`) cant FROM `pedidos` WHERE fecha BETWEEN '$desde' AND '$hasta' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_INT);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_INT);

        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT `nroMesa`,SUM(`importe`) cant FROM `pedidos` WHERE fecha <= '$ahora' GROUP BY `nroMesa` ORDER BY cant DESC");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);

        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }


}