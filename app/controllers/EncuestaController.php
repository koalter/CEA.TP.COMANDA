<?php
namespace App\Controllers;

use App\DTO\EncuestaDTO;
use App\Interfaces\IEncuestaService;
use App\Services\EncuestaService;

class EncuestaController 
{
    private IEncuestaService $_encuestaService;

    public function __construct()
    {
        $this->_encuestaService = EncuestaService::obtenerInstancia();
    }

    public function ResponderEncuesta($request, $response, $args)
    {
        try {
            $body = $request->getParsedBody();
            $dto = new EncuestaDTO(-1, $body["mesa"], $body["restaurante"], $body["mozo"], $body["cocinero"], $body["opinion"]);
            if ($this->_encuestaService->Responder($args["codigo"], $dto))
            {
                $resultado = ["mensaje" => "Gracias por responder a nuestra encuesta de satisfaccion."];
            }
            else 
            {
                $resultado = ["mensaje" => "Ya ha respondido la encuesta."];
            }
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

    public function MejoresComentarios($request, $response)
    {
        $resultado = $this->_encuestaService->TraerMejores();

        $response->getBody()->write(json_encode($resultado));
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}