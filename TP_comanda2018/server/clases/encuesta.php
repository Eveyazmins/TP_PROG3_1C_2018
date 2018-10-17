<?php
include_once "AccesoDatos.php";
class encuesta
{
    public $id;
    public $idComanda;
    public $idMesa;
    public $estado;
    public $puntosMesa;
    public $puntosRestaurante;
    public $puntosMozo;
    public $puntosCocinero;
    public $comentario;


    public function insertarEncuestaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into encuesta (idComanda,idMesa,estado)values(:idComanda,:idMesa,:estado)");
        $consulta->bindValue(':idMesa',$this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idComanda',$this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();	
        return $consulta->fetchAll(PDO::FETCH_CLASS, "encuesta");	
    }

    
    public static function TraerEncuestaPendiente($idComandaAux,$nroMesaAux) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT  * FROM `encuesta` WHERE `idMesa`= '$nroMesaAux' AND `idComanda`= '$idComandaAux' AND `estado` = 'en cliente'");
            $consulta->execute();
            $EncuestaAux= $consulta->fetchObject('encuesta');
            if($consulta->rowCount() == 0){
                return false;   
            }
            return $EncuestaAux;		
    }
    

    public static function TraerTodasEncuestasPendientes() 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT  * FROM `encuesta` WHERE `estado` = 'en cliente'");
            $consulta->execute();
            if($consulta->rowCount() == 0){
                return false;   
            }
            return $consulta->fetchAll(PDO::FETCH_CLASS, "encuesta");	
    }


    public function completarEncuesta($idComandaAux,$nroMesaAux)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE encuesta set estado=:estado,puntosMesa=:puntosMesa,puntosRestaurante=:puntosRestaurante,puntosMozo=:puntosMozo,puntosCocinero=:puntosCocinero,comentario=:comentario WHERE `idMesa`= '$nroMesaAux' AND `idComanda`= '$idComandaAux'");
            //$consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':puntosMesa', $this->puntosMesa, PDO::PARAM_INT);
            $consulta->bindValue(':puntosRestaurante', $this->puntosRestaurante, PDO::PARAM_INT);
            $consulta->bindValue(':puntosMozo', $this->puntosMozo, PDO::PARAM_INT);
            $consulta->bindValue(':puntosCocinero', $this->puntosCocinero, PDO::PARAM_INT);
            $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
    }

    public static function traerMejoresComentarios()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT comentario from encuesta where puntosRestaurante > 5 LIMIT 5"); 
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
    }

    public static function traerPeoresComentarios()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT comentario from encuesta where puntosRestaurante < 5 LIMIT 5"); 
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        return $consulta->fetchAll();
    }


}