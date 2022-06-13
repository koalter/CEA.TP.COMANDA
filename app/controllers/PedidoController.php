<?php
namespace App\Controllers;

use App\Interfaces\IApiUsable;
use App\Interfaces\IPedidoService;
use App\Services\PedidoService;

class PedidoController implements IApiUsable 
{
    private IPedidoService $_pedidoService;

    public function __construct()
    {
        $this->_pedidoService = PedidoService::obtenerInstancia();
    }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $resultado = $this->_pedidoService->GenerarPedido($parametros);

        $payload = json_encode(array("mensaje" => $resultado ? "Pedido creado con exito" : "El pedido no pudo ser creado"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = $this->_pedidoService->TraerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}