<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

//CONTROLERS
require_once './controllers/UsuarioController.php';
require_once './controllers/controlerMesas.php';
require_once './controllers/controlerComida.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/controlerPedidos.php';
require_once './controllers/controlerCerrarPedido.php';
require_once './controllers/controlerResenia.php';
require_once './controllers/controlerClientes.php';
require_once './csv/controlerCsv.php';

//Middlewares
require_once './middlewares/mddUsuarios.php';
require_once './middlewares/validarResenia.php';
require_once './middlewares/mddCsv.php';
//Jwt
require_once './JWT/creacionjwt.php';
require_once './JWT/verificadorPuestos.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
// $app->group('/usuarios', function (RouteCollectorProxy $group) {
//     $group->get('[/]', \UsuarioController::class . ':TraerTodos');
//     $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
//     $group->post('[/]', \UsuarioController::class . ':CargarUno'); ->add(new verificacionUsuarioMW())
//   });

$app->group('/usuario',  function (RouteCollectorProxy $group)
{
  $group->post('/ingresar', \UsuarioController::class . ':ingresarUsuario')
  ->add(\VerificadorPuestos::class . ':verificarSocios')
  ->add(new verificacionUsuarioMW());
  $group->get('/mostrar', \UsuarioController::class . ':mostrarUnUsuario')->add(VerificadorPuestos::class . ':verificarSocios');
  $group->get('/eliminar', \UsuarioController::class . ':bajaUnUsuario')->add(VerificadorPuestos::class . ':verificarSocios');
  $group->get('/alta', \UsuarioController::class . ':Altadenuevo')->add(VerificadorPuestos::class . ':verificarSocios');
  $group->post('/modificar', \UsuarioController::class . ':modificarUsuario')->add(VerificadorPuestos::class . ':verificarSocios');

  $group->post('/login', \UsuarioController::class . ':loginUser')->add(VerificadorPuestos::class . ':verificarSocios');
});

$app->group('/pedidos', function (RouteCollectorProxy $group)
{
  $group->post('/ingresar', \ControlerPedidos::class . ':ingresarPedido')->add(VerificadorPuestos::class . ':verificarMozo');
  $group->get('/mostrar', \ControlerPedidos::class . ':mostrarPedido')->add(VerificadorPuestos::class . ':verificarMozo');
  $group->get('/eliminar', \ControlerPedidos::class . ':borrarPedido')->add(VerificadorPuestos::class . ':verificarMozo');
  $group->post('/modificar', \ControlerPedidos::class . ':modificarPedido')->add(VerificadorPuestos::class . ':verificarMozo');
  $group->post('/mostrarPuesto', \ControlerPedidos::class . ':mostrarPendientePuesto')->add(VerificadorPuestos::class . ':verificarEmpleados'); 
  $group->post('/terminar', \ControlerPedidos::class . ':terminarPendiente')->add(VerificadorPuestos::class . ':verificarEmpleados');
  $group->post('/entregar', \ControlerPedidos::class . ':entregarPedido')->add(VerificadorPuestos::class . ':verificarMozo');
  //cerrar pedido (No cierra mesa) y cerrar mesa
  $group->post('/cerrarPedido', \ControlerCerrarPedido::class . ':cerrarPedido')->add(VerificadorPuestos::class . ':verificarMozo'); 
  $group->post('/cerrarMesa', \ControlerCerrarPedido::class . ':cerrarMesa')->add(VerificadorPuestos::class . ':verificarSocios'); 

});

$app->group('/clientes',  function (RouteCollectorProxy $group)
{
  $group->post('/tiempo', \ControlerClientes::class . ':obtenerTiempoPreparacion');
});



$app->group('/mesa',  function (RouteCollectorProxy $group)
{
  $group->post('/ingresar', \ControlerMesas::class . ':IngresarMesa')->add(VerificadorPuestos::class . ':verificarMozo');
  $group->get('/mostrarUna', \ControlerMesas::class . ':mostrarUnaMesa')->add(VerificadorPuestos::class . ':verificarMozo');
  $group->get('/eliminarUna', \ControlerMesas::class . ':eliminarUnaMesa')->add(VerificadorPuestos::class . ':verificarMozo');
  $group->get('/mostrarTodas', \ControlerMesas::class . ':mostrarTodasMesa')->add(VerificadorPuestos::class . ':verificarMozo');

});

$app->group('/comida',  function (RouteCollectorProxy $group)
{
  $group->post('/ingresar', \ControlerComida::class . ':ingresarComida')->add(VerificadorPuestos::class . ':verificarSocios');
  $group->get('/mostrarUna', \ControlerComida::class . ':mostrarUnaComida')->add(VerificadorPuestos::class . ':verificarSocios');
  $group->get('/eliminarUna', \ControlerComida::class . ':eliminarUnaComida')->add(VerificadorPuestos::class . ':verificarSocios');
});

$app->group('/reseñas',  function (RouteCollectorProxy $group)
{
  $group->post('/ingresar', \ControlerResenia::class . ':ingresarResenia')->add(new ValidarResenia);
  $group->post('/mostrar', \ControlerResenia::class . ':mostrarResenia')->add(VerificadorPuestos::class . ':verificarSocios');
  $group->post('/mejores', \ControlerResenia::class . ':mejoresResenias')->add(VerificadorPuestos::class . ':verificarSocios');
});


$app->group('/subir', function (RouteCollectorProxy $group) {
  $group->post('/cargarDatos', \ControlerCsv::class . ':cargarDatos')->add(new MddCsv());
});



$app->get('[/]', function (Request $request, Response $response) {
    error_log("Dentro de la ruta /");
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();


// date_default_timezone_set('America/Mexico_City');		

// 	$fechaInicio = new DateTime("2020-03-09 17:55:15");
// 	$fechaFin = new DateTime("2022-01-01 17:45:25");
// 	$intervalo = $fechaInicio->diff($fechaFin);

// 	echo "La diferencia entre  " . $fechaInicio->format('Y-m-d h:i:s') . " y " . $fechaFin->format('Y-m-d h:i:s') . " es de: <br> 
//   " . $intervalo->h . " horas, " . $intervalo->i . " minutos y " . $intervalo->s . " segundos";  






