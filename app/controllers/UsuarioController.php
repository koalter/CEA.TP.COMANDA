<?php
namespace App\Controllers;

use App\Services\UsuarioService;
use App\Interfaces\IApiUsable;
use App\Interfaces\IFileService;
use App\Interfaces\IUsuarioService;
use App\Services\FileService;

class UsuarioController implements IApiUsable
{
  private IUsuarioService $_usuarioService;
  private IFileService $_fileService;

  public function __construct()
  {
    $this->_usuarioService = UsuarioService::obtenerInstancia();
    $this->_fileService = FileService::obtenerInstancia();
  }

  public function Login($request, $response, $args) 
  {
    $body = $request->getParsedBody();
    $username = $body['usuario'];
    $password = $body['password'];

    $token = $this->_usuarioService->Login($username, $password);

    $newResponse = $response->withStatus(200);

    $newResponse->getBody()->write(json_encode($token));

    return $newResponse->withHeader("Content-Type", "application/json");
  }
  
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $password = $parametros['password'];
    $rol = $parametros['rol'];

    $result = $this->_usuarioService->CargarUno($nombre, $password, $rol);

    $payload = json_encode(array("mensaje" => $result ? "Usuario creado con exito" : "El usuario no pudo ser creado"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  // public function TraerUno($request, $response, $args)
  // {
  //   // Buscamos usuario por nombre
  //   $usr = $args['usuario'];

  //   // Buscamos por primary key
  //   // $usuario = Usuario::find($usr);

  //   // Buscamos por attr usuario
  //   $usuario = Usuario::where('usuario', $usr)->first();

  //   $payload = json_encode($usuario);

  //   $response->getBody()->write($payload);
  //   return $response
  //     ->withHeader('Content-Type', 'application/json');
  // }

  public function TraerTodos($request, $response, $args)
  {
    $lista = $this->_usuarioService->TraerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  // public function ModificarUno($request, $response, $args)
  // {
  //   $parametros = $request->getParsedBody();

  //   $usrModificado = $parametros['usuario'];
  //   $usuarioId = $args['id'];

  //   // Conseguimos el objeto
  //   $usr = Usuario::where('id', '=', $usuarioId)->first();

  //   // Si existe
  //   if ($usr !== null) {
  //     // Seteamos un nuevo usuario
  //     $usr->usuario = $usrModificado;
  //     // Guardamos en base de datos
  //     $usr->save();
  //     $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
  //   } else {
  //     $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
  //   }

  //   $response->getBody()->write($payload);
  //   return $response
  //     ->withHeader('Content-Type', 'application/json');
  // }

  // public function BorrarUno($request, $response, $args)
  // {
  //   $usuarioId = $args['id'];
  //   // Buscamos el usuario
  //   $usuario = Usuario::find($usuarioId);
  //   // Borramos
  //   $usuario->delete();

  //   $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

  //   $response->getBody()->write($payload);
  //   return $response
  //     ->withHeader('Content-Type', 'application/json');
  // }

  public function CargarCSV($request, $response)
  {
    try {
      $body = $request->getParsedBody();
      $archivo = $_FILES['archivo'];
      $passwordEncriptado = isset($body['encriptado']) ? boolval($body['encriptado']) : false;
      $resultado = $this->_fileService->CargarCSV($archivo['tmp_name'], $passwordEncriptado);
      
      $payload = json_encode($resultado);
      $status = 200;

    } catch (\Throwable $th) {
      $payload = json_encode($th->getMessage());
      $status = 400;
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus($status);
  }

  public function DescargarCSV($request, $response)
  {
    try {
      $filename = $this->_fileService->DescargarCSV();

      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($filename).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filename));

      flush();
      if (readfile($filename, true))
      {
        $payload = json_encode(true);
        $status = 200;
      }
      else 
      {
        $payload = "Error al leer el archivo desde el servidor!";
        $status = 500;
      }

    } catch (\Throwable $th) {
      $payload = json_encode($th->getMessage());
      $status = 400;
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus($status);
  }

  public function DescargarPDF($request, $response)
  {
    $resultado = $this->_fileService->DescargarPDF();
    $response->getBody()->write($resultado);
  }
}
