<?php
include_once "Pedido.php";
include_once "Detalle.php";
include_once "AutentificadorJWT.php";

class PedidoApi {

    /*
    Ingreso como parámetros los siguientes datos:
    *IdMesa
    *Descripcion del pedido (producto:descripción separados por ,) Ej Gaseosa:coca,...
    *Nombre del cliente

    Se guarda la foto (revisar)
    Se crea un detalle por cada producto del pedido. (Varios detalles por pedido)

    */
    
    public static function IngresarPedido($request, $response, $args) 
    {
        $objDelaRespuesta= new stdclass();
        $ArrayDeParametros = $request->getParsedBody();
      
        $idMesa= $ArrayDeParametros['idMesa'];
        $pedido= $ArrayDeParametros['pedido'];
        $cliente= $ArrayDeParametros['cliente'];
        $tiempoInicio= date('Y/m/d G:i,s');

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
            $nuevoPedido->tiempoInicio=$tiempoInicio;
            $nuevoPedido->fotoMesa=$ultimoDestinoFoto; 
            $nuevoPedido->nombreCliente=$cliente;  
            $idPedido=$nuevoPedido->GuardarPedido();

            //En Arraydetalle se va a guardar producto:detalle. Ej:
            //gaseosa:coca,plato:plato1,....
           
            $arrayDetalle=explode(",",$pedido);
           
            // => es array. 
            //Ver si al ingresar Producto:descripcion como input lo toma como producto=>descripcion
           foreach ($arrayDetalle as $producto=>$descripcion)
           {
               //Genero un detalle por cada item del pedido (varios detalles por pedido)
               $detallePedido=new Detalle();
               $detallePedido->idPedido=$idPedido;
               $detallePedido->producto=$producto;
               $detallePedido->descripcion=$descripcion;  
               $detallePedido->estado="pendiente";
               
               if($producto=='trago'|| $producto=='vino'|| $producto=='gaseosa')
               {
                   $detallePedido->sector="barra";
               }
               if($producto=='pizza'|| $producto=='empanadas' || $producto=='plato')
               {
                   $detallePedido->sector="cocina";
               }
               if($producto=='cerveza')
               {
                   $detallePedido->sector="chopera";
               }
               if($producto=='postre')
               {
                   $detallePedido->sector="candybar";
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

        //ACA QUEDE
        public static function PrepararPedido($request, $response, $args)
        {
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










    }


