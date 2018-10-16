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

    
}

    
