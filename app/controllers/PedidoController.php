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

    public function ListarPendientes($request, $response) 
    {
        $resultado = $this->_pedidoService->ListarPendientes($_COOKIE['rol']);
        
        $response->getBody()->write(json_encode($resultado));
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ListarEnPreparacion($request, $response) 
    {
        $resultado = $this->_pedidoService->ListarEnPreparacion($_COOKIE['rol']);
        
        $response->getBody()->write(json_encode($resultado));
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function PrepararSiguiente($request, $response) 
    {
        $resultado = $this->_pedidoService->PrepararSiguiente($_COOKIE['rol']);

        $payload = is_null($resultado) ? array("mensaje" => "No hay mas pedidos para preparar!") : $resultado;

        $response->getBody()->write(json_encode($payload));
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ListoParaServir($request, $response, $args)
    {
        $resultado = $this->_pedidoService->ListoParaServir($_COOKIE['rol'], $args['id']);

        $response->getBody()->write(json_encode($resultado));
        return $response
        ->withHeader('Content-Type', 'application/json');
    } 
}