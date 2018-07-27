<?php
include_once "Pedido.php";
include_once "Detalle.php";
include_once "Mesa.php";
include_once "AutentificadorJWT.php";

class PedidoApi {
    
#FUNCIONES API ------------------------------------------------------------------------------------

public static function IngresarPedido($request, $response, $args) 
{
    $objDelaRespuesta= new stdclass();
    $ArrayDeParametros = $request->getParsedBody();
    $idMesa= $ArrayDeParametros['idMesa'];
    $pedido= $ArrayDeParametros['pedido'];
    $tiempoInicio= date('Y/m/d G:i,s');
    $miMesa=Mesa::TraerUnaMesa($idMesa);
    $miMesa->estado="Cliente esperando pedido";
    $miMesa->cantidad++;
    $miMesa->ModificarMesa();

    $archivos = $request->getUploadedFiles();
    $destino="./fotos/";
    $logo="logo.png";
    
    $nombreAnterior=$archivos['foto']->getClientFilename();
    $extension= explode(".", $nombreAnterior)  ;
    $extension=array_reverse($extension);
    
    $ultimoDestinoFoto=$destino.$idMesa.".".$extension[0];
    if(file_exists($ultimoDestinoFoto))
    {
        copy($ultimoDestinoFoto,"./backup/".date("Ymd").$idMesa.".".$extension[0]);
    }
    $archivos['foto']->moveTo($ultimoDestinoFoto);
    
    $nuevoPedido= new Pedido();
    $nuevoPedido->idMesa=$idMesa;
    $nuevoPedido->horaPedido=$tiempoInicio;
    $nuevoPedido->fotoMesa=$ultimoDestinoFoto;   
    $idPedido=$nuevoPedido->GuardarPedido();

    $arrayDetalle=explode(",",$pedido);
    
    for($i=0 ; $i < count($arrayDetalle) - 1; $i++)
    {
        $detallePedido=new Detalle();
        $detallePedido->idPedido=$idPedido;
        $detallePedido->producto=$arrayDetalle[$i];
        $detallePedido->estado="pendiente";
        
        if ($arrayDetalle[$i]=='trago'|| $arrayDetalle[$i]=='vino'|| $arrayDetalle[$i]=='gaseosa')
        {
            $detallePedido->sector="barra";
        }
        if($arrayDetalle[$i]=='pizza'|| $arrayDetalle[$i]=='empanadas' || $arrayDetalle[$i]=='plato')
        {
            $detallePedido->sector="cocina";
        }
        if($arrayDetalle[$i]=='cerveza')
        {
            $detallePedido->sector="chopera";
        }
        if($arrayDetalle[$i]=='postre')
        {
            $detallePedido->sector="candy bar";
        }
                
        $detallePedido->GuardarDetalle();
    }

    $objDelaRespuesta->idPedido= $idPedido;
    return $response->withJson($objDelaRespuesta, 200);
    }

    public static function TraerPendientesEmpleado($request, $response, $args)
    {
        $objDelaRespuesta=new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
        $token=$ArrayDeParametros['token'];
        $payload=AutentificadorJWT::ObtenerData($token);
        $idEmpleado=$payload->idEmpleado;
        $objDelaRespuesta=Detalle::TraerPendientes($idEmpleado);
    
        return $response->withJson($objDelaRespuesta, 200);
    }

    public static function PrepararPedido($request, $response, $args)
    {
        /*Se envía como parámetro el token. 
        En la variable Payload se decodifica el token y se obtienen los datos del empleado.
        Se guarda el ID de empleado en variable.
        
        */

        $respuesta=new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
        $token=$ArrayDeParametros['token'];
        $payload=AutentificadorJWT::ObtenerData($token);
        $idEmpleado=$payload->idEmpleado;
        $idDetalle=$ArrayDeParametros['idDetalle'];
        $tiempoPreparacion=$ArrayDeParametros['tiempoPreparacion'];
        $tiempoPreparacion=$tiempoPreparacion;
        $ahora=date('Y/m/d G:i'); 
        $tiempo=strtotime($ahora. ' + '. $tiempoPreparacion . 'minutes');
        $miDetalle=new Detalle();
        $miDetalle->idDetalle=$idDetalle;
        $miDetalle->tiempoPreparacion=date('Y/m/d G:i',$tiempo);
        $miDetalle->idEmpleado=$idEmpleado;
        $miDetalle->estado="en preparacion";
        $respuesta=$miDetalle->PrepararDetalle();
       
        return $response->withJson($respuesta,200);
    
    }

    public static function ServirPedido($request, $response, $args)
    {
        $respuesta=new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
        $idDetalle=$ArrayDeParametros['idDetalle'];
        $tiempoServido=date('Y/m/d G:i');
        $miDetalle=new Detalle();
        $miDetalle->idDetalle=$idDetalle;
        $miDetalle->tiempoServido=$tiempoServido;
        $respuesta=$miDetalle->ServirDetalle();
       
        return $response->withJson($respuesta,200);
    }

    public static function TiempoRestante($request, $response, $args)
    {
        $respuesta=new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
        $idMesa=$ArrayDeParametros['idMesa'];
        $idPedido=$ArrayDeParametros['idPedido'];
        $detalles=Detalle::TraerDetalleDelPedido($idPedido);
    
        $ahora=date('Y/m/d G:i'); 
    
        $arrayRespuesta=array();
    
        foreach($detalles as $det)
        {
            if($det->estado=="en preparacion")
            {
                $tprep=strtotime($det->tiempoPreparacion);
                $now=strtotime($ahora);
                $tiempoRestante=$tprep-$now;
                $detallesRespuesta->idDetalle=$det->idDetalle;
                $detallesRespuesta->producto=$det->producto;
                $detallesRespuesta->tiempoRestante=date('i:s',$tiempoRestante);
                array_push($arrayRespuesta,$detallesRespuesta);
            }
        }
        
        $respuesta->pedido=$idPedido;
        $respuesta->detalles=$arrayRespuesta;
    
        return $response->withJson($respuesta,200);
    }


    public static function CancelarPedido($request, $response, $args)
    {
        $respuesta=new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
        $idPedido=$ArrayDeParametros['idPedido'];
        $respuesta= Detalle::CancelarDetalles($idPedido);
        return $response->withJson($respuesta,200);
    }

    public static function TraerCancelados($request, $response, $args)
    {
        $respuesta=new stdclass();
        $respuesta= Pedido::PedidosCancelados();
        return $response->withJson($respuesta,200);
    }




}