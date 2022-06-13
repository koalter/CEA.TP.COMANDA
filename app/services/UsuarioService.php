<?php
namespace App\Services;

use App\Models\Usuario;
use App\DTO\UsuarioDTO;

class UsuarioService 
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
    public function CargarUno(string $nombre, string $strRol) 
    {
        $rol = $this->rolService->ObtenerRol(strtolower($strRol));
        // Creamos el usuario
        $usuario = new Usuario();
        $usuario->nombre = $nombre;
        
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
    #endregion
}