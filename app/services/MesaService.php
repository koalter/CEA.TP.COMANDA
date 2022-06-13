<?php
namespace App\Services;

use App\Models\Mesa;
use App\Models\EstadoMesas;
use App\DTO\MesaDTO;

class MesaService 
{
    #region Singleton
    private static $mesaService;
    private $rolService;

    protected function __construct() 
    {
        $this->rolService = RolService::obtenerInstancia();
    }

    protected function __clone() {}

    public static function obtenerInstancia() : MesaService
    {
        if (!isset(self::$mesaService)) 
        {
            self::$mesaService = new MesaService();
        }
        return self::$mesaService;
    }
    #endregion

    #region Métodos Públicos
    public function CargarUno(string $cliente) 
    {
        $mesa = new Mesa();
        $mesa->cliente = strtolower($cliente);
        
        return $mesa->save();
    }

    public function TraerTodos() 
    {
        $mesas = Mesa::all();
        $dtoMesas = array();

        foreach ($mesas as $mesa) {
            $dtoMesas[] = new MesaDTO($mesa->id, $mesa->cliente, $mesa->estado->descripcion);
        }

        return $dtoMesas;
    }
    #endregion

    #region Métodos Privados
    private function ObtenerEstado(string $strEstado) : EstadoMesas 
    {
        return EstadoMesas::where('cliente', '=', $strEstado)->first();
    }
    #endregion
}