<?php
namespace App\Controllers;

use App\Interfaces\IApiUsable;
use App\Interfaces\IProductoService;
use App\Services\ProductoService;

class ProductoController implements IApiUsable 
{
    private IProductoService $_productoService;

    public function __construct()
    {
        $this->_productoService = ProductoService::obtenerInstancia();
    }

    public function CargarUno($request, $response, $args) 
    {
        $parametros = $request->getParsedBody();

        $descripcion = strtolower($parametros['descripcion']);
        $precio = floatval($parametros['precio']);
        $rol = strtolower($parametros['rol']);

        $resultado = $this->_productoService->CargarUno($descripcion, $precio, $rol);

        $payload = json_encode(array("mensaje" => $resultado ? "Producto creado con exito" : "El producto no pudo ser creado"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args) 
    {
        $lista = $this->_productoService->TraerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}