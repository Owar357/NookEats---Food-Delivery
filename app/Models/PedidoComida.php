<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoComida extends Model
{
    use HasFactory;

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }


    public function comidaRestaurante()
    {
        return $this->belongsTo(ComidaRestaurante::class);
    }

    
}
