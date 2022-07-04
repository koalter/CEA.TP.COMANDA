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

        try {
            $resultado = $this->_mesaService->CargarUno($cliente);
            $payload = array("codigo" => $resultado);
        } catch (\Throwable $th) {
            $payload = array(
                "mensaje" => $th->getMessage(),
                "stackTrace" => $th->getTraceAsString()
            );
        }

        $response->getBody()->write(json_encode($payload));
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

    public function AgregarFoto($request, $response, $args)
    {
        $resultado = $this->_mesaService->GuardarFoto($args["codigo"], $_FILES["foto"]["tmp_name"]);

        $payload = array("mensaje" => $resultado ? "La foto se subio con exito!" : "No se pudo subir la foto!");

        $response->getBody()->write(json_encode($payload));
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CobrarMesa($request, $response, $args)
    {
        try {
            $costoTotal = $this->_mesaService->AClientePagando($args["codigo"]);
            $resultado = array(
                "costoTotal" => $costoTotal
            );
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

    public function CerrarMesa($request, $response, $args)
    {
        try {
            $resultado = $this->_mesaService->CerrarMesa($args["codigo"]);
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