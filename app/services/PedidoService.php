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
    public function GenerarPedido(string $cliente, array $lista)
    {
        if (trim($cliente) === "") 
        {
            throw new \Exception("Falta clave '".self::CLIENTE."'", 1);
        }

        $pedidosAGuardar = array();
        
        foreach ($lista as $key => $value) 
        {
            $pedidosAGuardar[] = $this->CargarPedidoEnMemoria($key, $value);
        }

        if (count($pedidosAGuardar) > 0)
        {
            $resultadoFinal = $this->CargarPedidosEnBase($cliente, $pedidosAGuardar);
        }

        return $resultadoFinal;
    }

    public function TraerUno(int $id, string $codigo)
    {
        $mesa = Mesa::whereFirst("codigo", "=", $codigo);

        if (is_null($mesa))
        {
            throw new \Exception("Codigo de mesa invalido.");
        }

        $pedido = Pedido::find($id);

        if (is_null($pedido))
        {
            throw new \Exception("Id de pedido invalido.");
        }

        $tiempoPreparacion = $this->ObtenerTiempoRestanteDePreparacion(date_create($pedido->tiempo_preparacion));

        return new PedidoDTO(
            $pedido->id,
            $pedido->producto->descripcion,
            $pedido->mesa->cliente,
            $pedido->cantidad,
            $pedido->estado->descripcion,
            $tiempoPreparacion);
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
        if (strtolower($rolDesc) === 'socio')
        {
            $pedidos = Pedido::where('estado_id', '=', 2)->get();
        }
        else
        {
            $rol = Rol::where('nombre', '=', $rolDesc)->first();
            $pedidos = Pedido::has('producto')
                ->whereRelation('producto', 'rol_id', $rol->id)
                ->where('estado_id', '=', 2)
                ->get();
        }


        $dtoPedidos = array();

        foreach ($pedidos as $pedido) 
        {
            $tiempoPreparacion = null;
            if (!is_null($pedido->tiempo_preparacion))
            {
                $tiempoPreparacion = $this->ObtenerTiempoRestanteDePreparacion(date_create($pedido->tiempo_preparacion));
            }
            $dtoPedidos[] = new PedidoDTO(
                $pedido->id,
                $pedido->producto->descripcion,
                $pedido->mesa->cliente,
                $pedido->cantidad,
                $pedido->estado->descripcion,
                $tiempoPreparacion);
        }

        return $dtoPedidos;
    }

    public function ListarPedidosListos()
    {
        $pedidos = Pedido::where('estado_id', '=', 3)->get();

        $dtoPedidos = array();

        foreach ($pedidos as $pedido)
        {
            $dtoPedidos[] = new PedidoDTO(
                $pedido->id,
                $pedido->producto->descripcion,
                $pedido->mesa->cliente,
                $pedido->cantidad,
                $pedido->estado->descripcion);
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

        $tiempoPreparacion = $siguientePedido->producto->tiempo_preparacion;
        $intervalo = $tiempoPreparacion . " seconds";

        $siguientePedido->estado_id = 2;
        $siguientePedido->tiempo_preparacion = date_add(date_create(), date_interval_create_from_date_string($intervalo));
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

    public function ServirPedido(int $id)
    {
        $pedido = Pedido::find($id);

        if (is_null($pedido))
        {
            throw new \Exception("El pedido no existe.", 404);
        }

        $pedido->estado_id = 4;
        if ($pedido->save())
        {
            $mesa = Mesa::find($pedido->mesa_id);
            $mesa->estado_id = 2;

            if ($mesa->save())
            {
                return $pedido;
            }
            else
            {
                throw new \Exception("Error al cambiar el estado de la mesa.");
            }
        }
        else
        {
            throw new \Exception("Error al cambir el estado del pedido.");
        }
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

    private function CargarPedidosEnBase(string $cliente, array $pedidos) : string 
    {
        $mesa = Mesa::firstOrCreate([
            'cliente' => $cliente
        ]);
        if (!isset($mesa->codigo))
        {
            $mesa->codigo = $this->mesaService->GenerarCodigo();
            $mesa->save();
        }

        $mesa->pedidos()->saveMany($pedidos);

        return $mesa->codigo;
    }

    private function ObtenerTiempoRestanteDePreparacion(\DateTime $tiempoPreparacion) : string
    {
        $timestamp = date_timestamp_get($tiempoPreparacion) - date_timestamp_get(date_create());
        $minutosRestantes = ceil($timestamp / 60);
        $respuesta = $minutosRestantes == 1 || $minutosRestantes == -1 ?
            $minutosRestantes . " minuto" : $minutosRestantes . " minutos";

        return $respuesta;
    }
    #endregion
}