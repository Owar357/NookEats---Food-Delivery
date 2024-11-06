<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComidaRestaurante extends Model
{
    use HasFactory;

    protected $table  = "comidas_restaurante"; 

    public function  categoria()
    {
        return $this -> belongsTo(Categoria::class);
    }


    public function  pedidoComidas()
    {
        return $this -> hasMany(PedidoComida::class);
    }
}
