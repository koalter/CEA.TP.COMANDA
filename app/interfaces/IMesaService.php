<?php
namespace App\Interfaces;

interface IMesaService 
{
    public function CargarUno(string $cliente);
    public function TraerTodos();
    public function TraerUno(string $codigo);
    public function GuardarFoto(string $codigo, string $origen);
    public function GenerarCodigo();
    public function AClienteComiendo(int $id);
    public function AClientePagando(string $codigo);
    public function CerrarMesa(string $codigo);
    public function ObtenerMesaCerradaPorIdYCodigo(int $id, string $codigo);
}