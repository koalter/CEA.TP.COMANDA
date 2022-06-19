<?php
namespace App\Services;

use App\Models\Mesa;
use App\Models\EstadoMesas;
use App\DTO\MesaDTO;
use App\Interfaces\IMesaService;

class MesaService implements IMesaService
{
    #region Singleton
    private const PATH_FOTOS = "./fotos/";
    private const DICCIONARIO = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWYZ";
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
        $mesa->GenerarCodigo();
        
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

    public function TraerUno(string $codigo)
    {
        return Mesa::firstWhere("codigo", $codigo);
    }

    public function GuardarFoto(string $codigo, string $origen)
    {
        $mesa = $this->TraerUno($codigo);
        if (!is_null($mesa))
        {
            $nombreDeArchivo = self::PATH_FOTOS . $codigo . "_" . $mesa->cliente . '.jpg';
            if (!file_exists(self::PATH_FOTOS))
            {
                mkdir(self::PATH_FOTOS, 0777, true);
            }
            $resultado = move_uploaded_file($origen, $nombreDeArchivo);
            
            $mesa->foto = $nombreDeArchivo;
            $mesa->save();
        }

        return $resultado;
    }

    public function GenerarCodigo()
    {
        $resultado = "";

        for ($i = 0; $i < 5; $i++)
        {
            $resultado .= substr(self::DICCIONARIO, rand(0, strlen(self::DICCIONARIO)), 1);
        }

        return $resultado;
    }
    #endregion

    #region Métodos Privados
    private function ObtenerEstado(string $strEstado) : EstadoMesas 
    {
        return EstadoMesas::where('cliente', '=', $strEstado)->first();
    }
    #endregion
}