<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoMesas extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'estado_mesas';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'descripcion'
    ];

    public function mesas() 
    {
        return $this->hasMany(Mesa::class, "estado_id", "id");
    }
}