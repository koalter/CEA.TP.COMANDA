<?php
namespace App\Services;

use Firebase\JWT\JWT;

class TokenService 
{
    public static function CrearToken($user)
    {
        $admin = $user->rol == "socio";
        $ahora = time();

        $payload = array(
            "iat" => $ahora,
            "exp" => $ahora + 3600,
            "data" => $user,
            "admin" => $admin
        );

        return JWT::encode($payload, $_ENV['secret'], "HS256");
    }
}