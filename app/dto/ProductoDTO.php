<?php
namespace App\DTO;

class ProductoDTO implements \JsonSerializable
{
    private int $id;
    private string $descripcion;
    private float $precio;
    private string $rol;

    public function __construct(int $id, string $descripcion, float $precio, string $rol = null)
    {
        $this->id = $id;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        if (!is_null($rol))
        {
            $this->rol = $rol;
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