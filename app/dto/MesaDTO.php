<?php
namespace App\DTO;

class MesaDTO implements \JsonSerializable
{
    private int $id;
    private string $cliente;
    private string $codigo;
    private string $estado;

    public function __construct(int $id, string $cliente, string $estado, string $codigo)
    {
        $this->id = $id;
        $this->cliente = $cliente;
        $this->estado = $estado;
        $this->codigo = $codigo;
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