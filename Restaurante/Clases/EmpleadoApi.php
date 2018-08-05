<?php
require_once 'Empleado.php';
require_once 'IApiUsable.php';
require_once 'AutentificadorJWT.php';


class EmpleadoApi extends Empleado implements IApiUsable
{
    #FUNCIONES API-----------------------------------------------------------------------------------

    public function TraerUno($request, $response, $args) 
    {    
        $id=$args['id'];
        $empleado=Empleado::TraerUnEmpleado($id);
        if(!$empleado)
        {
           $objRespuesta= new stdclass();
           $objRespuesta->error="No existe El usuario";
           $Respuesta = $response->withJson($objRespuesta, 500); 
        }
        else
        {
            $Respuesta = $response->withJson($empleado, 200); 
        }     
        return $Respuesta;
    }


    public function TraerTodos($request, $response, $args) 
    {
       $todosEmpleados = Empleado::TraerTodosEmpleados();
       $Respuesta = $response->withJson($todosEmpleados,200);  
       return $Respuesta;
    }

    public function CargarUno($request, $response, $args)
    {
        $objRespuesta= new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
       //var_dump($ArrayDeParametros);
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

        $objRespuesta->respuesta="Se guardo el Empleado.";
        $objRespuesta->ultimoIdGrabado=$ultimoId;   
        return $response->withJson($objRespuesta, 200);
    }

    public function BorrarUno($request, $response, $args) {
    $ArrayDeParametros = $request->getParsedBody();
    
    $id=$ArrayDeParametros['id'];
    $empleado= new Empleado();
    $empleado->id=$id;
    
    $cantidadBorrados=$empleado->BorrarEmpleado();

    $objDeRespuesta= new stdclass();
    $objRespuesta->cantidad=$cantidadBorrados;
    if($cantidadDeBorrados>0)
    {
        $objRespuesta->resultado="algo borro!!!";
    }
    else
    {
        $objRespuesta->resultado="no Borro nada!!!";
    }
    $Respuesta = $response->withJson($ArrayDeParametros, 200);  
     return $Respuesta;
    }

    public function ModificarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody(); 
        $objRespuesta= new stdclass();

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
        $objRespuesta= new stdclass();
        //var_dump($resultado);
        $objRespuesta->resultado=$resultado;
        $objRespuesta->tarea="modificar";
        return $response->withJson($objRespuesta, 200);		
   }

   public static function CambiarEstado($request, $response, $args) 
   {
       //PROBAR PARA ACTIVO - ACTIVO
       //PROBAR PARA SUSPENDIDO - SUSPENDIDO
       //PROBAR PARA SUSPENDIDO - ACTIVO

       $ArrayDeParametros = $request->getParsedBody(); 
       $id=$ArrayDeParametros['id'];
       $estado=$ArrayDeParametros['estado'];   	
       $resultado= Empleado::CambiarEstadoEmpleado($id,$estado);
       $objRespuesta= new stdclass();
       //var_dump($resultado);
       $objRespuesta->resultado=$resultado;
       $objRespuesta->tarea="Suspender";
       
       return $response->withJson($objRespuesta, 200);		
   }

   public function Login($request, $response, $args) 
   {
       $ArrayDeParametros = $request->getParsedBody();
       
       $usuario=$ArrayDeParametros['usuario'];
       $clave=$ArrayDeParametros['clave'];
       $empleado=Empleado::ValidarEmpleado($usuario,$clave);

       $datos = array(
           'usuario' => $empleado->usuario,
           'perfil' => $empleado->perfil, 
           'id'=>$empleado->id
        );
        
        $token= AutentificadorJWT::CrearToken($datos);
        $respuesta= array('token'=>$token,'datos'=> $datos);
        
        return $response->withJson($respuesta, 200);		
  }

  public static function SesionesEmpleados($request, $response, $args)
  {
      $objDelaRespuesta= new stdclass();
      $objDelaRespuesta=Empleado::SesionesEmpleados();
      return $response->withJson($objDelaRespuesta, 200);
  }

  public static function OperacionesTodosEmpleados($request, $response, $args)
  {
      $objDelaRespuesta= new stdclass();
      $objDelaRespuesta=Empleado::CantidadOperacionesTodosEmpleados();
      return $response->withJson($objDelaRespuesta, 200);
  }

  public static function OperacionesTodosSectores($request, $response, $args)
  {
      $ArrayDeParametros = $request->getParsedBody();
      $objDelaRespuesta= new stdclass();
      $objDelaRespuesta=Empleado::CantidadOperacionesTodosSectores();
      return $response->withJson($objDelaRespuesta, 200);
  }

  public static function OperacionesUnEmpleado($request, $response, $args)
  {
      $id=$args['id'];
      $operaciones=Empleado::CantidadOperacionesUnEmpleado($id);
      return $response->withJson($operaciones, 200);
  }

  public static function OperacionesUnSector($request, $response, $args)
  {
      $sector=$args['sector'];
      $objDelaRespuesta= new stdclass();
      $objDelaRespuesta=Empleado::CantidadOperacionesUnSector($sector);
      return $response->withJson($objDelaRespuesta, 200);

  }

  







}