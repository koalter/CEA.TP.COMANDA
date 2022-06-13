<?php
namespace App\Services;

use App\Models\Pedido;
use App\DTO\PedidoDTO;
use App\Interfaces\IPedidoService;

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

        if ($this->CargarPedidosEnBase($pedidosAGuardar)) 
        {
            $resultadoFinal = $this->mesaService->CargarUno($cliente);
        }

        return $resultadoFinal;
    }

    public function TraerTodos() 
    {
        $pedidos = Pedido::all();
        $dtoPedidos = array();

        foreach ($pedidos as $pedido) 
        {
            $dtoPedidos[] = new PedidoDTO($pedido->id, $pedido->producto->descripcion, $pedido->cantidad, $pedido->estado->descripcion);
        }

        return $dtoPedidos;
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

    private function CargarPedidosEnBase(array $pedidos) : bool 
    {
        $count = count($pedidos);

        for ($i = 0; $i < $count; $i++) 
        {
            if (!is_a($pedidos[$i], Pedido::class) || !$pedidos[$i]->save()) 
            {
                throw new \Exception("Error al generar pedido. Pedido: " . json_encode($pedidos[$i]), 1);
            }
        }

        return true;
    }
    #endregion
}