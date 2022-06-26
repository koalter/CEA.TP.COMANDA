<?php
namespace App\Services;

use App\Models\Mesa;
use App\Models\EstadoMesas;
use App\DTO\MesaDTO;
use App\DTO\PedidoDTO;
use App\Interfaces\IMesaService;
use App\Interfaces\IProductoService;

class MesaService implements IMesaService
{
    #region Singleton
    private const PATH_FOTOS = "./fotos/";
    private const DICCIONARIO = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWYZ";
    private static $mesaService;
    private IProductoService $_productoService;

    protected function __construct() 
    {
        $this->_productoService = ProductoService::obtenerInstancia();
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
        $mesa->codigo = $this->GenerarCodigo();
        
        if (!$mesa->save())
        {
            throw new \Exception("No se pudo dar de alta la mesa.");
        }

        return $mesa->codigo;
    }

    public function TraerTodos() 
    {
        $mesas = Mesa::all();
        $dtoMesas = array();

        foreach ($mesas as $mesa) {
            $dtoMesas[] = new MesaDTO($mesa->id, $mesa->cliente, $mesa->estado->descripcion, $mesa->codigo);
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
            $nombreDeArchivo = self::PATH_FOTOS . $codigo . "_" . $mesa->cliente . "_" . date("j-m-Y") . '.jpg';
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

    public function AClienteComiendo(int $id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->estado_id = 2;

        return $mesa->save();
    }

    public function AClientePagando(string $codigo)
    {
        $mesa = Mesa::where([
            "codigo" => $codigo,
            "estado_id" => 2
        ])->firstOrFail();

        $mesa->estado_id = 3;

        if (!$mesa->save())
        {
            throw new \Exception("Hubo un error al mover el estado de la mesa.");
        }

        $costoTotal = 0;

        foreach ($mesa->pedidos as $pedido)
        {
            $costoTotal += $this->_productoService->ObtenerProductoPorId($pedido->id)->precio * $pedido->cantidad;
        }

        return $costoTotal;
    }

    public function CerrarMesa(string $codigo)
    {
        $mesa = Mesa::where([
            "codigo" => $codigo,
            "estado_id" => 3
        ])->firstOrFail();
        $mesa->estado_id = 4;

        return $mesa->save();
    }

    public function ObtenerMesaCerradaPorIdYCodigo(int $id, string $codigo)
    {
        return Mesa::where([
            "id" => $id,
            "codigo" => $codigo,
            "estado_id" => 4
        ])->firstOrFail();
    }
    #endregion

    #region Métodos Privados
    private function ObtenerEstado(string $strEstado) : EstadoMesas 
    {
        return EstadoMesas::where('cliente', '=', $strEstado)->first();
    }
    #endregion
}