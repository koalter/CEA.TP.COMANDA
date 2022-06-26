<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encuesta extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'encuestas';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'deleted_at'
    ];

    public function mesa() 
    {
        return $this->belongsTo(Mesa::class, 'mesa_id', 'id');
    }
}