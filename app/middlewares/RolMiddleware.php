<?php
namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RolMiddleware 
{
    public static function VerificarRol(Request $request,  RequestHandler $handler, array $roles) : Response
    {
        if (!array_search($_COOKIE['rol'], $roles))
        {
            echo "Usuario no autorizado!";
            die();
        }

        return $handler->handle($request);
    }
}