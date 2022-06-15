<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UsuarioController;
use App\Controllers\ProductoController;
use App\Controllers\MesaController;
use App\Controllers\PedidoController;
use App\Middlewares\TokenMiddleware;
use App\Tests\Tests;

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Eloquent
$container=$app->getContainer();

$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();


// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', UsuarioController::class . ':TraerTodos');
    // $group->get('/{usuario}', UsuarioController::class . ':TraerUno');
    $group->post('[/]', UsuarioController::class . ':CargarUno');
    // $group->put('/{id}', UsuarioController::class . ':ModificarUno');
    // $group->delete('/{id}', UsuarioController::class . ':BorrarUno');
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', ProductoController::class . ':TraerTodos');
    $group->post('[/]', ProductoController::class . ':CargarUno');
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', MesaController::class . ':TraerTodos');
    $group->post('[/]', MesaController::class . ':CargarUno');
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', PedidoController::class . ':TraerTodos');
    $group->post('[/]', PedidoController::class . ':CargarUno');
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->post('/login', UsuarioController::class . ':Login');

// Tests
$app->get('/test', Tests::class . ':correrTests');

// Punto de entrada
$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP");
    return $response;
});

$app->run();
