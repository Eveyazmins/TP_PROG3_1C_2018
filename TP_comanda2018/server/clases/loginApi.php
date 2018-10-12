<?php
require_once 'empleado.php';
require_once 'AutentificadorJWT.php';
require_once 'historico.php';


class loginApi
{

    public function login($request, $response, $args) 
    {
        $token="";
        $usuarioBuscado = "";
        $ArrayDeParametros = $request->getParsedBody();

        //Si los campos no estan vacíos

        if(isset( $ArrayDeParametros['email'])&& isset( $ArrayDeParametros['clave']) )
        {
            $email = $ArrayDeParametros['email'];
            $clave = $ArrayDeParametros['clave'];
      
           //verifica si mail es valido

             if (loginApi::is_valid_email($email)==true)
             {
                 $empAux = empleado::TraerEmpleadoEmail($email);
             }
             else
             {
                 return $response->withJson("no existe el email ingresado",404);
             }
            
            // recupero un objeto empleado ingresando el mail ya validado.

            $empAux = empleado::TraerEmpleadoEmail($email);

            /*Si lo encuentra, compara la clave ingresada con la clave encriptada en la DB.
              Si no coinciden muestra error, sino
            */
            
            if ($empAux) 
            {
                if (password_verify($clave, $empAux->clave)) 
                {
                    $usuarioBuscado = empleado::TraerEmpleadoEmail($email);
                } 
                else 
                {
                    //var_dump($clave);
                    return $response->withJson("La contraseña no es válida",404);
                }
            }/*
            else {
                 $usuarioBuscado = false;
                 $empAux =false;
                 return $response->withJson("El usuario no es válido",404);
            }*/
            
            //var_dump($usuarioBuscado);
            $objRespuesta = new stdClass();
            $objRespuesta->msj = null;
            $objRespuesta->Token = null;
               
            /*
            Si el usuario no esta suspendido, genero el token.
            Y guardo los datos de la sesión en la base
            */
            if($usuarioBuscado)
            {
                if ($usuarioBuscado->estado != "suspendido") 
                {
                    $token= AutentificadorJWT::CrearToken(array(
                    'id'=> $usuarioBuscado->id,
                    'email'=> $usuarioBuscado->email,
                    'nombre'=> $usuarioBuscado->nombre,
                    'tipo'=> $usuarioBuscado->tipo,
                    'estado'=> $usuarioBuscado->estado));

                    $datos= AutentificadorJWT::ObtenerData($token);
                    //$objRespuesta->Token = $token;
                    //$objRespuesta->Datos =$datos;
                    $f= date("Y-m-d");
                    $h= date("H:i:s");
                    historico::registrarLogin($usuarioBuscado->id,$f,$h);
                    $objRespuesta->msj ="Bienvenido ".$datos->nombre;
                    $objRespuesta->Token = $token;
                    return $response->withJson($objRespuesta ,200);
                }
                else 
                {
                    return $response->withJson("Usuario Suspendido",404);
                }
            }
            else
            {
                //Mensaje generico por si hay algun error 
                return $response->withJson("Error en email o clave",404);
            }
        }
        //SI los campos estan vacíos
        else
        {
            return $response->withJson("Falta email y clave",404);
        }
    }

    public function datosToken($request, $response, $args) {
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        try{
			$datosToken = AutentificadorJWT::ObtenerData($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}
		return $response->withJson( $datosToken ,200);

    }

    public static function is_valid_email($str)
    {
      return (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
    }

}