<?php
include_once "AccesoDatos.php";
class mesa
{
    public $id;
    public $codigo;
    public $estado;
    public $usos;

    //EN CASO DE TENER QUE AGREGAR UNA MESA 
    //LOS DATOS DE MESA SE CARGAN POR UNICA VEZ. 

    public function InsertarMesaParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into mesa (codigo,estado,usos)values(:codigo,:estado,:usos)");
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':usos', $this->usos, PDO::PARAM_INT);
            $consulta->execute();	
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodas()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from mesa");
            $consulta->execute();	
            if($consulta->rowCount() == 0){
                return false;   
            }			
			return $consulta->fetchAll(PDO::FETCH_CLASS, "mesa");		
    }

    public static function TraerMesaId($auxId)
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from mesa where id = $auxId");
            $consulta->execute();	
            $mesaAux = $consulta->fetchObject('mesa');
            if($consulta->rowCount() == 0){
                return false;   
            }			
			return $mesaAux;		
    }		

    public static function TraerTodasDisponibles()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from mesa where estado = 'cerrado'");
            $consulta->execute();	
            if($consulta->rowCount() == 0){
                return false;   
            }			
			return $consulta->fetchAll(PDO::FETCH_CLASS, "mesa");		
    }


    public static function cambiarEstadoMesa($auxID,$auxEST)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE mesa set estado=:estado where id='$auxID'");
        $consulta->bindValue(':estado', $auxEST, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public static function RetornarUsosMesa($auxId)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT usos from mesa where id=$auxId");
        
        $consulta->execute();
        return $consulta->fetch();
    }

    public static function CargarUsoMesa($auxId, $usos)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE mesa set usos=:usos WHERE id=$auxId");
        $consulta->bindValue(':usos', $usos, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
        
    }

    public static function TraerMesaMasUtilizada()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesa WHERE usos=(SELECT MAX(usos) FROM mesa) ");
        $consulta->execute();
        $mesa=$consulta->fetchObject("Mesa");
       
        return $mesa;
    }


    public static function TraerMesaMenosUtilizada()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesa WHERE usos=(SELECT MIN(usos) FROM mesa) ");
        
        $consulta->execute();
        $mesa=$consulta->fetchObject("Mesa");
    
        return $mesa;
    }


}