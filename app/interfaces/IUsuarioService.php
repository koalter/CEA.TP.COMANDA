<?php
namespace App\Interfaces;

interface IUsuarioService 
{
    public function Login(string $username, string $password);
    public function CargarUno(string $nombre, string $clave, string $strRol);
    public function TraerTodos();
}