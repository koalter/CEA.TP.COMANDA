<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'usuarios';
    public $incrementing = true;
    public $timestamps = false;
    protected $with = ['rol'];

    // const DELETED_AT = 'fechaBaja';

    protected $fillable = [
        'nombre', 'rol_id', 'deleted_at'
    ];

    public function rol() 
    {
        return $this->belongsTo(Rol::class);
    }
}