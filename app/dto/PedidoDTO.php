<?php
namespace App\DTO;

class PedidoDTO implements \JsonSerializable
{
    private int $id;
    private string $producto;
    private string $cliente;
    private int $cantidad;
    private string $estado;
    private string $tiempoPreparacion;

    public function __construct(int $id, string $producto, string $cliente, int $cantidad, string $estado, string $tiempo_preparacion = null)
    {
        $this->id = $id;
        $this->producto = $producto;
        $this->cliente = $cliente;
        $this->cantidad = $cantidad;
        $this->estado = $estado;
        if (!is_null($tiempo_preparacion))
        {
            $this->tiempoPreparacion = $tiempo_preparacion;
        }
    }
    
    public function __get($name)
    {
        return $this->$name;
    }

    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }
}