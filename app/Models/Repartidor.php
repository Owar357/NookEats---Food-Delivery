<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repartidor extends Model
{
    use HasFactory;

    public function  usuario()
    {
       return $this->belongsTo(Usuario::class);
    }
    
    
    public function  pedidos()
    {
       return $this->hasMany(Pedido::class);
    }
}
