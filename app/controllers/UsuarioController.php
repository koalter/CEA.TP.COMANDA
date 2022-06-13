<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Services\UsuarioService;
use App\Interfaces\IApiUsable;

class UsuarioController implements IApiUsable
{
  public function __construct()
  {
    $this->_usuarioService = UsuarioService::obtenerInstancia();
  }
  
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $rol = $parametros['rol'];

    $result = $this->_usuarioService->CargarUno($nombre, $rol);

    $payload = json_encode(array("mensaje" => $result ? "Usuario creado con exito" : "El usuario no pudo ser creado"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];

    // Buscamos por primary key
    // $usuario = Usuario::find($usr);

    // Buscamos por attr usuario
    $usuario = Usuario::where('usuario', $usr)->first();

    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = $this->_usuarioService->TraerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usrModificado = $parametros['usuario'];
    $usuarioId = $args['id'];

    // Conseguimos el objeto
    $usr = Usuario::where('id', '=', $usuarioId)->first();

    // Si existe
    if ($usr !== null) {
      // Seteamos un nuevo usuario
      $usr->usuario = $usrModificado;
      // Guardamos en base de datos
      $usr->save();
      $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];
    // Buscamos el usuario
    $usuario = Usuario::find($usuarioId);
    // Borramos
    $usuario->delete();

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
