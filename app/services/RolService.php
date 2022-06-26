<?php
namespace App\Services;

use App\Interfaces\IRolService;
use App\Models\Rol;

class RolService implements IRolService
{
    #region Singleton
    private static $rolService;

    protected function __construct() {}

    protected function __clone() {}

    public static function obtenerInstancia() : RolService
    {
        if (!isset(self::$rolService)) 
        {
            self::$rolService = new RolService();
        }
        return self::$rolService;
    }
    #endregion

    #region Métodos Públicos
    public static function ObtenerRol(string $strRol) : Rol
    {
        return Rol::where('nombre', '=', $strRol)->first();
    }
    #endregion
}