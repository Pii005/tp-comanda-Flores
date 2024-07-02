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

require_once './controllers/UsuarioController.php';
require_once './controllers/controlerMesas.php';
require_once './controllers/controlerComida.php';

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
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  });

$app->group('/mesa',  function (RouteCollectorProxy $group)
{
  $group->post('/ingresar', \ControlerMesas::class . ':IngresarMesa');
  $group->get('/mostrarUna', \ControlerMesas::class . ':mostrarUnaMesa');
  $group->get('/eliminarUna', \ControlerMesas::class . ':eliminarUnaMesa');
});

$app->group('/comida',  function (RouteCollectorProxy $group)
{
  $group->post('/ingresar', \ControlerComida::class . ':ingresarComida');
  $group->get('/mostrarUna', \ControlerComida::class . ':mostrarUnaComida');
  $group->get('/eliminarUna', \ControlerComida::class . ':eliminarUnaComida');
});

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
