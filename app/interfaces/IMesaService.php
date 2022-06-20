<?php
namespace App\Interfaces;

interface IMesaService 
{
    public function CargarUno(string $cliente);
    public function TraerTodos();
    public function TraerUno(string $codigo);
    public function GuardarFoto(string $codigo, string $origen);
    public function GenerarCodigo();
}