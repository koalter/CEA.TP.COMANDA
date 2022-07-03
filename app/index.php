<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
error_reporting(-1);
ini_set('display_errors', 1);

use App\Controllers\EncuestaController;
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
use App\Middlewares\RolMiddleware;
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
    $group->get('[/]', UsuarioController::class . ':TraerTodos')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    });
    // $group->get('/{usuario}', UsuarioController::class . ':TraerUno');
    $group->post('[/]', UsuarioController::class . ':CargarUno')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    });
    // $group->put('/{id}', UsuarioController::class . ':ModificarUno');
    // $group->delete('/{id}', UsuarioController::class . ':BorrarUno');
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', ProductoController::class . ':TraerTodos')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    });
    $group->post('[/]', ProductoController::class . ':CargarUno')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    });
    $group->get('/mejor', ProductoController::class . ':TraerMasPedido')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    });
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', MesaController::class . ':TraerTodos')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio', 'mozo']);
    });
    $group->post('[/]', MesaController::class . ':CargarUno')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio', 'mozo']);
    });
    $group->post('/foto/{codigo}', MesaController::class . ':AgregarFoto')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio', 'mozo']);
    });
    $group->put('/cobrar/{codigo}', MesaController::class . ':CobrarMesa')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio', 'mozo']);
    });
    $group->put('/cerrar/{codigo}', MesaController::class . ':CerrarMesa')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    });
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', PedidoController::class . ':TraerTodos')->add(function ($request, $handler) { 
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    });
    $group->get('/pendientes', PedidoController::class . ':ListarPendientes');
    $group->get('/en-preparacion', PedidoController::class . ':ListarEnPreparacion');
    $group->get('/listos', PedidoController::class . ':ListarPedidosListos')->add(function ($request, $handler) {
        return RolMiddleware::VerificarRol($request, $handler, ['socio', 'mozo']);
    });
    $group->get('/servir/{id}', PedidoController::class . ':ServirPedido')->add(function ($request, $handler) {
        return RolMiddleware::VerificarRol($request, $handler, ['socio', 'mozo']);
    });
    $group->post('/nuevo/{codigo}', PedidoController::class . ':CargarUno')->add(function ($request, $handler) {
        return RolMiddleware::VerificarRol($request, $handler, ['socio', 'mozo']);
    });
    $group->put('/siguiente', PedidoController::class . ':PrepararSiguiente');
    $group->put('/listo/{id}', PedidoController::class . ':ListoParaServir');
})->add(function ($request, $handler) {
    return TokenMiddleware::VerificarToken($request, $handler);
});

$app->post('/login', UsuarioController::class . ':Login');

$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/ver/codigo/{codigo}/id/{id}', PedidoController::class . ':TraerUno');
});

$app->group('/encuestas', function (RouteCollectorProxy $group) {
    $group->post('/responder/codigo/{codigo}', EncuestaController::class . ':ResponderEncuesta');
    
    $group->get('/mejores', EncuestaController::class . ':MejoresComentarios')->add(function ($request, $handler) {
        return RolMiddleware::VerificarRol($request, $handler, ['socio']);
    })->add(function ($request, $handler) {
        return TokenMiddleware::VerificarToken($request, $handler);
    });
});

$app->group('/admin', function (RouteCollectorProxy $group) {
    $group->get('/pdf', UsuarioController::class . ':DescargarPDF')->add(function ($request, $handler) {
        return TokenMiddleware::VerificarToken($request, $handler);
    });
    $group->get('/csv', UsuarioController::class . ':DescargarCSV')->add(function ($request, $handler) {
        return TokenMiddleware::VerificarToken($request, $handler);
    });
    $group->post('/csv', UsuarioController::class . ':CargarCSV')->add(function ($request, $handler) {
        if (!$request->getParsedBody()['secret'] === $_ENV['secret'])
        {
            echo "Acceso denegado.";
            die();
        }
        return $handler->handle($request);
    });
});

// Tests
$app->get('/test', Tests::class . ':correrTests');

// Punto de entrada
$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP");
    return $response;
});

$app->run();
