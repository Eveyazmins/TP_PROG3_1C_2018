<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require './composer/vendor/autoload.php';
require_once './clases/AccesoDatos.php';
require_once './clases/EmpleadoApi.php';
require_once './clases/PedidoApi.php';
require_once './clases/MesaApi.php';
require_once './clases/SesionApi.php';
require_once './clases/ProductoApi.php';
require_once './clases/AutentificadorJWT.php';
require_once './clases/MWparaCORS.php';
require_once './clases/MWparaAutentificar.php';
require_once './clases/MWLaComanda.php';


$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;


$app = new \Slim\App(["settings" => $config]);


$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


/*LLAMADA A METODOS DE INSTANCIA DE UNA CLASE*/
$app->group('/Empleados', function () { 
  $this->post('/', \EmpleadoApi::class . ':CargarUno');
  $this->delete('/', \EmpleadoApi::class . ':BorrarUno');
  $this->post('/ModificarEmpleado', \EmpleadoApi::class . ':ModificarUno');
  $this->put('/Suspender', \EmpleadoApi::class . ':Suspender');  
  $this->get('/Operaciones/{id}', \EmpleadoApi::class . ':CantidadDeOperaciones');
  $this->get('/Logueos', \EmpleadoApi::class . ':IngresosAlSistema');
  $this->get('/OperacionesEmpleados', \EmpleadoApi::class . ':OperacionesTodosEmpleados');
  $this->get('/OperacionesSector/{sector}', \EmpleadoApi::class . ':OperacionesPorSector');
  $this->get('/OperacionesEmpleado/{idEmpleado}', \EmpleadoApi::class . ':OperacionesEmpleadoSeparado');
  $this->get('/OperacionesEmpleadoSector/{sector}', \EmpleadoApi::class . ':OperacionesEmpleadosSector');
  $this->post('/ListaEmpleados', \EmpleadoApi::class . ':traerTodos')->add(\MWLaComanda::class . ':VerificarAdministrador');//->add(\MWparaAutentificar::class . ':VerificarUsuario');
  $this->get('/TraerUno/{id}', \EmpleadoApi::class . ':traerUno')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
})->add(\MWparaAutentificar::class . ':VerificarUsuario')->add(\MWparaCORS::class . ':HabilitarCORS8080')->add(\MWparaCORS::class . ':HabilitarCORSTodos');


$app->group('/Pedidos', function(){
  $this->post('/',\PedidoApi::class . ':IngresarPedido'); 
  $this->post('/PendientesEmpleado',\PedidoApi::class . ':TraerPendientesEmpleado');
  $this->post('/PrepararPedido',\PedidoApi::class . ':PrepararPedido');
  $this->post('/ServirPedido',\PedidoApi::class . ':ServirPedido');
  $this->post('/TiempoRestante',\PedidoApi::class . ':TiempoRestante');
  $this->post('/Cancelar',\PedidoApi::class . ':CancelarPedido');
  $this->get('/Cancelados',\PedidoApi::class . ':TraerCancelados');
  $this->get('/MasVendido',\PedidoApi::class . ':TraerMasVendido');
  $this->get('/MenosVendido',\PedidoApi::class . ':TraerMenosVendido');
  $this->get('/NoEntregadosATiempo',\PedidoApi::class . ':NoEntregadosATiempo');
  
});//->add(\MWLaComanda::class . ':VerificarSuspendido');

$app->group('/Productos', function(){
  $this->get('/{nombre}',\ProductoApi::class . ':TraerProducto'); 
});

$app->group('/Mesas', function(){
  $this->post('/Cobrar',\MesaApi::class . ':CobrarMesa'); 
  $this->post('/Cerrar',\MesaApi::class . ':CerrarMesa');
  $this->get('/MasUsada',\MesaApi::class . ':MasUtilizada');
  $this->get('/MenosUsada',\MesaApi::class . ':MenosUtilizada');
  $this->get('/NoSeUso',\MesaApi::class . ':NoSeUso');
  $this->get('/MasFacturo',\MesaApi::class . ':MasFacturo');
  $this->get('/MenosFacturo',\MesaApi::class . ':MenosFacturo');
  $this->get('/MenorFactura',\MesaApi::class . ':MenorFactura');
  $this->get('/MayorFactura',\MesaApi::class . ':MayorFactura');
  $this->post('/FacturadoEntreFechas',\MesaApi::class . ':FacturadoEntreFechas');
});



$app->group('/Sesion', function(){
  $this->post('/',\SesionApi::class . ':Login');
  $this->put('/Salir', \SesionApi::class . ':CerrarSesion');

});





$app->run();