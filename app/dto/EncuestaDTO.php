<?php
namespace App\DTO;

class EncuestaDTO implements \JsonSerializable
{
    private int $id;
    private int $puntuacionMesa;
    private int $puntuacionRestaurante;
    private int $puntuacionMozo;
    private int $puntuacionCocinero;
    private string $opinion;

    public function __construct(int $id, int $puntuacionMesa, int $puntuacionRestaurante, int $puntuacionMozo, int $puntuacionCocinero, string $opinion)
    {
        $this->id = $id;
        $this->puntuacionMesa = EncuestaDTO::ValidarPuntuacion($puntuacionMesa);
        $this->puntuacionRestaurante = EncuestaDTO::ValidarPuntuacion($puntuacionRestaurante);
        $this->puntuacionMozo = EncuestaDTO::ValidarPuntuacion($puntuacionMozo);
        $this->puntuacionCocinero = EncuestaDTO::ValidarPuntuacion($puntuacionCocinero);
        $this->opinion = EncuestaDTO::ValidarOpinion($opinion);
    }
    
    public function __get($name)
    {
        return $this->$name;
    }

    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }

    private static function ValidarPuntuacion(int $puntuacion) : int
    {
        if (isset($puntuacion) &&
            $puntuacion >= 1 && $puntuacion <= 10)
        {
            return $puntuacion;
        }

        throw new \InvalidArgumentException("La puntuacion debe ser del 1 al 10 unicamente.");
    }

    private static function ValidarOpinion(string $opinion) : string 
    {
        if (isset($opinion))
        {
            $caracteres = strlen($opinion);
            if ($caracteres <= 66)
            {
                return $opinion;
            }
        }

        throw new \InvalidArgumentException("La opinion no puede exceder los 66 caracteres.");
    }
}