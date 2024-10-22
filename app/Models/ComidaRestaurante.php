<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComidaRestaurante extends Model
{
    use HasFactory;

    
    public function  comida()
    {
        return $this -> belongsTo(Comida::class);
    }

    public function  categoria()
    {
        return $this -> belongsTo(Categoria::class);
    }

    
    public function  restaurante()
    {
        return $this -> belongsTo(Restaurante::class);
    }
    

    public function  pedidoComidas()
    {
        return $this -> hasMany(PedidoComida::class);
    }
}
