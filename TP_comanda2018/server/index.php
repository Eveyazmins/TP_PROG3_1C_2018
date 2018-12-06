<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'clases/AccesoDatos.php';
require_once 'clases/empleadoApi.php';
require_once 'clases/pedidoApi.php';
require_once 'clases/mesaApi.php';
//require_once 'clases/encuestaApi.php';
require_once 'clases/listados.php';
require_once 'clases/loginApi.php';
require_once 'clases/MWparaCORS.php';
require_once 'clases/MWparaAutentificar.php';
require_once 'clases/excel.php';
require_once 'clases/pdf.php';
require_once 'clases/foto.php';
require_once 'clases/comandaApi.php';


$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

/*
¡La primera línea es la más importante! A su vez en el modo de 
desarrollo para obtener información sobre los errores
 (sin él, Slim por lo menos registrar los errores por lo que si está utilizando
  el construido en PHP webserver, entonces usted verá en la salida de la consola 
  que es útil).

  La segunda línea permite al servidor web establecer el encabezado Content-Length, 
  lo que hace que Slim se comporte de manera más predecible.
*/

$app = new \Slim\App(["settings" => $config]);
$app->add(function($request, $response, $next){
  $response = $next($request, $response);

  return $response
              ->withHeader('Access-Control-Allow-Origin', '*')
              ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
              ->withHeader('Access-Control-Allow-Methods', 'GET, POST');
            // ->withHeader('Access-Control-Allow-Origin','*')
            // ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Origin, Authorization, field, content-type, Content-Type:multipart/form-data')
            // ->withHeader('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, OPTIONS')
            // ->withHeader('Content-Type','application/json; charset=utf-8');
});


$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("Bienvenido!!!");
  return $response;

})->add(\MWparaCORS::class . ':HabilitarCORSTodos');
//Para LogIn Email y clave
$app->post('/Login[/]', \loginApi::class . ':login')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
//Para ver datos del empleado validando Token, ingresar Token + valor token en el header
$app->post('/datosToken[/]', \loginApi::class . ':datosToken')->add(\MWparaAutentificar::class . ':VerificarUser')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

//FALTA PROBAR!
$app->post('/Encuesta[/]', \pedidoApi::class . ':encuesta')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
$app->post('/finalizarEncuesta[/]', \pedidoApi::class . ':finalizarEncuesta')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
$app->post('/todasEncuestas[/]', \pedidoApi::class . ':traerTodasEncuestas')->add(\MWparaCORS::class . ':HabilitarCORSTodos');


$app->group('/empleado', function () {

     //7-De​ los empleados c- alta suspenderlos​ y borrarlos (POST nombre sexo email clave turno perfil estado) (FILES foto)
     $this->post('/alta[/]', \empleadoApi::class . ':CargarUno');
     //Trae todo ( si le agrega una letra a args trae solo los suspendidos)
     $this->get('/[{suspendidos}]', \empleadoApi::class . ':traerTodos');
     $this->post('/traerUno[/]', \empleadoApi::class . ':traerUno');
     $this->post('/traerEmpleados[/]', \empleadoApi::class . ':traerTodos');
     //(POST id)
     $this->post('/borrar[/]', \empleadoApi::class . ':BorrarUno');
     //para buscar (POST id) a modificar cualquer dato del alta por post
     //$this->post('/modificar[/]', \empleadoApi::class . ':modificarUno');
     //(POST id)
     $this->post('/suspender[/]', \empleadoApi::class . ':suspenderUno');
     //(POST id)
     $this->post('/activar[/]', \empleadoApi::class . ':activarUno');
     //7-b- Cantidad de operaciones por cada uno (POST desde, hasta args email)
     $this->post('/cantidadOperaciones/[{email}]', \empleadoApi::class . ':operacionesEmpleado');
     //7-a-los​ días y horarios​ que se Ingresaron​ al sistema (POST desde, hasta args email)
     $this->post('/historicoLogin/[{email}]', \empleadoApi::class . ':loginEmpleado');
     //$this->get('/verImagen/[{email}]', \foto::class . ':verImagen');

 })->add(\MWparaAutentificar::class . ':VerificarAdmin')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

 $app->group('/pedido', function () {

  $this->post('/alta[/]', \pedidoApi::class . ':crearPedido')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->get('/', \pedidoApi::class . ':traerTodos');
  $this->post('/traerUno[/]', \pedidoApi::class . ':traerUno');
  $this->post('/cancelar[/]', \pedidoApi::class . ':cancelarUno')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->post('/modificar[/]', \pedidoApi::class . ':modificarUno')->add(\MWparaAutentificar::class . ':VerificarEmpleado');
  $this->post('/finalizar[/]', \pedidoApi::class . ':finalizarUno')->add(\MWparaAutentificar::class . ':VerificarEmpleado');
  //$this->post('/estadoGlobal[/]', \pedidoApi::class . ':cambiarEstadoPedido');
  $this->post('/operacionesSector[/]', \pedidoApi::class . ':operacionesSector')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->post('/operacionesSectorEmpleado[/]', \pedidoApi::class . ':operacionesSectorEmpleados')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->post('/masVendidos[/]', \pedidoApi::class . ':masPedidos')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->post('/tiempoRestante[/]', \pedidoApi::class . ':TiempoRestante');
  $this->get('/cancelados', \pedidoApi::class . ':traerTodosCancelados')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->get('/demorados', \pedidoApi::class . ':traerTodosDemorados')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
})->add(\MWparaCORS::class . ':HabilitarCORSTodos');

$app->group('/comanda', function () {

  $this->post('/alta[/]', \comandaApi::class . ':crearComanda')->add(\MWparaAutentificar::class . ':VerificarMozo');
  $this->get('/', \comandaApi::class . ':traerTodas')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->post('/traerPedidos[/]', \comandaApi::class . ':traerPedidosComanda')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->post('/traerUna[/]', \comandaApi::class . ':traerUna')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->post('/cerrarUna[/]', \comandaApi::class . ':cerrarComanda')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->post('/altaEncuesta[/]', \comandaApi::class . ':CargarEncuesta')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->post('/finalizarEncuesta[/]', \comandaApi::class .':finalizarEncuesta');
  $this->get('/mejoresComentarios', \comandaApi::class .':mejoresComentarios')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->get('/peoresComentarios', \comandaApi::class .':peoresComentarios')->add(\MWparaAutentificar::class . ':VerificarAdmin');
})->add(\MWparaCORS::class . ':HabilitarCORSTodos');


$app->group('/mesa', function () {

  $this->get('/', \mesaApi::class . ':traerTodos')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->get('/disponibles/', \mesaApi::class . ':traerDisponibles')->add(\MWparaAutentificar::class . ':VerificarSocioMozo');
  $this->get('/masFacturada/', \comandaApi::class . ':MesaMasFacturo')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->get('/menosFacturada/', \comandaApi::class . ':MesaMenosFacturo')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->get('/mayorImporte/', \comandaApi::class . ':MesaMasImporte')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->get('/menorImporte/', \comandaApi::class . ':MesaMenosImporte')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->get('/masUsada/', \MesaApi::class . ':MesaMasUsada')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->get('/menosUsada/', \MesaApi::class . ':MesaMenosUsada')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  $this->post('/FacturadoDesdeHasta', \comandaApi::class . ':TraerFacturadoDesdeHasta')->add(\MWparaAutentificar::class . ':VerificarAdmin');

})->add(\MWparaCORS::class . ':HabilitarCORSTodos');

$app->group('/listados', function () {

  $this->get('/empleados/login/', \listados::class . ':traerTodosLoginEmpleados');

})->add(\MWparaCORS::class . ':HabilitarCORSTodos');

$app->group('/loginExport', function () {
  $this->get('/excel/[{id}]', \excel::class . ':loginUsurioExcel');
  $this->get('/pdf/[{id}]', \epdf::class . ':loginUsuarioPDF');
  
 })->add(\MWparaCORS::class . ':HabilitarCORSTodos');



// $app->group('/excel', function () {

//   $this->get('/empleados[/]', \excel::class . ':traerTodosEmpleadosExcel');
//   $this->get('/login[/]', \excel::class . ':loginExcel');


// })->add(\MWparaAutentificar::class . ':VerificarAdmin')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

// $app->group('/pdf', function () {
//   $this->get('/ubicar/[{patente}]', \epdf::class . ':ubicaAutoPDF');
  
// })->add(\MWparaAutentificar::class . ':VerificarAdmin')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

// $app->group('/loginExport', function () {
//   $this->get('/excel/[{id}]', \excel::class . ':loginUsurioExcel');
//   $this->get('/pdf/[{id}]', \epdf::class . ':loginUsuarioPDF');
  
// })->add(\MWparaCORS::class . ':HabilitarCORSTodos');

// $app->group('/foto', function () {

//   $this->post('/backup[/]', \foto::class . ':backupFoto');
//   $this->post('/renombrarFoto[/]', \foto::class . ':reNombrarFoto');
//   $this->post('/marcaDeAgua[/]', \foto::class . ':marcaDeAgua');
//   $this->get('/vaciarPapelera', \foto::class . ':vaciarPapelera');
  
// })->add(\MWparaAutentificar::class . ':VerificarAdmin')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

$app->run();