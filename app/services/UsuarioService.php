<?php
namespace App\Services;

use App\Models\Usuario;
use App\DTO\UsuarioDTO;
use App\Interfaces\IUsuarioService;

class UsuarioService implements IUsuarioService
{
    #region Singleton
    private static $usuarioService;
    private $rolService;

    protected function __construct() 
    {
        $this->rolService = RolService::obtenerInstancia();
    }

    protected function __clone() {}

    public static function obtenerInstancia() : UsuarioService
    {
        if (!isset(self::$usuarioService)) 
        {
            self::$usuarioService = new UsuarioService();
        }
        return self::$usuarioService;
    }
    #endregion

    #region Métodos Públicos
    public function CargarUno(string $nombre, string $clave, string $strRol) 
    {
        $rol = $this->rolService->ObtenerRol(strtolower($strRol));
        // Creamos el usuario
        $usuario = new Usuario();
        $usuario->nombre = $nombre;
        $usuario->clave = password_hash($clave, PASSWORD_DEFAULT);
        
        return $rol->usuarios()->save($usuario);
    }

    public function TraerTodos() 
    {
        $usuarios = Usuario::all();
        $dtoUsuarios = array();

        foreach ($usuarios as $usuario) {
            $dtoUsuarios[] = new UsuarioDTO($usuario->id, $usuario->nombre, $usuario->rol->nombre);
        }

        return $dtoUsuarios;
    }

    public function Login(string $username, string $password)
    {
        $user = Usuario::where(array(
            'nombre' => $username
        ))->first();

        if (!password_verify($password, $user->clave))
        {
            throw new \Exception("Credenciales inválidas");
        }

        $dto = new UsuarioDTO($user->id, $user->nombre, $user->rol->nombre);

        $token = TokenService::CrearToken($dto);

        return setcookie("token", $token, time() + 3600) && setcookie("role", $user->rol->nombre, time() + 3600);
    }
    #endregion
}