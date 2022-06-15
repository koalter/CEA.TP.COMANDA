<?php
namespace App\Middlewares;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class TokenMiddleware
{
    public static function VerificarToken(Request $request, RequestHandler $handler): Response
    {
        if (!isset($_COOKIE['token']))
        {
            echo "No se ha iniciado sesion!";
            die();
        }

        $token = $_COOKIE['token'];

        $decoded = JWT::decode(
            $token,
            $_ENV['secret'],
            ['HS256']
        );

        if ($decoded->admin)
        {
            return $handler->handle($request);
        }
        else 
        {
            echo "No posee suficientes permisos para realizar esta accion.";
            die();
        }
    }
}