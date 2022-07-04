<?php
namespace App\Interfaces;
interface IFileService
{
    public function CargarCSV(string $archivo, bool $encriptado = false);
    public function DescargarCSV();
    public function DescargarPDF();
}