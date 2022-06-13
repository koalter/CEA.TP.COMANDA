<?php
namespace App\Tests;

use App\Services\UsuarioService;
use App\Services\ProductoService;
use App\Services\MesaService;
use App\Services\PedidoService;

class Tests 
{
    private UsuarioService $_usuarioService;
    private ProductoService $_productoService;
    private MesaService $_mesaService;
    private PedidoService $_pedidoService;

    public function __construct() 
    {
        $this->_usuarioService = UsuarioService::obtenerInstancia();
        $this->_productoService = ProductoService::obtenerInstancia();
        $this->_mesaService = MesaService::obtenerInstancia();
        $this->_pedidoService = PedidoService::obtenerInstancia();
    }

    public function correrTests($request, $response, $args) 
    {
        $resultados = array();

        // Listado de tests
        $resultados["Dar de alta y listar usuarios"] = $this->darDeAltaYListarUsuarios();
        $resultados["Dar de alta y listar productos"] = $this->darDeAltaYListarProductos();
        $resultados["Dar de alta y listar mesas"] = $this->darDeAltaYListarMesas();
        $resultados["Dar de alta y listar pedidos"] = $this->darDeAltaYListarPedidos();

        $response->getBody()->write(json_encode($resultados));

        return $response->withHeader("Content-Type", "application/json");
    }

    private function darDeAltaYListarUsuarios() 
    {
        $response = $this->_usuarioService->CargarUno("test_usuario", "mozo");

        if ($response) 
        {
            $response = count($this->_usuarioService->TraerTodos()) == 1;
        }

        return $response;
    }

    private function darDeAltaYListarProductos() 
    {
        $response = $this->_productoService->CargarUno("milanesa a caballo", 249.99, "cocinero");
        $response = $this->_productoService->CargarUno("hamburguesa de garbanzo", 200, "cocinero");
        $response = $this->_productoService->CargarUno("corona", 135, "cervecero");
        $response = $this->_productoService->CargarUno("daikiri", 180, "bartender");

        if ($response) 
        {
            $response = count($this->_productoService->TraerTodos()) == 4;
        }

        return $response;
    }
    
    private function darDeAltaYListarMesas() 
    {
        $response = $this->_mesaService->CargarUno("lorenzo");

        if ($response) 
        {
            $response = count($this->_mesaService->TraerTodos()) == 1;
        }

        return $response;
    }

    private function darDeAltaYListarPedidos() 
    {
        $pedido = array(
            "cliente" => "test_cliente",
            "milanesa a caballo" => 1,
            "hamburguesa de garbanzo" => 2,
            "corona" => 1,
            "Daikiri" => 1 
        );

        $response = $this->_pedidoService->GenerarPedido($pedido);

        if ($response) 
        {
            $response = count($this->_pedidoService->TraerTodos()) == 4;
        }

        return $response;
    }
}