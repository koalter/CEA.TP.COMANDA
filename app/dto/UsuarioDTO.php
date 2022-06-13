<?php
namespace App\DTO;

class UsuarioDTO implements \JsonSerializable
{
    private int $id;
    private string $nombre;
    private string $rol;

    public function __construct(int $id, string $nombre, string $rol)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->rol = $rol;
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