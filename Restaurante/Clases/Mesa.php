<?php
include_once "AccesoDatos.php";

class Mesa
{
    public $idMesa;
    public $estado;
    public $fecha;
    public $monto;
    public $usos;

    //ALTA DE MESA

    public function GuardarMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
        INSERT into Mesas (idMesa, codigoMesa, usos, estado)values(:idMesa, :usos, :estado)");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':usos', $this->usos, PDO::PARAM_INT);
        $consulta->execute();
        
        return $objetoAccesoDato->RetornarUltimoIdInsertado();        
    }

    //LIBERAR MESA (Se cambia estado y se suma un uso)

    public function LiberarMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
        UPDATE Mesas SET estado=:estado, usos=:usos where idMesa=:idMesa");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':usos', $this->usos, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();		
    }

    //MESA MAS USADA (revisar query)

    public static function MasUtilizada()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * FROM Mesas WHERE usos=(SELECT MAX(usos) FROM Mesas WHERE usos !=0)");
        $consulta->execute();
        $mesa_b=$consulta->fetchObject("Mesa");
       
        return $mesa_b;
    }

    //MESA MENOS USADA

    public static function MenosUtilizada()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        SELECT * FROM Mesas WHERE usos=(SELECT MIN(usos) FROM Mesas WHERE usos!=0) ");
        $consulta->execute();
        $mesa_b=$consulta->fetchObject("Mesa");
    
        return $mesa_b;
    }

    /* METODOS FACTURACIÓN 

    
    public function Facturar()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT SUM(prod.precio) as total from productos as prod where nombre in (SELECT pd.producto FROM pedidodetalle as pd WHERE pd.idPedido in (SELECT id from pedidos as p where p.idMesa=$this->idMesa) and pd.estado='listo para servir') ");
        $consulta->execute();
        return $consulta->fetch();    
    }

    public static function LaQueMasFacturo()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT f.mesa, sum(f.importe) total FROM facturas as f GROUP by f.mesa ORDER BY total DESC LIMIT 1");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);
    
        return $mesa;
    }

    public static function LaQueMenosFacturo()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT f.mesa, sum(f.importe) total FROM facturas as f GROUP by f.mesa ORDER BY total ASC LIMIT 1");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);
    
        return $mesa;
    }      

    public static function LaDeMenorImporte()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT f.mesa, f.importe from facturas as f where f.importe = (SELECT MIN(importe) from facturas)");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);

        return $mesa;

    }

    public static function LaDeMayorImporte()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT f.mesa, f.importe from facturas as f where f.importe = (SELECT MAX(importe) from facturas)");
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);

        return $mesa;

    }

    public static function FacturadoDesdeHasta($mesa, $desde, $hasta)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT SUM(f.importe) total FROM facturas as f WHERE f.mesa= :mesa AND f.fecha <= :hasta AND f.fecha >= :desde");
        $consulta->bindValue(':mesa', $mesa, PDO::PARAM_INT);
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        $mesa=$consulta->fetchAll(PDO::FETCH_CLASS);

        return $mesa;

    }
    */

}
