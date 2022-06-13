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
    protected $with = ['estado'];

    protected $fillable = [
        'cliente', 'estado_id', 'deleted_at'
    ];

    public function estado() 
    {
        return $this->belongsTo(EstadoMesas::class, "estado_id", "id");
    }
}