<?php
namespace App\Interfaces;

interface IPedidoService 
{
    public function GenerarPedido(array $lista);
    public function TraerTodos();
    public function ListarPendientes(string $rol);
    public function ListarEnPreparacion(string $rol);
    public function PrepararSiguiente(string $rol);
    public function ListoParaServir(string $rol, int $id);
}