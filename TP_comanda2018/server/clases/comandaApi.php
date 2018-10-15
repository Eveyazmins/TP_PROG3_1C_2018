<?php
include_once "pedido.php";
include_once "mesa.php";
include_once "encuesta.php";
include_once "comanda.php";

class comandaApi
{
    public function crearComanda($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
		$token=$arrayConToken[0];
		//$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkMTE1NDk3MzcxMWIxMzU5ODVkYjVlNzA0NTI5Nzk0ODVlMjE0Yzg4IiwiZGF0YSI6eyJpZCI6MjMsIm5vbWJyZSI6InVzdWFyaW9Vbm8iLCJzZXhvIjoibWFzY3VsaW5vIiwiZW1haWwiOiJ1c2VyQHVzZXIuY29tIiwidHVybm8iOiJtYW5pYW5hIiwicGVyZmlsIjoidXNlciIsImZvdG8iOiJmb3Rvc0VtcGxlYWRvc1wvdXN1YXJpb1Vuby5wbmciLCJhbHRhIjoiMjAxNy0xMi0xOCAxNTo0NDozNCIsImVzdGFkbyI6ImFjdGl2byJ9LCJhcHAiOiJBUEkgUkVTVCBUUC1Fc3RhY2lvbmFtaWVudG8ifQ.Hl41g_LiwUdnL_l5eOSaxgSbEzBDoibnvXvPFq0rgT0";
		$datosToken = AutentificadorJWT::ObtenerData($token);

        if ($datosToken->estado =="suspendido") 
        {
             return $response->withJson("Esta suspendido, pongase en contacto con el administrador",404);
        }
        else 
        {
            if(!isset($ArrayDeParametros['idMesa'])) 
            {
                return $response->withJson("Mesa no puede esta vacio",404);   
            }
            //validar solo numero
            $idMesa = $ArrayDeParametros['idMesa'];

            if(!isset($ArrayDeParametros['cliente'])) 
            {
                return $response->withJson("Cliente no puede esta vacio",404);   
            }

            $cliente= strtolower($ArrayDeParametros['cliente']);

            //validar nombre

            //valida si los campos son ""

            if ($idMesa == "" or $cliente == "")
            {
                return $response->withJson("Completar todos los campos obligatorios",404); 
            }  

            $idMozo = $datosToken->id;

            $comandaAux = new comanda();

            $comandaAux->idMesa = $idMesa;
            $comandaAux->monto = '0';
            $comandaAux->estado = 'abierto';
            $comandaAux->cliente = $cliente;
            $comandaAux->fecha = date("Y-m-d");
            $comandaAux->idMozo = $idMozo;

            $archivos = $request->getUploadedFiles();
            $destino="./fotosComanda/";
       
            if(isset($archivos['foto']))
            {
                $nombreAnterior=$archivos['foto']->getClientFilename();
                $extension= explode(".", $nombreAnterior)  ;
                //var_dump($nombreAnterior);
                $extension=array_reverse($extension);
                $archivos['foto']->moveTo($destino.$cliente.".".$extension[0]);
                $comandaAux->foto = $destino.$cliente.".".$extension[0];
            } 
            else
            {
                $comandaAux->foto = "sin foto";
            }
            
            $comandaAux->InsertarComandaParametros();
        }
        return $response->withJson("La comanda se genero correctamente",200);
    }

    public function traerTodos($request, $response, $args) 
	{
        $todasComandas = comanda::TraerTodoLosPedidos();
        return $response->withJson($todasComandas, 200);  
    }



}