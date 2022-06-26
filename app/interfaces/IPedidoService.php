<?php
namespace App\Interfaces;

interface IPedidoService 
{
    public function GenerarPedido(string $codigo, array $lista);
    public function TraerUno(int $id, string $codigo);
    public function TraerTodos();
    public function ListarPendientes(string $rol);
    public function ListarEnPreparacion(string $rol);
    public function ListarPedidosListos();
    public function PrepararSiguiente(string $rol);
    public function ListoParaServir(string $rol, int $id);
    public function ServirPedido(int $id);
}