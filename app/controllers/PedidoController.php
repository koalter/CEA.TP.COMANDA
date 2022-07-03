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

    public function TraerUno($request, $response, $args)
    {
        try {
            $codigo = $args["codigo"];
            $id = $args["id"];

            $resultado = $this->_pedidoService->TraerUno($id, $codigo);
            $status = 200;
        } catch (\Throwable $th) {
            $resultado = array(
                "mensaje" => $th->getMessage(),
                "stackTrace" => $th->getTraceAsString());
            $status = 404;
        }

        $response->getBody()->write(json_encode($resultado));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo = $args['codigo'];

        $resultado = $this->_pedidoService->GenerarPedido($codigo, $parametros);

        $payload = json_encode($resultado);

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

    public function ListarPedidosListos($request, $response)
    {
        $resultado = $this->_pedidoService->ListarPedidosListos();

        $response->getBody()->write(json_encode($resultado));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function PrepararSiguiente($request, $response) 
    {
        try
        {
            $resultado = $this->_pedidoService->PrepararSiguiente($_COOKIE['rol']);
            $status = 200;
        }
        catch (\Throwable $th)
        {
            $resultado = [
                "mensaje" => $th->getMessage(),
                "stackTrace" => $th->getTraceAsString()
            ];
            $status = $th->getCode() === 404 ? 404 : 400;
        }

        $response->getBody()->write(json_encode($resultado));
        return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
    }

    public function ListoParaServir($request, $response, $args)
    {
        $resultado = $this->_pedidoService->ListoParaServir($_COOKIE['rol'], $args['id']);

        $response->getBody()->write(json_encode($resultado));
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ServirPedido($request, $response, $args)
    {
        try {
            $resultado = $this->_pedidoService->ServirPedido($args['id']);
            $status = 200;
        } catch (\Throwable $th) {
            $resultado = array(
                "mensaje" => $th->getMessage(),
                "stackTrace" => $th->getTraceAsString()
            );
            $status = $th->getCode() === 404 ? 404 : 400;
        }

        $response->getBody()->write(json_encode($resultado));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}