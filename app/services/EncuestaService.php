<?php
namespace App\Services;

use App\DTO\EncuestaDTO;
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

    public function Responder(string $codigo, $datos)
    {
        $mesa = $this->_mesaService->ObtenerMesaCerradaPorIdYCodigo($codigo);

        if (is_null($mesa))
        {
            throw new \Exception("Datos de mesa invalidos.", 404);
        }

        
        $encuesta = Encuesta::where("mesa_id", "=", $mesa->id)->first();
        if (!is_null($encuesta)) 
        {
            return false;
        }

        $encuesta = new Encuesta;
        $encuesta->puntuacion_mesa = $datos->puntuacionMesa;
        $encuesta->puntuacion_restaurante = $datos->puntuacionRestaurante;
        $encuesta->puntuacion_mozo = $datos->puntuacionMozo;
        $encuesta->puntuacion_cocinero = $datos->puntuacionCocinero;
        $encuesta->opinion = $datos->opinion;
        $encuesta->promedio = ($datos->puntuacionMesa + $datos->puntuacionRestaurante + $datos->puntuacionMozo + $datos->puntuacionCocinero) / 4;
        $encuesta->mesa_id = $mesa->id;

        return $encuesta->save();
    }

    public function TraerMejores()
    {
        $encuestas = Encuesta::orderByDesc("promedio")
            ->limit(10)
            ->get();
            
        $dto = array();

        foreach ($encuestas as $encuesta) {
            $dto[] = new EncuestaDTO(
                $encuesta->id, 
                $encuesta->puntuacion_mesa, 
                $encuesta->puntuacion_restaurante, 
                $encuesta->puntuacion_mozo, 
                $encuesta->puntuacion_cocinero, 
                $encuesta->opinion);
        }

        return $dto;
    }
}