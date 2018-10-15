<?php
include_once "pedido.php";
include_once "mesa.php";
include_once "encuesta.php";

class pedidoApi
{

    public function crearPedido($request, $response, $args)
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
            if(!isset($ArrayDeParametros['idComanda'])) 
            {
                return $response->withJson("Comanda no puede esta vacio",404);   
            }

            if(!isset($ArrayDeParametros['idProducto']))
            {
                return $response->withJson("Producto no puede esta vacio",404);   
            }

            if (!isset($ArrayDeParametros['cantidad']))
            {
                return $response->withJson("Cantidad no puede esta vacio",404);   
            }


            $idComanda= $ArrayDeParametros['idComanda'];
            $idProducto = $ArrayDeParametros['idProducto'];
            $cantidad = $ArrayDeParametros['cantidad'];

            if (!isset($ArrayDeParametros['tipo'])) 
            {
                return $response->withJson("Tipo no puede esta vacio",404);   
            }
            $tipo= strtolower($ArrayDeParametros['tipo']);
    
            if ($this->validarNombre($tipo) == false) 
            {
                return $response->withJson("Error: tipo solo puede contener letras",404);
            }

            if (!isset($ArrayDeParametros['monto'])) 
            {
                return $response->withJson("Monto no puede esta vacio",404);   
            }

            $monto= $ArrayDeParametros['monto'];
            /*

            if ($this->validarMonto() == false) 
            {
                return $response->withJson("Error: Ingrese monto valido",404);
            }
*/

            if ($idComanda == "" or $idProducto == "" or $cantidad == "" or $tipo == "" or $monto == "")
            {
                return $response->withJson("Completar todos los campos obligatorios",404); 

            }    
            $pedidoAux = new pedido();

            $pedidoAux->idComanda = $idComanda;
            $pedidoAux->idProducto = $idProducto;
            $pedidoAux->cantidad = $cantidad;
            $pedidoAux->tipo = $tipo;
            $pedidoAux->horaEstimada = "pendiente";
            $pedidoAux->horaFinal = "pendiente";
            $pedidoAux->estado = "pendiente";
            $pedidoAux->idEmpleado = 0;
            $pedidoAux->monto = $monto;
            $pedidoAux->fecha = date("Y-m-d");
            
            $pedidoAux->InsertarPedidoParametros();

        }
        return $response->withJson("El pedido con se genero correctamente",200);
    }

    public function validarNombre($cadena){ 
        $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ "; 
        for ($i=0; $i<strlen($cadena); $i++){ 
            if (strpos($permitidos, substr($cadena,$i,1))===false){ 
            //no es válido; 
            return false; 
            } 
        }  
        //si estoy aqui es que todos los caracteres son validos 
        return true; 
    }

    public function validarAlfanum($cadena){ 
        $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 "; 
        for ($i=0; $i<strlen($cadena); $i++){ 
            if (strpos($permitidos, substr($cadena,$i,1))===false){ 
            //no es válido; 
            return false; 
            } 
        }  
        //si estoy aqui es que todos los caracteres son validos 
        return true; 
    }

    public function validarMonto($cadena){ 
        $permitidos = "0123456789,"; 
        for ($i=0; $i<strlen($cadena); $i++){ 
            if (strpos($permitidos, substr($cadena,$i,1))===false){ 
            //no es válido; 
            return false; 
            } 
        }  
        //si estoy aqui es que todos los caracteres son validos 
        return true; 
    }

    /*
    Trae todos los pedidos
    */

    public function traerTodos($request, $response, $args) 
	{
        $todosPedidos = pedido::TraerTodoLosPedidos();
        return $response->withJson($todosPedidos, 200);  
    }

    
    public function traerTodosConEstadoMesa($request, $response, $args) 
	{
        $todosPedidos = pedido::TraerTodoLosPedidosConEstadoMesa();
        return $response->withJson($todosPedidos, 200);  
    }

    public function traerUno($request, $response, $args) 
	{
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
		$token=$arrayConToken[0];
		//$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJkMTE1NDk3MzcxMWIxMzU5ODVkYjVlNzA0NTI5Nzk0ODVlMjE0Yzg4IiwiZGF0YSI6eyJpZCI6MjMsIm5vbWJyZSI6InVzdWFyaW9Vbm8iLCJzZXhvIjoibWFzY3VsaW5vIiwiZW1haWwiOiJ1c2VyQHVzZXIuY29tIiwidHVybm8iOiJtYW5pYW5hIiwicGVyZmlsIjoidXNlciIsImZvdG8iOiJmb3Rvc0VtcGxlYWRvc1wvdXN1YXJpb1Vuby5wbmciLCJhbHRhIjoiMjAxNy0xMi0xOCAxNTo0NDozNCIsImVzdGFkbyI6ImFjdGl2byJ9LCJhcHAiOiJBUEkgUkVTVCBUUC1Fc3RhY2lvbmFtaWVudG8ifQ.Hl41g_LiwUdnL_l5eOSaxgSbEzBDoibnvXvPFq0rgT0";
		$datosToken = AutentificadorJWT::ObtenerData($token);

		if ($datosToken->estado =="suspendido") {
             return $response->withJson("Esta suspendido, pongase en contacto con el administrador",404);
		}
		else {
            if (!isset($ArrayDeParametros['idPedido'])) {
                return $response->withJson("idPedido no puede esta vacio",404);   
            }
            $idPedido= strtolower($ArrayDeParametros['idPedido']);
            $todosPedidos = pedido::TraerPedidoIDConEstadoMesa($idPedido);
            return $response->withJson($todosPedidos, 200);  
        }


    }
    //en preparación
    public function modificarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody();
            $arrayConToken = $request->getHeader('token');
            $token=$arrayConToken[0];
            $datosToken = AutentificadorJWT::ObtenerData($token);
    
            if ($datosToken->estado =="suspendido") {
                 return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
            }
            if (!isset($ArrayDeParametros['idPedido'])) {
                return $response->withJson('Error al modificar: Debe ingresar ID de pedido',404);
            }
            $idPedido= $ArrayDeParametros['idPedido'];
            $objDelaRespuesta= new stdclass();
            $pedModificar = pedido::TraerPedidoID($idPedido);

            if ($pedModificar != false) {
                $objDelaRespuesta->msj = "se modifico pedido numero ".$idPedido;
                if (isset($ArrayDeParametros['estadoBar'])&& isset($ArrayDeParametros['tiempo_estimado_bar'])) {
                    $estadoBar = strtolower($ArrayDeParametros['estadoBar']);
                    $tiempo_estimado_bar = $ArrayDeParametros['tiempo_estimado_bar'];

                    $pedModificar->estadoBar = $estadoBar;
                    $pedModificar->tiempo_estimado_bar = $tiempo_estimado_bar;
                    $pedModificar->idEmpladoBar = $datosToken->id;
                    if ($pedModificar->estadoBar== "" || !isset($pedModificar->estadoBar)) {
                        return $response->withJson('Error: estadoBar no puede esta vacio',404);
                    }
                    if ($pedModificar->tiempo_estimado_bar== "" || !isset($pedModificar->tiempo_estimado_bar)) {
                        return $response->withJson("Error: tiempo_estimado_bar no puede esta vacio",404);
                    }
                    $pedModificar->modificarBarID($idPedido);
                    $objDelaRespuesta->estadoBar =$estadoBar;
                }
                if (isset($ArrayDeParametros['estadoCer']) && isset($ArrayDeParametros['tiempo_estimado_cer'])) {
                    $estadoCer = strtolower($ArrayDeParametros['estadoCer']);
                    $tiempo_estimado_cer = $ArrayDeParametros['tiempo_estimado_cer'];

                    $pedModificar->estadoCer = $estadoCer;
                    $pedModificar->tiempo_estimado_cer = $tiempo_estimado_cer;
                    $pedModificar->idEmpladoCer = $datosToken->id;
                    if ($pedModificar->estadoCer== "" || !isset($pedModificar->estadoCer)) {
                        return $response->withJson("Error: estadoCer no puede esta vacio",404);
                    }
                    if ($pedModificar->tiempo_estimado_cer== "" || !isset($pedModificar->tiempo_estimado_cer)) {
                        return $response->withJson("Error: tiempo_estimado_cer no puede esta vacio",404);
                    }
                    $pedModificar->modificarCerID($idPedido);
                    $objDelaRespuesta->estadoCer =$estadoCer;
                }
                if (isset($ArrayDeParametros['estadoCoc']) && isset($ArrayDeParametros['tiempo_estimado_coc'])) {
                    $estadoCoc = strtolower($ArrayDeParametros['estadoCoc']);
                    $tiempo_estimado_coc = $ArrayDeParametros['tiempo_estimado_coc'];

                    $pedModificar->estadoCoc = $estadoCoc;
                    $pedModificar->tiempo_estimado_coc = $tiempo_estimado_coc;
                    $pedModificar->idEmpladoCoc = $datosToken->id;
                    if ($pedModificar->estadoCoc== "" || !isset($pedModificar->estadoCoc)) {
                        return $response->withJson('Error: estadoCoc no puede esta vacio',404);
                    }
                    if ($pedModificar->tiempo_estimado_coc== "" || !isset($pedModificar->tiempo_estimado_coc)) {
                        return $response->withJson("Error: tiempo_estimado_coc no puede esta vacio",404);
                    }
                    $pedModificar->modificarCocID($idPedido);
                    $objDelaRespuesta->estadoCoc =$estadoCoc;
                }

            }
            else {
                return $response->withJson('Error no existe el numero de pedido',404);
            }
            return $response->withJson($objDelaRespuesta, 202);
            
    }
    
    public function finalizarPedido($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        $datosToken = AutentificadorJWT::ObtenerData($token);

        if ($datosToken->estado =="suspendido") {
             return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
        }
        if (!isset($ArrayDeParametros['idPedido'])) {
            return $response->withJson('Error al finalizar: Debe ingresar ID de pedido',404);
        }
        $idPedido= $ArrayDeParametros['idPedido'];
        $objDelaRespuesta= new stdclass();
        $pedFinalizar = pedido::TraerPedidoID($idPedido);
        if (isset($ArrayDeParametros['estadoCoc'])) {
            $tiempo_final_coc =$this->calculoTiempo($pedFinalizar->tiempo_estimado_coc);
            $estadoCoc = $ArrayDeParametros['estadoCoc'];
            $pedFinalizar->estadoCoc = $estadoCoc;
            $pedFinalizar->tiempo_final_coc = $tiempo_final_coc;
            //echo ($pedFinalizar->tiempo_final_coc);
            $pedFinalizar->finalizarCocinarID($idPedido);
            return $response->withJson('El pedido de cocina esta listo para servir',200);

        }
        if (isset($ArrayDeParametros['estadoBar'])) {
            $tiempo_final_bar =$this->calculoTiempo($pedFinalizar->tiempo_estimado_bar);
            $estadoBar = $ArrayDeParametros['estadoBar'];
            $pedFinalizar->estadoBar = $estadoBar;
            $pedFinalizar->tiempo_final_bar = $tiempo_final_bar;
            //echo ($pedFinalizar->tiempo_final_bar);
            $pedFinalizar->finalizarBartenderID($idPedido);
            return $response->withJson('El pedido de bartender esta listo para servir',200);

        }
        if (isset($ArrayDeParametros['estadoCer'])) {
            $tiempo_final_cer =$this->calculoTiempo($pedFinalizar->tiempo_estimado_cer);
            $estadoCer = $ArrayDeParametros['estadoCer'];
            $pedFinalizar->estadoCer = $estadoCer;
            $pedFinalizar->tiempo_final_cer = $tiempo_final_cer;
            //echo ($pedFinalizar->tiempo_final_cer);
            $pedFinalizar->finalizarCerveceriaID($idPedido);
            return $response->withJson('El pedido de cerveceria esta listo para servir',200);
        }

        return $response->withJson('No se modifico ningun pedido',404);

    }
    
    public function calculoTiempo($tiempoPedido){
        (int)$tiempoEstimado = $tiempoPedido;
        $ahora= (int)date("i");
        return $resultado = $tiempoEstimado - $ahora; 
    }
    public function tiempoEstimado($request, $response, $args){
        $ArrayDeParametros = $request->getParsedBody();
        if (!isset($ArrayDeParametros['idPedido'])) {
            return $response->withJson('Error al finalizar: Debe ingresar ID de pedido',404);
        }
        $objDelaRespuesta= new stdclass();
        $idPedido= $ArrayDeParametros['idPedido'];
        $pedFinalizar = pedido::TraerPedidoID($idPedido);
        $ahora= (int)date("i");
        
        if ($pedFinalizar->tiempo_estimado_bar != NULL) {
            $objDelaRespuesta->tiempoBar = $pedFinalizar->tiempo_estimado_bar - $ahora;
        }
        else {
            $objDelaRespuesta->tiempoBar ="Sin pedido";
        }
        if ($pedFinalizar->tiempo_estimado_coc != NULL) {
            $objDelaRespuesta->tiempoCoc = $pedFinalizar->tiempo_estimado_coc - $ahora;
        }
        else {
            $objDelaRespuesta->tiempoCoc ="Sin pedido";
        }
        if ($pedFinalizar->tiempo_estimado_cer != NULL) {
            $objDelaRespuesta->tiempoCer = $pedFinalizar->tiempo_estimado_cer - $ahora;
        }
        else {
            $objDelaRespuesta->tiempoCer ="Sin pedido";
        }
        return $response->withJson($objDelaRespuesta,200);
    }

        //return $resultado = $tiempoEstimado - $ahora;


    public function cambiarEstadoPedido($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        $datosToken = AutentificadorJWT::ObtenerData($token);

        if ($datosToken->estado =="suspendido") {
             return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
        }
        if (!isset($ArrayDeParametros['idPedido'])) {
            return $response->withJson('Error al finalizar: Debe ingresar ID de pedido',404);
        }
        $idPedido= $ArrayDeParametros['idPedido'];
        $objDelaRespuesta= new stdclass();
        $pedFinalizar = pedido::TraerPedidoID($idPedido);
        if (isset($ArrayDeParametros['estado'])) {
            $estado = $ArrayDeParametros['estado'];
            //$pedFinalizar->estado = $estado;
            //$pedFinalizar->cambiarEstadoPedidoGlobal($idPedido);
            mesa::ocuparMesa($pedFinalizar->nroMesa,$estado);
            if ($estado == "libre") {
                //cambiarTodosEstadoSector
                pedido::cambiarTodosEstadoSector($idPedido,"Finalizado");
                encuesta::altaEncuesta($idPedido,$pedFinalizar->nroMesa,"Pendiente");
            }
            return $response->withJson('El pedido ya esta servido',200);
        }
        return $response->withJson('No se modifico ningun pedido general',404);
    }

    //operacionesSector
    public function operacionesSector($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        $datosToken = AutentificadorJWT::ObtenerData($token);

        if ($datosToken->estado =="suspendido") {
             return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
        }
        $objDelaRespuesta= new stdclass();

        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];

			$hasta= $ArrayDeParametros['hasta'];

            $objDelaRespuesta->estadoBar= pedido::TraerCantidadOperacionesSectorFechas("estadoBar",$desde,$hasta);
            $objDelaRespuesta->estadoCoc= pedido::TraerCantidadOperacionesSectorFechas("estadoCoc",$desde,$hasta);
            $objDelaRespuesta->estadoCer= pedido::TraerCantidadOperacionesSectorFechas("estadoCer",$desde,$hasta);
            return $response->withJson($objDelaRespuesta, 200); 
            

        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
				$desde= $ArrayDeParametros['desde'];

                $objDelaRespuesta->estadoBar= pedido::TraerCantidadOperacionesSectorFechas("estadoBar",$desde,"");
                $objDelaRespuesta->estadoCoc= pedido::TraerCantidadOperacionesSectorFechas("estadoCoc",$desde,"");
                $objDelaRespuesta->estadoCer= pedido::TraerCantidadOperacionesSectorFechas("estadoCer",$desde,"");
                return $response->withJson($objDelaRespuesta, 200); 

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
				$hasta= $ArrayDeParametros['hasta'];


                $objDelaRespuesta->estadoBar= pedido::TraerCantidadOperacionesSectorFechas("estadoBar","",$hasta);
                $objDelaRespuesta->estadoCoc= pedido::TraerCantidadOperacionesSectorFechas("estadoCoc","",$hasta);
                $objDelaRespuesta->estadoCer= pedido::TraerCantidadOperacionesSectorFechas("estadoCer","",$hasta);
                return $response->withJson($objDelaRespuesta, 200); 

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $objDelaRespuesta->estadoBar= pedido::TraerCantidadOperacionesSectorFechas("estadoBar","","");
            $objDelaRespuesta->estadoCoc= pedido::TraerCantidadOperacionesSectorFechas("estadoCoc","","");
            $objDelaRespuesta->estadoCer= pedido::TraerCantidadOperacionesSectorFechas("estadoCer","","");
            return $response->withJson($objDelaRespuesta, 200); 
		}
    }

        //operacionesSector
        public function masPedidos($request, $response, $args)
        {
            $ArrayDeParametros = $request->getParsedBody();
            $arrayConToken = $request->getHeader('token');
            $token=$arrayConToken[0];
            $datosToken = AutentificadorJWT::ObtenerData($token);
    
            if ($datosToken->estado =="suspendido") {
                 return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
            }
            $objDelaRespuesta= new stdclass();
    
            if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
            {
                $desde= $ArrayDeParametros['desde'];
    
                $hasta= $ArrayDeParametros['hasta'];
    
                $objDelaRespuesta->detalleBar= pedido::TraerMasVendidosSectorFechas("detalleBar",$desde,$hasta);
                if ($objDelaRespuesta->detalleBar == false) {
                    $objDelaRespuesta->detalleBar = "Nada";
                }
                $objDelaRespuesta->detalleCoc= pedido::TraerMasVendidosSectorFechas("detalleCoc",$desde,$hasta);
                if ($objDelaRespuesta->detalleCoc == false) {
                    $objDelaRespuesta->detalleCoc = "Nada";
                }
                $objDelaRespuesta->detalleCer= pedido::TraerMasVendidosSectorFechas("detalleCer",$desde,$hasta);
                if ($objDelaRespuesta->detalleCer == false) {
                    $objDelaRespuesta->detalleCer = "Nada";
                }
                return $response->withJson($objDelaRespuesta, 200); 
                
    
            }
            if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
    
                    $objDelaRespuesta->detalleBar= pedido::TraerMasVendidosSectorFechas("detalleBar",$desde,"");
                    if ($objDelaRespuesta->detalleBar == false) {
                        $objDelaRespuesta->detalleBar = "Nada";
                    }
                    $objDelaRespuesta->detalleCoc= pedido::TraerMasVendidosSectorFechas("detalleCoc",$desde,"");
                    if ($objDelaRespuesta->detalleCoc == false) {
                        $objDelaRespuesta->detalleCoc = "Nada";
                    }
                    $objDelaRespuesta->detalleCer= pedido::TraerMasVendidosSectorFechas("detalleCer",$desde,"");
                    if ($objDelaRespuesta->detalleCer == false) {
                        $objDelaRespuesta->detalleCer = "Nada";
                    }
                    return $response->withJson($objDelaRespuesta, 200); 
    
            }
            if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $hasta= $ArrayDeParametros['hasta'];
    
    
                    $objDelaRespuesta->detalleBar= pedido::TraerMasVendidosSectorFechas("detalleBar","",$hasta);
                    if ($objDelaRespuesta->detalleBar == false) {
                        $objDelaRespuesta->detalleBar = "Nada";
                    }
                    $objDelaRespuesta->detalleCoc= pedido::TraerMasVendidosSectorFechas("detalleCoc","",$hasta);
                    if ($objDelaRespuesta->detalleCoc == false) {
                        $objDelaRespuesta->detalleCoc = "Nada";
                    }
                    $objDelaRespuesta->detalleCer= pedido::TraerMasVendidosSectorFechas("detalleCer","",$hasta);
                    if ($objDelaRespuesta->detalleCer == false) {
                        $objDelaRespuesta->detalleCer = "Nada";
                    }
                    return $response->withJson($objDelaRespuesta, 200); 
    
            }
            if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $objDelaRespuesta->detalleBar= pedido::TraerMasVendidosSectorFechas("detalleBar","","");
                $objDelaRespuesta->detalleCoc= pedido::TraerMasVendidosSectorFechas("detalleCoc","","");
                $objDelaRespuesta->detalleCer= pedido::TraerMasVendidosSectorFechas("detalleCer","","");
                return $response->withJson($objDelaRespuesta, 200); 
            }
        }

        public function BorrarUno($request, $response, $args)
        {
            $ArrayDeParametros = $request->getParsedBody();
            $arrayConToken = $request->getHeader('token');
            $token=$arrayConToken[0];
            $datosToken = AutentificadorJWT::ObtenerData($token);
    
            if ($datosToken->estado =="suspendido") {
                 return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
            }
            if (!isset($ArrayDeParametros['idPedido'])) {
                return $response->withJson('Error al finalizar: Debe ingresar ID de pedido',404);
            }
            $idPedido= $ArrayDeParametros['idPedido'];
            $objDelaRespuesta= new stdclass();
            $pedFinalizar = pedido::TraerPedidoID($idPedido);
            if ($pedFinalizar) {
                mesa::ocuparMesa($pedFinalizar->nroMesa,"libre");
                pedido::cambiarTodosEstadoSector($idPedido,"Cancelado");
                return $response->withJson('El pedido fue cancelado',200);
            }
            return $response->withJson('No se cancelo el pedido',404);
        }

                //operacionesSector
        public function usoMesas($request, $response, $args)
        {
            $ArrayDeParametros = $request->getParsedBody();
            $arrayConToken = $request->getHeader('token');
            $token=$arrayConToken[0];
            $datosToken = AutentificadorJWT::ObtenerData($token);
    
            if ($datosToken->estado =="suspendido") {
                 return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
            }
            $objDelaRespuesta= new stdclass();
    
            if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
            {
                $desde= $ArrayDeParametros['desde'];
                $hasta= $ArrayDeParametros['hasta'];
    
                $Mesas= pedido::TraerUsosMesasFechas($desde,$hasta);
                return $response->withJson($Mesas, 200); 
                
    
            }
            if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
    
                    $Mesas= pedido::TraerUsosMesasFechas($desde,"");
                    return $response->withJson($Mesas, 200); 
    
            }
            if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $hasta= $ArrayDeParametros['hasta'];
    
    
                    $Mesas= pedido::TraerUsosMesasFechas("",$hasta);
                    return $response->withJson($Mesas, 200); 
    
            }
            if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $Mesas= pedido::TraerUsosMesasFechas("","");
                return $response->withJson($Mesas, 200); 
            }
        }

        public function facturacionMesas($request, $response, $args)
        {
            $ArrayDeParametros = $request->getParsedBody();
            $arrayConToken = $request->getHeader('token');
            $token=$arrayConToken[0];
            $datosToken = AutentificadorJWT::ObtenerData($token);
    
            if ($datosToken->estado =="suspendido") {
                 return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
            }
            $objDelaRespuesta= new stdclass();
    
            if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
            {
                $desde= $ArrayDeParametros['desde'];
                $hasta= $ArrayDeParametros['hasta'];
    
                $Mesas= pedido::TraerFacturacionMesasFechas($desde,$hasta);
                return $response->withJson($Mesas, 200); 
                
    
            }
            if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
    
                    $Mesas= pedido::TraerFacturacionMesasFechas($desde,"");
                    return $response->withJson($Mesas, 200); 
    
            }
            if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $hasta= $ArrayDeParametros['hasta'];
    
    
                    $Mesas= pedido::TraerFacturacionMesasFechas("",$hasta);
                    return $response->withJson($Mesas, 200); 
    
            }
            if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $Mesas= pedido::TraerFacturacionMesasFechas("","");
                return $response->withJson($Mesas, 200); 
            }
        }

        public function encuesta($request, $response, $args)
        {
            $ArrayDeParametros = $request->getParsedBody();
            if (!isset($ArrayDeParametros['idPedido'])) {
                return $response->withJson("idPedido no puede esta vacio",404);   
            }
            $idPedido= strtolower($ArrayDeParametros['idPedido']);
            if (!isset($ArrayDeParametros['nroMesa'])) {
                return $response->withJson("nroMesa no puede esta vacio",404);   
            }
            $nroMesa= strtolower($ArrayDeParametros['nroMesa']);
    
            $todosPedidos = encuesta::TraerEncuestaPendiente($idPedido,$nroMesa);
            return $response->withJson($todosPedidos, 200); 

        }
        public function finalizarEncuesta($request, $response, $args)
        {

            $ArrayDeParametros = $request->getParsedBody();
            if (!isset($ArrayDeParametros['idPedido'])) {
                return $response->withJson("idPedido no puede esta vacio",404);   
            }
            $idPedido= strtolower($ArrayDeParametros['idPedido']);

            if (!isset($ArrayDeParametros['nroMesa'])) {
                return $response->withJson("nroMesa no puede esta vacio",404);   
            }
            $nroMesa= strtolower($ArrayDeParametros['nroMesa']);

            if (!isset($ArrayDeParametros['puntos_mesa'])) {
                return $response->withJson("puntos_mesa no puede esta vacio",404);   
            }
            $puntos_mesa= strtolower($ArrayDeParametros['puntos_mesa']);

            if (!isset($ArrayDeParametros['puntos_restaurante'])) {
                return $response->withJson("puntos_restaurante no puede esta vacio",404);   
            }
            $puntos_restaurante= strtolower($ArrayDeParametros['puntos_restaurante']);

            if (!isset($ArrayDeParametros['puntos_mozo'])) {
                return $response->withJson("puntos_mozo no puede esta vacio",404);   
            }
            $puntos_mozo= strtolower($ArrayDeParametros['puntos_mozo']);

            if (!isset($ArrayDeParametros['puntos_cocinero'])) {
                return $response->withJson("puntos_cocinero no puede esta vacio",404);   
            }
            $puntos_cocinero= strtolower($ArrayDeParametros['puntos_cocinero']);

            if (!isset($ArrayDeParametros['comentario'])) {
                return $response->withJson("comentario no puede esta vacio",404);   
            }
            $comentario= strtolower($ArrayDeParametros['comentario']);
            $encuestaAux = new pedido();
            $encuestaAux = encuesta::TraerEncuestaPendiente($idPedido,$nroMesa);
            if ($encuestaAux->estado_encuesta == "Pendiente") {
                $encuestaAux->estado_encuesta = "Finalizada";
                $encuestaAux->puntos_mesa = $puntos_mesa;
                $encuestaAux->puntos_restaurante = $puntos_restaurante;
                $encuestaAux->puntos_mozo = $puntos_mozo;
                $encuestaAux->puntos_cocinero = $puntos_cocinero;
                $encuestaAux->comentario = $comentario;
                $encuestaAux->completarEncuesta($idPedido,$nroMesa);
                return $response->withJson("Gracias por responder la encuesta",200);
            }
            return $response->withJson("No se completo la encuesta",404);


        }
        public function traerTodasEncuestas($request, $response, $args) 
        {
            $todosencuestas = encuesta::TraerTodasEncuestaPendiente();
            return $response->withJson($todosencuestas, 200);  
    
        }
}