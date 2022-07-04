<?php
namespace App\Services;

use App\Models\Producto;
use App\DTO\ProductoDTO;
use App\Interfaces\IProductoService;
use App\Models\Pedido;
use PDO;

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

    public function ObtenerProductoPorId(int $id)
    {
        return Producto::findOrFail($id);
    }

    public function TraerMasPedido()
    {
        $pdo = new PDO('mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'].';charset=utf8', $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $query = $pdo->prepare("SELECT SUM(`cantidad`) cantidad, pr.* FROM `pedidos` pe, `productos` pr WHERE pe.producto_id = pr.id GROUP BY `producto_id` ORDER BY cantidad desc LIMIT 1;");

        if ($query->execute())
        {
            $resultado = $query->fetch(PDO::FETCH_ASSOC);
            $producto = new ProductoDTO($resultado['id'], $resultado['descripcion'], $resultado['precio']);

            return $producto;
        }
        
        return null;
    }
    #endregion
}