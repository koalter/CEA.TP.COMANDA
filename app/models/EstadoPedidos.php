<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedidos extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'estado_pedidos';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'descripcion'
    ];

    public function pedidos() 
    {
        return $this->hasMany(Pedido::class, "estado_id", "id");
    }
}