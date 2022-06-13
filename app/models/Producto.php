<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model 
{
    protected $primaryKey = 'id';
    protected $table = 'productos';
    public $incrementing = true;
    public $timestamps = false;
    protected $with = ['rol'];

    protected $fillable = [
        'descripcion', 'rol_id'
    ];

    public function rol() 
    {
        return $this->belongsTo(Rol::class);
    }
}