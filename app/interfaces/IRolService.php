<?php
namespace App\Interfaces;

use App\Models\Rol;

interface IRolService 
{
    public static function ObtenerRol(string $strRol) : Rol;
}