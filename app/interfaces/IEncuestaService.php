<?php
namespace App\Interfaces;

interface IEncuestaService 
{
    public function Responder(string $codigo, int $id, $datos);
}