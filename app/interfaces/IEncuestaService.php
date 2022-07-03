<?php
namespace App\Interfaces;

interface IEncuestaService 
{
    public function Responder(string $codigo, $datos);
    public function TraerMejores();
}