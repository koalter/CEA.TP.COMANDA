<?php
namespace App\Interfaces;

interface IPedidoService 
{
    public function GenerarPedido(array $lista);
    public function TraerTodos();
}