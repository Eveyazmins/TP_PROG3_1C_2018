<?php
include_once "pedido.php";
include_once "mesa.php";
include_once "encuesta.php";
include_once "comanda.php";
include_once "mesa.php";

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

            $usosViejo = mesa::RetornarUsosMesa($idMesa); 
            $usosNuevo =  $usosViejo[0] + 1;            

            $ok = mesa::CargarUsoMesa($idMesa,$usosNuevo);
            $ok = mesa::cambiarEstadoMesa($idMesa,"Con clientes esperando pedido");
        }
        return $response->withJson("La comanda se genero correctamente",200);
    }

    /*
    TRAER TODAS LAS COMANDAS
    */

    public function traerTodas($request, $response, $args) 
	{
        $todasComandas = comanda::TraerTodasLasComandas();
        return $response->withJson($todasComandas, 200);  
    }

    
    /*
    TRAER TODOS LOS PEDIDOS DE COMANDA
    */

    public function traerPedidosComanda($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['id']))
        {
            return $response->withJson("Ingrese ID de comanda!", 400);
        }
        $id = $ArrayDeParametros['id'];

        $todosPedidos = comanda::TraerTodosPedidosComanda($id);

        return $response->withJson($todosPedidos, 200); 
    }

    /*
    TRAER UNA COMANDA POR ID
    */

    public function traerUna($request, $response, $args) 
	{
        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['id']))
        {
            return $response->withJson("Ingrese ID de comanda !", 400);
        }
        
        $id = $ArrayDeParametros['id'];

        if($id == "")
        {
            return $response->withJson("Ingrese ID valido",404);
        }

        $comandaBuscada = comanda::TraerComandaID($id);

        if(!$comandaBuscada)
        {
            return $response->withJson("Comanda no existe",404);
        }
        
        return $response->withJson($comandaBuscada, 200);  
        
    }

    public function cerrarComanda($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();

        if (!isset($ArrayDeParametros['id']))
        {
            return $response->withJson("Ingrese ID de comanda !", 400);
        }

        $id = $ArrayDeParametros['id'];

        if($id == "")
        {
            return $response->withJson("Ingrese ID de comanda !", 400);
        }

        $comandaBuscada = comanda::TraerComandaID($id);

        if(!$comandaBuscada)
        {
            return $response->withJson("Comanda no existe",404);
        }

        if($comandaBuscada->estado == "cerrado")
        {
            return $response->withJson("Comanda ya se encuentra cerrada",404);
        }

        $auxMesa = $comandaBuscada->idMesa;
        comanda::cerrarComandaParametros($id);
        mesa::cambiarEstadoMesa($auxMesa,"cerrada");

        return $response->withJson("Se cerro comanda ".$comandaBuscada->id, 200);
    }

    public function MesaMasFacturo($request, $response, $args)
    {
        $masFactura = comanda::TraerMesaMasFacturoParametros();
        return $response->withJson($masFactura, 200);
    }

    public function MesaMenosFacturo($request, $response, $args)
    {
        $menosFactura = comanda::TraerMesaMenosFacturoParametros();
        return $response->withJson($menosFactura, 200);
    }

    public function MesaMasImporte($request, $response, $args)
    {
        $masImporte = comanda::TraerMesaMayorImporte();
        return $response->withJson($masImporte, 200);
    }

    public function MesaMenosImporte($request, $response, $args)
    {
        $menosImporte = comanda::TraerMesaMenorImporte();
        return $response->withJson($menosImporte, 200);
    }

    public function TraerFacturadoDesdeHasta($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        
        if (!isset($ArrayDeParametros['idMesa']))
        {
            return $response->withJson("Error: Mesa no puede estar vacio!",404);
        }

        $idmesa = $ArrayDeParametros['idMesa'];

        if ($mesaAux = mesa::TraerMesaId($idmesa))
        {
            $objDelaRespuesta->mesa=$mesaAux->id;

            if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
            {
                $desde= $ArrayDeParametros['desde'];

                $hasta= $ArrayDeParametros['hasta'];

                if (($desde > $hasta) and ($hasta != "")) 
                {
                    return $response->withJson("Error: inconsistencia de fechas!",404);
                }
                
                $objDelaRespuesta->facturados = comanda::FacturadoDesdeHasta($mesaAux->id,$desde,$hasta);
            }

            if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) 
                {
                    $desde= $ArrayDeParametros['desde'];
                    
                    $objDelaRespuesta->facturados = comanda::FacturadoDesdeHasta($mesaAux->id,$desde,"");
                }
                if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
                {
                    $hasta= $ArrayDeParametros['hasta'];
                
                    $objDelaRespuesta->facturados = comanda::FacturadoDesdeHasta($mesaAux->id,"",$hasta);
                }
                if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta']))
                {
                    $objDelaRespuesta->facturados = comanda::FacturadoDesdeHasta($mesaAux->id,"","");
                }
                
                return $response->withJson($objDelaRespuesta,200);
            }
            else
            {
                return $response->withJson("La mesa no existe ",206);
            }
    }



    //FUNCIONES ENCUESTA

    public function CargarEncuesta($request, $response, $args)
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

        if (!isset($ArrayDeParametros['idComanda'])) {
            return $response->withJson("idComanda no puede esta vacio",404);   
        }
        //validar numero
        $idPedido= $ArrayDeParametros['idComanda'];

        if($idPedido == "")
        {
            return $response->withJson("id comanda no puede esta vacio",404);
        }

        if (!isset($ArrayDeParametros['idMesa'])) {
            return $response->withJson("Mesa no puede esta vacio",404);   
        }
        //validar numero
        $idMesa= $ArrayDeParametros['idMesa'];
        if($idMesa == "")
        {
            return $response->withJson("Mesa no puede esta vacio",404);
        }

        $encuestaAux = new encuesta();

        $encuestaAux->idMesa = $idMesa;
        $encuestaAux->idPedido = $idPedido;
        $encuestaAux->estado = 'en cliente';
        $encuestaAux->puntosMesa = 0;
        $encuestaAux->puntosRestaurante = 0;
        $encuestaAux->puntosMozo = 0;
        $encuestaAux->puntosCocinero = 0;
        $encuestaAux->comentario = "pendiente";

        $encuestaAux->insertarEncuestaParametros();

        $ok = mesa::cambiarEstadoMesa($idMesa,"Con clientes pagando");

        return $response->withJson("Se cargo la encuesta",200);
    }


    public function finalizarEncuesta($request, $response, $args)
    {

        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['idComanda'])) {
            return $response->withJson("Comanda no puede esta vacio",404);   
        }
        $idComanda= $ArrayDeParametros['idComanda'];

        if (!isset($ArrayDeParametros['idMesa'])) {
            return $response->withJson("Mesa no puede esta vacio",404);   
        }

        $idMesa= $ArrayDeParametros['idMesa'];

        $encuestaAux = encuesta::TraerEncuestaPendiente($idComanda,$idMesa);

        if(!$encuestaAux)
        {
            return $response->withJson("La encuesta no existe",404); 
        }

        if (!isset($ArrayDeParametros['puntosMesa'])) {
            return $response->withJson("puntosMesa no puede esta vacio",404);   
        }
        $puntosMesa= $ArrayDeParametros['puntosMesa'];

        if (!isset($ArrayDeParametros['puntosRestaurante'])) {
            return $response->withJson("puntosrRestaurante no puede esta vacio",404);   
        }
        $puntosRestaurante= $ArrayDeParametros['puntosRestaurante'];

        if (!isset($ArrayDeParametros['puntosMozo'])) {
            return $response->withJson("puntosMozo no puede esta vacio",404);   
        }
        $puntosMozo= $ArrayDeParametros['puntosMozo'];

        if (!isset($ArrayDeParametros['puntosCocinero'])) {
            return $response->withJson("puntosCocinero no puede esta vacio",404);   
        }
        $puntosCocinero= $ArrayDeParametros['puntosCocinero'];

        if (!isset($ArrayDeParametros['comentario'])) {
            return $response->withJson("comentario no puede esta vacio",404);   
        }
        $comentario= strtolower($ArrayDeParametros['comentario']);
    
        
        $encuestaAux->estado = "finalizado";
        $encuestaAux->puntosMesa = $puntosMesa;
        $encuestaAux->puntosRestaurante = $puntosRestaurante;
        $encuestaAux->puntosMozo = $puntosMozo;
        $encuestaAux->puntosCocinero = $puntosCocinero;
        $encuestaAux->comentario = $comentario;
        $encuestaAux->completarEncuesta($idComanda,$idMesa);
        
        return $response->withJson("Gracias por responder la encuesta",200);
    }
    

    public function traerTodasEncuestas($request, $response, $args) 
    {
        $todosencuestas = encuesta::TraerTodasEncuestasPendientes();
        return $response->withJson($todosencuestas, 200);  

    }

    public function TraerEncuesta($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['idComanda'])) {
            return $response->withJson("comanda no puede esta vacio",404);   
        }
        $idComanda= $ArrayDeParametros['idComanda'];
        
        if (!isset($ArrayDeParametros['idMesa'])) {
            return $response->withJson("Mesa no puede esta vacio",404);   
        }
        $idMesa= $ArrayDeParametros['idMesa'];

        $todosPedidos = encuesta::TraerEncuestaPendiente($idComanda,$idMesa);
        return $response->withJson($todosPedidos, 200); 
    }


    public function mejoresComentarios($request, $response, $args)
    {
        $comentarios = encuesta::traerMejoresComentarios();
        return $response->withJson($comentarios, 200); 
    }

    public function peoresComentarios($request, $response, $args)
    {
        $comentarios = encuesta::traerPeoresComentarios();
        return $response->withJson($comentarios, 200); 
    }



    
}

    
