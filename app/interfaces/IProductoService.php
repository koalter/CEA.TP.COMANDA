<?php
namespace App\Interfaces;

interface IProductoService 
{
    public function CargarUno(string $descripcion, float $precio, string $strRol);
    public function TraerTodos();
    public function ObtenerProducto(string $strProducto);
    public function ObtenerProductoPorId(int $id);
    public function TraerMasPedido();
}