<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'mesas';
    public $incrementing = true;
    public $timestamps = false;
    protected $with = [
        'estado',
        'pedidos',
        'encuesta'
    ];
  
    protected $fillable = [
        'cliente', 'estado_id', 'deleted_at'
    ];

    public function estado() 
    {
        return $this->belongsTo(EstadoMesas::class, "estado_id", "id");
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, "mesa_id", "id");
    }

    public function encuesta()
    {
        return $this->hasOne(Encuesta::class, "mesa_id", "id");
    }
}