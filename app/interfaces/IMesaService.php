<?php
namespace App\Interfaces;

interface IMesaService 
{
    public function CargarUno(string $cliente);
    public function TraerTodos();
}