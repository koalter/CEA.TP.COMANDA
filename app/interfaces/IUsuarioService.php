<?php
namespace App\Interfaces;

interface IUsuarioService 
{
    public function CargarUno(string $nombre, string $strRol);
    public function TraerTodos();
}