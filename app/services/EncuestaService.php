<?php
namespace App\Services;

use App\Interfaces\IEncuestaService;
use App\Interfaces\IMesaService;
use App\Models\Encuesta;

class EncuestaService implements IEncuestaService
{
    private static IEncuestaService $encuestaService;
    private IMesaService $_mesaService;

    protected function __construct()
    {
        $this->_mesaService = MesaService::obtenerInstancia();
    }

    protected function __clone() {}

    public static function obtenerInstancia() : EncuestaService
    {
        if (!isset(self::$encuestaService)) 
        {
            self::$encuestaService = new EncuestaService();
        }
        return self::$encuestaService;
    }

    public function Responder(string $codigo, int $id, $datos)
    {
        $mesa = $this->_mesaService->ObtenerMesaCerradaPorIdYCodigo($id, $codigo);

        if (is_null($mesa))
        {
            throw new \Exception("Datos de mesa invalidos.", 404);
        }

        $encuesta = new Encuesta;
        $encuesta->puntuacion_mesa = $datos->puntuacionMesa;
        $encuesta->puntuacion_restaurante = $datos->puntuacionRestaurante;
        $encuesta->puntuacion_mozo = $datos->puntuacionMozo;
        $encuesta->puntuacion_cocinero = $datos->puntuacionCocinero;
        $encuesta->opinion = $datos->opinion;
        $encuesta->mesa_id = $mesa->id;

        return $encuesta->save();
    }
}