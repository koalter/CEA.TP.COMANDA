<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'roles';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nombre'
    ];

    public function usuarios() 
    {
        return $this->hasMany(Usuario::class);
    }

    public function productos() 
    {
        return $this->hasMany(Producto::class);
    }
}