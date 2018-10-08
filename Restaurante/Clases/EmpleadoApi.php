<?php
require_once 'Empleado.php';
require_once 'IApiUsable.php';
require_once 'AutentificadorJWT.php';

class EmpleadoApi extends Empleado implements IApiUsable
{
    //CARGAR NUEVO EMPLEADO

    public function CargarUno($request, $response, $args)
    {
        $objDelaRespuesta= new stdclass();
        $ArrayDeParametros = $request->getParsedBody();

        $usuario= $ArrayDeParametros['usuario'];
        $sector= $ArrayDeParametros['sector'];
        $clave= $ArrayDeParametros['clave'];
        $perfil= $ArrayDeParametros['perfil'];
        $estado= "Activo";

        $miEmpleado= new Empleado();
        $miEmpleado->usuario=$usuario;
        $miEmpleado->clave=$clave;
        $miEmpleado->sector=$sector;
        $miEmpleado->perfil=$perfil;
        $miEmpleado->estado=$estado;

        $ultimoId=$miEmpleado->InsertarEmpleado();    
        $objDelaRespuesta->respuesta="Se guardo el Empleado";
        $objDelaRespuesta->ultimoIdGrabado=$ultimoId;   
        return $response->withJson($objDelaRespuesta, 200);
    }
    
    //MODIFICAR DATOS EMPLEADO

    public function ModificarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody(); 
        $objDelaRespuesta= new stdclass();
        
        $usuario= $ArrayDeParametros['usuario'];
        $sector= $ArrayDeParametros['sector'];
        $clave= $ArrayDeParametros['clave'];
        $perfil= $ArrayDeParametros['perfil'];
        $id= $ArrayDeParametros['id'];

        $miEmpleado= new Empleado();
        $miEmpleado->usuario=$usuario;
        $miEmpleado->clave=$clave;
        $miEmpleado->sector=$sector;
        $miEmpleado->perfil=$perfil;
        $miEmpleado->id=$id;

        $resultado =$miEmpleado->ModificarEmpleado();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->resultado=$resultado;
        $objDelaRespuesta->tarea="modificar";

        return $response->withJson($objDelaRespuesta, 200);		
   }

   //BORRAR EMPLEADO
   
    public function BorrarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        $id=$ArrayDeParametros['id'];
        $empleado= new Empleado();
        $empleado->id=$id;
    
        $cantidadDeBorrados=$empleado->BorrarEmpleado();

        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->cantidad=$cantidadDeBorrados;
        
        if($cantidadDeBorrados>0)
        {
            $objDelaRespuesta->resultado="algo borro!!!";
        }
        else
        {
            $objDelaRespuesta->resultado="no Borro nada!!!";
        }
        $newResponse = $response->withJson($ArrayDeParametros, 200);  
        return $newResponse;
    }

    // SUSPENDER EMPLEADO (VER SI FUNCIONA ASI O USAR ESTATICAS)

    public function SuspenderUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        $id=$ArrayDeParametros['id'];
        $empleado= new Empleado();
        $empleado->id=$id;
    
        $cantidadDeSuspendidos=$empleado->SuspenderEmpleado();
    }

    //RE-ACTIVAR EMPLEADO (VER SI FUNCIONA ASI O USAR ESTATICAS)

    public function ReanudarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        $id=$ArrayDeParametros['id'];
        $empleado= new Empleado();
        $empleado->id=$id;
    
        $cantidadDeSuspendidos=$empleado->ReanudarEmpleado();
    }

    //LOGIN (VER SI FUNCIONA CON 'VALIDARLOGIN' ESTATICA)

    public function Login($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        $usuario=$ArrayDeParametros['usuario'];
	    $clave=$ArrayDeParametros['clave'];
        
        $empleado=Empleado::ValidarLogIn($usuario,$clave);
        $datos = array('usuario' => $empleado->usuario,'perfil' => $empleado->perfil, 
        'id'=>$empleado->id, 'sector'=>$empleado->sector , 'estado'=>$empleado->estado);
        
        $token= AutentificadorJWT::CrearToken($datos);
        $respuesta= array('token'=>$token,'datos'=> $datos);
        
		return $response->withJson($respuesta, 200);		
    }

    //OPERACIONES DE UN EMPLEADO

    public static function OperacionesUnEmpleado($request, $response, $args)
    {
        $id=$args['id'];
        $operaciones=Empleado::CantidadOperacionesEmpleado($id);
        return $response->withJson($operaciones, 200);
    }

    //OPERACIONES DE UN SECTOR

    public static function OperacionesUnSector($request, $response, $args)
    {
        $sector=$args['sector'];
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta=Empleado::CantidadOperacionesSector($sector);
        return $response->withJson($objDelaRespuesta, 200);
    }

    //OPERACIONES EMPLEADO Y SECTOR (VER SI ES ASI)

    public static function OperacionesUnEmpleadoYSector($request, $response, $args)
    {
        $sector=$args['sector'];
        $sector=$args['id'];
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta=Empleado::CantidadOperacionesEmpleadoSector($sector);
        return $response->withJson($objDelaRespuesta, 200);
    }
    
    //INGRESOS AL SISTEMA

    public static function FechasDeLogueo()
	{
		$objetoAccesoDato= AccesoDatos::DameUnObjetoAcceso();
		$consulta=$objetoAccesoDato->RetornarConsulta("SELECT e.usuario, s.horaInicio from empleados as e, sesiones as s where s.idEmpleado=e.id ORDER by e.usuario");
		$consulta->execute();
		$fechas= $consulta->fetchAll(PDO::FETCH_CLASS);
		return $fechas;
	}


}