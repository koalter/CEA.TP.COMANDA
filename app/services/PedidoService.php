<?php
namespace App\Services;

use App\Models\Pedido;
use App\DTO\PedidoDTO;
use App\Interfaces\IPedidoService;
use App\Models\EstadoPedidos;
use App\Models\Mesa;
use App\Models\Rol;

class PedidoService implements IPedidoService
{
    #region Singleton
    private static $pedidoService;
    private $productoService;
    private $mesaService;
    private const CLIENTE = "cliente";

    protected function __construct() 
    {
        $this->productoService = ProductoService::obtenerInstancia();
        $this->mesaService = MesaService::obtenerInstancia();
    }

    protected function __clone() {}

    public static function obtenerInstancia() : PedidoService
    {
        if (!isset(self::$pedidoService)) 
        {
            self::$pedidoService = new PedidoService();
        }
        return self::$pedidoService;
    }
    #endregion

    #region Métodos Públicos
    public function GenerarPedido(array $lista) 
    {
        $resultadoFinal = false;

        if (!array_key_exists(self::CLIENTE, $lista)) 
        {
            throw new \Exception("Falta clave '".self::CLIENTE."'", 1);
        }

        $pedidosAGuardar = array();
        
        foreach ($lista as $key => $value) 
        {
            if ($key === self::CLIENTE) 
            {
                $cliente = $value;
            }
            else 
            {
                $pedidosAGuardar[] = $this->CargarPedidoEnMemoria($key, $value);
            }
        }

        if (count($pedidosAGuardar) > 0)
        {
            $resultadoFinal = $this->CargarPedidosEnBase($cliente, $pedidosAGuardar);
        }

        return $resultadoFinal;
    }

    public function TraerTodos() 
    {
        $pedidos = Pedido::all();
        $dtoPedidos = array();

        foreach ($pedidos as $pedido) 
        {
            $dtoPedidos[] = new PedidoDTO($pedido->id, $pedido->producto->descripcion, $pedido->mesa->cliente, $pedido->cantidad, $pedido->estado->descripcion);
        }

        return $dtoPedidos;
    }

    public function ListarPendientes(string $rolDesc)
    {
        $rol = Rol::where('nombre', '=', $rolDesc)->first();
        $pedidos = Pedido::has('producto')
            ->whereRelation('producto', 'rol_id', $rol->id)
            ->where('estado_id', '=', 1)
            ->get();
        
        $dtoPedidos = array();

        foreach ($pedidos as $pedido) 
        {
            $dtoPedidos[] = new PedidoDTO($pedido->id, $pedido->producto->descripcion, $pedido->mesa->cliente, $pedido->cantidad, $pedido->estado->descripcion);
        }

        return $dtoPedidos;
    }

    public function ListarEnPreparacion(string $rolDesc)
    {
        $rol = Rol::where('nombre', '=', $rolDesc)->first();
        $pedidos = Pedido::has('producto')
            ->whereRelation('producto', 'rol_id', $rol->id)
            ->where('estado_id', '=', 2)
            ->get();

        $dtoPedidos = array();

        foreach ($pedidos as $pedido) 
        {
            $dtoPedidos[] = new PedidoDTO($pedido->id, $pedido->producto->descripcion, $pedido->mesa->cliente, $pedido->cantidad, $pedido->estado->descripcion);
        }

        return $dtoPedidos;
    }

    public function PrepararSiguiente(string $rolDesc)
    {
        $rol = Rol::where('nombre', '=', $rolDesc)->first();
        $siguientePedido = Pedido::has('producto')
            ->whereRelation('producto', 'rol_id', $rol->id)
            ->where('estado_id', '=', 1)
            ->first();

        if (is_null($siguientePedido))
        {
            return null;
        }

        $siguientePedido->estado_id = 2;
        if ($siguientePedido->save())
        {
            $dtoPedido = new PedidoDTO(
                $siguientePedido->id, 
                $siguientePedido->producto->descripcion, 
                $siguientePedido->mesa->cliente, 
                $siguientePedido->cantidad, 
                $siguientePedido->estado->descripcion
            );
        }
        
        return $dtoPedido;
    }

    public function ListoParaServir(string $rolDesc, int $id)
    {
        $rol = Rol::where('nombre', '=', $rolDesc)->first();
        $siguientePedido = Pedido::has('producto')
            ->whereRelation('producto', 'rol_id', $rol->id)
            ->where('estado_id', '=', 2)
            ->where('id', '=', $id)
            ->first();

        $siguientePedido->estado_id = 3;
        if ($siguientePedido->save())
        {
            $dtoPedido = new PedidoDTO(
                $siguientePedido->id, 
                $siguientePedido->producto->descripcion, 
                $siguientePedido->mesa->cliente, 
                $siguientePedido->cantidad, 
                $siguientePedido->estado->descripcion
            );
        }
        
        return $dtoPedido;
    }
    #endregion

    #region Métodos Privados
    private function CargarPedidoEnMemoria(string $descripcion, int $cantidad) : Pedido 
    {
        $descripcionFormateada = $this->formatear($descripcion);

        $producto = $this->productoService->ObtenerProducto($descripcionFormateada);
        if (is_null($producto))
        {
            throw new \Exception("No se encontró registrado el producto '".$descripcionFormateada."'", 1);
        }

        $pedido = new Pedido();
        $pedido->cantidad = $cantidad;
        $pedido->producto_id = $producto->id;

        return $pedido;
    }

    private function formatear(string $texto) : string 
    {
        $primerPaso = trim($texto);
        $segundoPaso = strtolower($primerPaso);
        $tercerPaso = str_replace("_", " ", $segundoPaso);

        return $tercerPaso;
    }

    private function CargarPedidosEnBase(string $cliente, array $pedidos) : bool 
    {
        $mesa = Mesa::firstOrCreate([
            'cliente' => $cliente
        ]);
        $mesa->pedidos()->saveMany($pedidos);

        return true;
    }
    #endregion
}