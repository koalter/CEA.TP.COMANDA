<?php
namespace App\Services;

use App\Interfaces\IFileService;
use App\Models\EstadoMesas;
use App\Models\EstadoPedidos;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuario;
use Fpdf\Fpdf;
use PDO;

class FileService implements IFileService
{
    private static $fileService;

    protected function __construct() {}

    protected function __clone() {}

    public static function obtenerInstancia() : FileService
    {
        if (!isset(self::$fileService)) 
        {
            self::$fileService = new FileService();
        }
        return self::$fileService;
    }

    #region Public Methods
    public function CargarCSV(string $filepath, bool $encriptado = false)
    {
        $archivo = fopen($filepath, 'r');
        try 
        {
            $this->CargarABase($archivo, $encriptado);
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            fclose($archivo);
        }

        return true;
    }

    public function DescargarCSV()
    {
        $estadoPedidos = EstadoPedidos::all();
        $estadoMesas = EstadoMesas::all();
        $roles = Rol::all();
        $usuarios = Usuario::all();
        $productos = Producto::all();
        $pedidos = Pedido::all();

        $csv = "";
        
        foreach ($estadoPedidos as $estadoPedido) {
            $csv .= "EstadoPedidos" . "," . $estadoPedido->id . "," . $estadoPedido->descripcion . "\n";
        }
        foreach ($estadoMesas as $estadoMesa) {
            $csv .= "EstadoMesas" . "," . $estadoMesa->id . "," . $estadoMesa->descripcion . "\n";
        }
        foreach ($roles as $rol) {
            $csv .= "Rol" . "," . $rol->id . "," . $rol->nombre . "\n";
        }
        foreach ($usuarios as $usuario) {
            $csv .= "Usuario" . "," . $usuario->id . "," . $usuario->nombre . "," . $usuario->clave . "," . $usuario->rol_id . "\n";
        }
        foreach ($productos as $producto) {
            $csv .= "Producto" . "," . $producto->id . "," . $producto->descripcion . "," . $producto->precio . "," . $producto->rol_id . "," . $producto->tiempo_preparacion . "\n";
        }
        foreach ($pedidos as $pedido) {
            $csv .= "Pedido" . "," . $pedido->id . "," . $pedido->cantidad . "," . $pedido->tiempo_preparacion . "," . $pedido->producto_id . "," . $pedido->mesa_id . "," . $pedido->estado_id . "\n";
        }
        
        $filename = "./assets/data.csv";

        $archivo = fopen($filename, "w");

        fwrite($archivo, $csv);
        fclose($archivo);
        return $filename;
    }

    public function DescargarPDF()
    {
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->Image("./assets/logo.jpg", 10, 6, 100);

        $pdf->Output();
    }
    #endregion

    #region Private Methods
    private function CargarABase($archivo, bool $encriptado)
    {
        $pdo = new PDO('mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'].';charset=utf8', $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $pdo->beginTransaction();

        try {
            while (!feof($archivo)) 
            {
                $parametros = fgetcsv($archivo);
                
                if ($parametros) 
                {
                    if (!$this->GenerarEntidad($pdo, $parametros, $encriptado))
                    {
                        throw new \Exception("Error en el archivo csv.");
                    }
                }
            }
            $pdo->commit();

        } catch (\Throwable $th) {
            $pdo->rollBack();
            throw $th;
        } finally {
            unset($pdo);
        }
    }

    private function GenerarEntidad(PDO $pdo, array $parametros, bool $encriptado)
    {
        $tipo = $parametros[0];
        $resultado = false;
        
        switch ($tipo) {
            case 'Rol':
                $query = $pdo->prepare("INSERT INTO roles (id, nombre) VALUES (:id, :nombre)");
                $query->bindParam(":id", $parametros[1], PDO::PARAM_INT);
                $query->bindParam(":nombre", $parametros[2]);
                $resultado = $query->execute();
                break;
            case 'Usuario':
                $clave = $encriptado ? password_hash($parametros[3], PASSWORD_DEFAULT) : $parametros[3];
                $query = $pdo->prepare("INSERT INTO usuarios (id, nombre, clave, rol_id) VALUES (:id, :nombre, :clave, :rol_id)");
                $query->bindParam(":id", $parametros[1], PDO::PARAM_INT);
                $query->bindParam(":nombre", $parametros[2]);
                $query->bindParam(":clave", $clave);
                $query->bindParam(":rol_id", $parametros[4], PDO::PARAM_INT);
                $resultado = $query->execute();
                break;
            case 'Producto':
                $query = $pdo->prepare("INSERT INTO productos (id, descripcion, precio, rol_id, tiempo_preparacion) VALUES (:id, :descripcion, :precio, :rol_id, :tiempo_preparacion)");
                $query->bindParam(":id", $parametros[1], PDO::PARAM_INT);
                $query->bindParam(":descripcion", $parametros[2]);
                $query->bindParam(":precio", $parametros[3], PDO::PARAM_INT);
                $query->bindParam(":rol_id", $parametros[4], PDO::PARAM_INT);
                if (isset($parametros[5]))
                    $query->bindParam(":tiempo_preparacion", $parametros[5], PDO::PARAM_INT);
                $resultado = $query->execute();
                break;
            case 'EstadoPedidos':
                $query = $pdo->prepare("INSERT INTO estado_pedidos (id, descripcion) VALUES (:id, :descripcion)");
                $query->bindParam(":id", $parametros[1], PDO::PARAM_INT);
                $query->bindParam(":descripcion", $parametros[2]);
                $resultado = $query->execute();
                break;
            case 'EstadoMesas':
                $query = $pdo->prepare("INSERT INTO estado_mesas (id, descripcion) VALUES (:id, :descripcion)");
                $query->bindParam(":id", $parametros[1], PDO::PARAM_INT);
                $query->bindParam(":descripcion", $parametros[2]);
                $resultado = $query->execute();
                break;
            case 'Mesa':
                $query = $pdo->prepare("INSERT INTO mesas (id, cliente, codigo, foto, estado_id) VALUES (:id, :cliente, :codigo, :foto, :estado_id)");
                $query->bindParam(":id", $parametros[1], PDO::PARAM_INT);
                $query->bindParam(":cliente", $parametros[2]);
                $query->bindParam(":codigo", $parametros[3]);
                $query->bindParam(":foto", $parametros[4]);
                $query->bindParam(":estado_id", $parametros[5], PDO::PARAM_INT);
                $resultado = $query->execute();
                break;
            case 'Pedido':
                $query = $pdo->prepare("INSERT INTO pedidos (id, cantidad, tiempo_preparacion, producto_id, mesa_id, estado_id) VALUES (:id, :cantidad, :tiempo_preparacion, :producto_id, :mesa_id, :estado_id)");
                $query->bindParam(":id", $parametros[1], PDO::PARAM_INT);
                $query->bindParam(":cantidad", $parametros[2], PDO::PARAM_INT);
                $query->bindParam(":tiempo_preparacion", $parametros[3], PDO::PARAM_INT);
                $query->bindParam(":producto_id", $parametros[4], PDO::PARAM_INT);
                $query->bindParam(":mesa_id", $parametros[5], PDO::PARAM_INT);
                $query->bindParam(":estado_id", $parametros[6], PDO::PARAM_INT);
                $resultado = $query->execute();
                break;
            default:
                throw new \BadMethodCallException("Entidad invalida. Entidad: " . json_encode($parametros));
                break;
        }

        return $resultado;
    }

    #endregion
}