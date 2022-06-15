<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'pedidos';
    public $incrementing = true;
    public $timestamps = false;
    protected $with = [
        'producto',
        'estado',
    ];

    protected $fillable = [
        'cantidad', 'producto_id', 'estado_id', 'mesa_id', 'deleted_at'
    ];

    public function producto() 
    {
        return $this->belongsTo(Producto::class);
    }

    public function estado() 
    {
        return $this->belongsTo(EstadoPedidos::class, 'estado_id', 'id');
    }

    public function mesa() 
    {
        $this->belongsTo(Mesa::class, 'mesa_id', 'id');
    }
}