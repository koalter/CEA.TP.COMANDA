<?php
namespace App\Services;

use App\Models\Producto;
use App\DTO\ProductoDTO;
use App\Interfaces\IProductoService;

class ProductoService implements IProductoService
{
    #region Singleton
    private static $productoService;
    private $rolService;

    protected function __construct() 
    {
        $this->rolService = RolService::obtenerInstancia();
    }

    protected function __clone() {}

    public static function obtenerInstancia() : ProductoService
    {
        if (!isset(self::$productoService)) 
        {
            self::$productoService = new ProductoService();
        }
        return self::$productoService;
    }
    #endregion

    #region Métodos Públicos
    public function CargarUno(string $descripcion, float $precio, string $strRol) 
    {
        $rol = $this->rolService->ObtenerRol(strtolower($strRol));

        $producto = new Producto();
        $producto->descripcion = strtolower($descripcion);
        $producto->precio = $precio;
        
        return $rol->productos()->save($producto);
    }

    public function TraerTodos() 
    {
        $productos = Producto::all();
        $dtoProductos = array();

        foreach ($productos as $producto) {
            $dtoProductos[] = new ProductoDTO($producto->id, $producto->descripcion, $producto->precio, $producto->rol->nombre);
        }

        return $dtoProductos;
    }

    public function ObtenerProducto(string $strProducto)
    {
        return Producto::where('descripcion', '=', $strProducto)->first();
    }
    #endregion
}