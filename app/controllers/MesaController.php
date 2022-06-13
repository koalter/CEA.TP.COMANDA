<?php
namespace App\Controllers;

use App\Interfaces\IApiUsable;
use App\Interfaces\IMesaService;
use App\Services\MesaService;

class MesaController implements IApiUsable 
{
    private IMesaService $_mesaService;

    public function __construct()
    {
        $this->_mesaService = MesaService::obtenerInstancia();
    }
    
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $cliente = strtolower($parametros['cliente']);

        $resultado = $this->_mesaService->CargarUno($cliente);

        $payload = json_encode(array("mensaje" => $resultado ? "Mesa creada con exito" : "La mesa no pudo ser creada"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = $this->_mesaService->TraerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}