<?php
include_once "pedido.php";
include_once "mesa.php";
include_once "encuesta.php";
include_once "comanda.php";

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

            $montoViejo = comanda::RetornarPrecioComanda($idComanda); 
            //var_dump($montoViejo[0]);
            $montoNuevo =  $montoViejo[0] + $monto;
            //var_dump($montoNuevo);

            $ok = comanda::CargarPrecioComanda($idComanda, $montoNuevo);
        }

        return $response->withJson("El pedido con se genero correctamente",200);
    }



    /*
    Trae todos los pedidos
    */

    public function traerTodos($request, $response, $args) 
	{
        $todosPedidos = pedido::TraerTodoLosPedidos();
        return $response->withJson($todosPedidos, 200);  
    }

    /*
    Trae todos los pedidos cancelados
    */

    public function traerTodosCancelados($request, $response, $args) 
	{
        $todosPedidos = pedido::TraerTodoLosPedidosCancelados();
        return $response->withJson($todosPedidos, 200);  
    }

    /*
    Trae todos los pedidos demorados
    */

    public function traerTodosDemorados($request, $response, $args) 
	{
        $todosPedidos = pedido::TraerTodoLosPedidosDemorados();
        return $response->withJson($todosPedidos, 200);  
    }


    /*
    TRAER UN PEDIDO
    */

    public function traerUno($request, $response, $args) 
	{

        $ArrayDeParametros = $request->getParsedBody();
        /*
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
            $idPedido= $ArrayDeParametros['idPedido'];
            $pedidoAux = pedido::TraerPedidoID($idPedido);
            if(!$pedidoAux)
            {
                return $response->withJson("No se encontro pedido",404);
            }
            return $response->withJson($pedidoAux, 200);  
        }
        */
        if (!isset($ArrayDeParametros['idPedido'])) {
            return $response->withJson("idPedido no puede esta vacio",404);   
        }
        $idPedido= $ArrayDeParametros['idPedido'];
        $pedidoAux = pedido::TraerPedidoID($idPedido);
        if(!$pedidoAux)
        {
            return $response->withJson("No se encontro pedido",404);
        }
        return $response->withJson($pedidoAux, 200);  
    }



    //en preparación
    public function modificarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        $datosToken = AutentificadorJWT::ObtenerData($token);
    
        if ($datosToken->estado =="suspendido") 
        {
            return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
        }
        if (!isset($ArrayDeParametros['idPedido'])) 
        {
            return $response->withJson('Error al modificar: Debe ingresar ID de pedido',404);
        }
        $idPedido= $ArrayDeParametros['idPedido'];
        $objDelaRespuesta= new stdclass();
        $pedModificar = pedido::TraerPedidoID($idPedido);

        if ($pedModificar != false) 
        {
            $objDelaRespuesta->msj = "se modifico pedido numero ".$idPedido;
    
            if (!isset($ArrayDeParametros['horaEstimada'])) 
            {
                return $response->withJson('Error: hora estimada no puede esta vacio',404);
            }
            
            $hora_estimada = $ArrayDeParametros['horaEstimada'];
            
            if ($hora_estimada == "") 
            {
                return $response->withJson('Error: hora estimada no puede esta vacio',404);
            }

            $ahora=date('Y/m/d G:i'); 
            $tiempo=strtotime($ahora . ' + '. $hora_estimada . 'minutes');
            $tiempoEstimado = date('Y-m-d H:i:s',$tiempo);

            $pedModificar->estado = "en preparacion";
            $pedModificar->horaEstimada = $tiempoEstimado;
            $pedModificar->idEmpleado = $datosToken->id;
            
            $pedModificar->TomarPedidoParametros($idPedido);
        
        }
        else 
        {
            return $response->withJson('Error no existe el numero de pedido',404);
        }
        return $response->withJson($objDelaRespuesta, 202);
            
    }
  
    public function finalizarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        $datosToken = AutentificadorJWT::ObtenerData($token);
    
        if ($datosToken->estado =="suspendido") 
        {
            return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
        }
        if (!isset($ArrayDeParametros['idPedido'])) 
        {
            return $response->withJson('Error al modificar: Debe ingresar ID de pedido',404);
        }
        $idPedido= $ArrayDeParametros['idPedido'];
        $objDelaRespuesta= new stdclass();
        $pedModificar = pedido::TraerPedidoID($idPedido);

        if ($pedModificar != false) 
        {
            $objDelaRespuesta->msj = "se modifico pedido numero ".$idPedido;


            $pedModificar->estado = "listo para servir";
            $pedModificar->horaFinal = date('Y-m-d H:i:s');
            $pedModificar->idEmpleado = $datosToken->id;
            
            $pedModificar->FinalizarPedidoParametros($idPedido);
            $ok = mesa::cambiarEstadoMesa($idMesa,"Con clientes comiendo");
        }
        else 
        {
            return $response->withJson('Error no existe el numero de pedido',404);
        }
        return $response->withJson($objDelaRespuesta, 202);
    }

    public function cancelarUno($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        $datosToken = AutentificadorJWT::ObtenerData($token);
    
        if ($datosToken->estado =="suspendido") 
        {
            return $response->withJson('Esta suspendido, pongase en contacto con el administrador',404);
        }
        if (!isset($ArrayDeParametros['idPedido'])) 
        {
            return $response->withJson('Error al modificar: Debe ingresar ID de pedido',404);
        }
        $idPedido= $ArrayDeParametros['idPedido'];
        $objDelaRespuesta= new stdclass();
        $pedModificar = pedido::TraerPedidoID($idPedido);

        if ($pedModificar != false) 
        {
            $objDelaRespuesta->msj = "se modifico pedido numero ".$idPedido;


            $pedModificar->estado = "cancelado";
            $pedModificar->horaFinal = date('Y-m-d H:i:s');
            $pedModificar->idEmpleado = $datosToken->id;
            
            $pedModificar->FinalizarPedidoParametros($idPedido);
        }
        else 
        {
            return $response->withJson('Error no existe el numero de pedido',404);
        }
        return $response->withJson($objDelaRespuesta, 202);
            
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


    public function TiempoRestante($request, $response, $args)
    {
        $respuesta=new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
        $idMesa=$ArrayDeParametros['codigoMesa'];
        $idComanda=$ArrayDeParametros['idComanda'];

        $auxPedido = pedido::TraerPedidoMasTarde($idComanda);
        var_dump($auxPedido);

    }


         




        /*
        FUNCIONES ENCUESTAS
        */
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

        /*
        FUNCIONES VALIDACION
        */

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
}