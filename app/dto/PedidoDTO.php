<?php
namespace App\DTO;

class PedidoDTO implements \JsonSerializable
{
    private int $id;
    private string $producto;
    private string $cliente;
    private int $cantidad;
    private string $estado;

    public function __construct(int $id, string $producto, string $cliente, int $cantidad, string $estado)
    {
        $this->id = $id;
        $this->producto = $producto;
        $this->cliente = $cliente;
        $this->cantidad = $cantidad;
        $this->estado = $estado;
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