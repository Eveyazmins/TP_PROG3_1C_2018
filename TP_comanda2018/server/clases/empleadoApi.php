<?php
include_once "empleado.php";
include_once "historico.php";

class empleadoApi extends empleado
{
    //FUNCIONES API

    /*
    Cargar un empleado (desde el index valido que solo Socios puedan agregar empleados)
    */
    
    public function CargarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        //$objDelaRespuesta= new stdclass();

        //Validaciones de campos vacíos
        if (!isset($ArrayDeParametros['email'])) 
        {
            return $response->withJson("email no puede esta vacio",404);   
        }
        
        if (!isset($ArrayDeParametros['clave'])) 
        {
            return $response->withJson("clave no puede esta vacio",404);   
        }

        if (!isset($ArrayDeParametros['usuario'])) 
        {
            return $response->withJson("usuario no puede esta vacio",404);   
        }

        if (!isset($ArrayDeParametros['tipo'])) 
        {
            return $response->withJson("tipo no puede esta vacio",404);   
        }

        if (!isset($ArrayDeParametros['estado'])) {
            return $response->withJson("estado no puede esta vacio",404);   
        }

        //valida mail
        $email= strtolower($ArrayDeParametros['email']);
        
        if (empleadoApi::is_valid_email($email) !== true) 
        {
            return $response->withJson("no es email",404);
        }

        //Encripta la clave para guardar en DB (y en LogIn Api poder validarla con Validate_pass)
        $clave= password_hash($ArrayDeParametros['clave'],PASSWORD_BCRYPT);

        //valida contenido de usuario
        $usuario= strtolower($ArrayDeParametros['usuario']);

        if ($this->validarNombre($usuario) == false) 
        {
            return $response->withJson("Error: Caracteres no permitidos",404);
        }
        
        $tipo= strtolower($ArrayDeParametros['tipo']);

        $estado= strtolower($ArrayDeParametros['estado']);

        $empleadoAux = new empleado();

        $empleadoAux->email = $email;
        $empleadoAux->clave = $clave;
        $empleadoAux->usuario = $usuario;
        $empleadoAux->tipo = $tipo;
        $empleadoAux->estado = $estado;

        $archivos = $request->getUploadedFiles();
        $destino="./fotosEmpleado/";
        //var_dump($archivos);
        //var_dump($archivos['foto']);
        if(isset($archivos['foto']))
        {
            $nombreAnterior=$archivos['foto']->getClientFilename();
            $extension= explode(".", $nombreAnterior)  ;
            //var_dump($nombreAnterior);
            $extension=array_reverse($extension);
            $archivos['foto']->moveTo($destino.$usuario.".".$extension[0]);
            $empleadoAux->foto = $destino.$usuario.".".$extension[0];
        } 
        else
        {
            $empleadoAux->foto = "sin foto";
        }
        
        $e = empleado::TraerEmail($empleadoAux->email);

        if ($e == null){
            $empleadoAux->InsertarEmpleadoParametros();
            $response->getBody()->write("Se dio de alta al empleado: ".$usuario,202);
            //$response->withJson("Se dio de alta al empleado: ".$nombre);

        }else {
            return $response->withJson("El empleado ya existee ",404);
        }

        return $response;   
    }

    /*
    Borrar un empleado
    */

    public function BorrarUno($request, $response, $args) 
    {
        //return $response->withJson("El emplEado ya existe ",404);
            $ArrayDeParametros = $request->getParsedBody(); //para delete urlencoded
            if (!isset($ArrayDeParametros['id'])) {
                return $response->withJson("Error al borrar: Debe ingresar ID de empleado",404);
            }
            $id=$ArrayDeParametros['id'];

            if(($id == "") or ($this->validarNumero($id)==false))
            {
                return $response->withJson("Ingrese un ID valido",404);
            }

            $empBorrar = empleado::TraerEmpleadoID($id);
            if ($empBorrar == false) {
                return $response->withJson('Error al borrar: No existe empleado con id: '.$id,404);
            }

            $nombreViejo =$empBorrar->usuario;
            if(empleado::BorrarEmpleadoID($id)>0){       
                return $response->withJson('Se borro con exito a '.$nombreViejo,202);
            }else{
                return $response->withJson('Error al Borrar el empleado',404);
            }
    }

    /*
    Suspender y reactivar un empleado
    */

    public function suspenderUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['id'])) 
        {
            return $response->withJson("Ingrese ID del empleado",404);
        }
        $id= $ArrayDeParametros['id'];

        if(($id == "" ) or ($this->validarNumero($id)==false))
        {
            return $response->withJson("Ingrese ID de empleado valido",404);
        }

        $objDelaRespuesta= new stdclass();
        $empModificar = new empleado();
        $empModificar = empleado::TraerEmpleadoID($id);
            
        if ($empModificar != NULL ) 
        {
            if($empModificar->estado != 'suspendido')
            {
                $accion = 'suspendido';
                empleado::CambiarEstadoEmpleadoParametros($id,$accion);
                $objDelaRespuesta->resultado="Se suspendio empleado : ".$empModificar->usuario;
            }
            else
            {
                $objDelaRespuesta->resultado="El empleado ya se encuentra suspendido";
            }
        }
        else 
        {
            $objDelaRespuesta->resultado="Error al suspender: El empleado no existe";
        }
        $newResponse = $response->withJson($objDelaRespuesta, 200);
        return $newResponse;     
    }

    public function activarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['id'])) 
        {
            return $response->withJson("Ingrese ID del empleado",404);
        }
        $id= $ArrayDeParametros['id'];

        if(($id == "" ) or ($this->validarNumero($id)==false))
        {
            return $response->withJson("Ingrese ID de empleado valido",404);
        }

        $objDelaRespuesta= new stdclass();
        $empModificar = new empleado();
        $empModificar = empleado::TraerEmpleadoID($id);
            
        if ($empModificar != NULL ) 
        {
            if($empModificar->estado != 'activo')
            {
                $accion = 'activo';
                empleado::CambiarEstadoEmpleadoParametros($id,$accion);
                $objDelaRespuesta->resultado="Se re-activo empleado : ".$empModificar->usuario;
            }
            else
            {
                $objDelaRespuesta->resultado="El empleado ya se encuentra activo";
            }
        }
        else 
        {
            $objDelaRespuesta->resultado="Error al reactivar: El empleado no existe";
        }
        $newResponse = $response->withJson($objDelaRespuesta, 200);
        return $newResponse;     
    }

    /*
    Traer TODOS los empleados
    */
    
    public function traerTodos($request, $response, $args) 
	{
        $suspendido = $request->getAttribute('suspendidos');
        if (!empty($args))
        {
            $todosEmpleados = empleado::TraerTodoLosEmpleadosSuspendidos();
            if ($todosEmpleados ==false) 
            {
                return $response->withJson("No hay empleados suspendidos");
            } 
            return $response->withJson($todosEmpleados, 200);
        }
        else 
        {
            $todosEmpleados = empleado::TraerTodoLosEmpleados();
		    return $response->withJson($todosEmpleados, 200);     
        }
    }

    /*
    Traer UN empleado por id
    */
    public function traerUno($request, $response, $args) 
	{
        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['id']))
        {
            return $response->withJson("Ingrese ID del empleado!", 400);
        }
        
        $id= $ArrayDeParametros['id'];

        if ($this->validarNumero($id) == false) 
        {
            return $response->withJson("Ingrese ID válido",404);
        }

        if($id == null)
        {
            return $response->withJson("Ingrese ID del empleado",404);
        }

        $empBuscado = empleado::TraerEmpleadoID($id);
        return $response->withJson($empBuscado, 200);  
    }
        

    //Listado de operaciones por empleado 
  

    public function operacionesEmpleado($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        if (!empty($args))
        {
            $email = $args['email'];
        
            if ($empleadoAux = empleado::TraerEmpleadoEmail($email))
            {
                $objDelaRespuesta->empleado=$empleadoAux->usuario;
                if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
                {
                    $desde= $ArrayDeParametros['desde'];
                    
                    $hasta= $ArrayDeParametros['hasta'];
                    
                    if (($desde > $hasta) AND ($hasta != ""))
                    {
                        //throw new Exception('Error: desde no puede ser mayor que hasta');
                        return $response->withJson("Error: Inconsistencia de fechas!",404);
                    }
                    $objDelaRespuesta->cantIngresos = pedido::TraerCantidadOperacionesEmpleadoFechas($empleadoAux->id, $desde, $hasta);
                    $objDelaRespuesta->msj ="Operaciones desde ".$desde." hasta ".$hasta;
                }
                if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) 
                {
                    $desde= $ArrayDeParametros['desde'];
                    
                    $objDelaRespuesta->cantIngresos =pedido::TraerCantidadOperacionesEmpleadoFechas($empleadoAux->id, $desde, "");
                    $objDelaRespuesta->msj ="Operaciones desde ".$desde." hasta hoy";
                }
                if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
                {
                    $hasta= $ArrayDeParametros['hasta'];
                   
                    $objDelaRespuesta->cantIngresos =pedido::TraerCantidadOperacionesEmpleadoFechas($empleadoAux->id, "", $hasta);
                    $objDelaRespuesta->msj ="Operaciones desde el inicio de actividades hasta ".$hasta;
                }
                if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) 
                {
                    $objDelaRespuesta->cantIngresos =pedido::TraerCantidadOperacionesEmpleadoFechas($empleadoAux->id, "", ""); 
                    $objDelaRespuesta->msj ="Operaciones desde el inicio de actividades hasta hoy";
                }
                return $response->withJson($objDelaRespuesta,200);
            }
            else
            {
                return $response->withJson("El empleado no existe ",206);
            }
        }
        else 
        {
            return $response->withJson("Error: ingrese Email! ",404);
        }
    }

    

    /*
    Listado de ingresos por empleado
    */

    public function loginEmpleado($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        if (!empty($args))
        {
            $email = $args['email'];
        
            if ($empleadoAux = empleado::TraerEmpleadoEmail($email))
            {
                $objDelaRespuesta->empleado=$empleadoAux->usuario;
                
                if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
                {
                    $desde= $ArrayDeParametros['desde'];

                    $hasta= $ArrayDeParametros['hasta'];

                    if (($desde > $hasta) and ($hasta != "")) 
                    {
                        //throw new Exception('Error: desde no puede ser mayor que hasta');
                        return $response->withJson("Error: inconsistencia de fechas!",404);
                    }
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,$desde,$hasta);
                }
                if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) 
                {
                    $desde= $ArrayDeParametros['desde'];
                    
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,$desde,"");
                }
                if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
                {
                    $hasta= $ArrayDeParametros['hasta'];
                
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,"",$hasta);
                }
                if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta']))
                {
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,"","");
                }
                
                return $response->withJson($objDelaRespuesta,200);
            }
            else
            {
                return $response->withJson("El empleado no existe ",206);
            }
        }
        else 
        {
            return $response->withJson("Error: ingrese email!",404);
        }
    }

    /*
    FUNCIONES DE VALIDACIÓN (nombres, numeros, mail)
    */

    public function validarNombre($cadena)
    { 
        $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
        for ($i=0; $i<strlen($cadena); $i++)
        { 
            if (strpos($permitidos, substr($cadena,$i,1))===false)
            {  
                return false; 
            } 
        }  
        return true; 
    }

    public function validarNumero($cadena)
    { 
        $permitidos = "0123456789"; 
        for ($i=0; $i<strlen($cadena); $i++)
        { 
            if (strpos($permitidos, substr($cadena,$i,1))===false)
            { 
                return false; 
            } 
        }  
        return true; 
    }

    public static function is_valid_email($str)
    {
        return (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
    }

    
    public function modificarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody();
            if (!isset($ArrayDeParametros['id'])) {
                return $response->withJson('Error al modificar: Debe ingresar ID de empleado',404);
            }
            $id= $ArrayDeParametros['id'];
            $objDelaRespuesta= new stdclass();
            $empModificar = empleado::TraerEmpleadoID($id);

            if ($empModificar != false) {
                $objDelaRespuesta->msj = "se modifico empleado con id ".$id;
                if (isset($ArrayDeParametros['usuario'])) {
                    $usuario = strtolower($ArrayDeParametros['usuario']);
                    $empModificar->usuario = $usuario;
                    if ($empModificar->usuario== "" || !isset($empModificar->usuario)) {
                        return $response->withJson('Error: usuario no puede esta vacio',404);
                    }
                    if ($this->validarNombre($empModificar->usuario) == false) {
                        return $response->withJson('Error: Usuario solo puede contener letras y numeros',404);
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->usuario =$usuario;
                }
                if (isset($ArrayDeParametros['email'])) {
                    $email = strtolower($ArrayDeParametros['email']);
                    if (empleadoApi::is_valid_email($email) !== true) {
                        return $response->withJson("no es email",404);
                    }
                    $empModificar->email = $email;
                    if ($empModificar->email== "" || !isset($empModificar->email)) {
                        return $response->withJson("Error: email no puede esta vacio",404);
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->email =$email;
                }
                if (isset($ArrayDeParametros['clave'])) {
                    $clave = password_hash($ArrayDeParametros['clave'],PASSWORD_BCRYPT);
                    $empModificar->clave = $clave;
                    if ($empModificar->clave== "" || !isset($empModificar->clave)) {
                        return $response->withJson('Error: clave no puede esta vacio',404);
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->clave =$clave;
                }
                if (isset($ArrayDeParametros['tipo'])) {
                    $tipo = strtolower($ArrayDeParametros['tipo']);
                    $empModificar->tipo = $tipo;
                    if ($empModificar->tipo== "" || !isset($empModificar->tipo)) {
                        return $response->withJson('Error: tipo no puede esta vacio',404);
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->tipo =$tipo;
                }
                if (isset($ArrayDeParametros['estado'])) {
                    $estado = strtolower($ArrayDeParametros['estado']);
                    $empModificar->estado = $estado;
                    if ($empModificar->estado== "" || !isset($empModificar->estado)) {
                        return $response->withJson('Error: estado no puede esta vacio',404);
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->estado =$estado;
                }
            }
            else {
                return $response->withJson('Error no existe el ID del empleado',404);
            }
            return $response->withJson($objDelaRespuesta, 202);       
    }

    //Manejo Imagen 

    public function obtenerArchivo($nombre) 
	{
        if(!isset($_FILES['foto']))
        {
            throw new Exception('Error: No existe foto');
        }
        if ( 0 < $_FILES['foto']['error'] ) {
			return null;
		}
		else {
            $foto = $_FILES['foto']['name'];
			
            $extension= explode(".", $foto);
            $tipo = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if($tipo != "jpg" && $tipo != "jpeg" && $tipo != "png") {
                throw new Exception('Error: de formato, solo se acepta jpg jpeg png');
            }

            $nombreNuevo = 'fotosEmpleados/'.$nombre.".".strtolower($extension[1]);
            return $nombreNuevo;
		}
    }


    public static function fotoPapelera($fotoVieja, $nombre)
    {
            $ahora = date("Ymd-His");
            $extension = pathinfo($fotoVieja, PATHINFO_EXTENSION);
            rename($fotoVieja , "fotosEmpleados/papelera/".trim($nombre)."-".$ahora.".".$extension);
    }




}
