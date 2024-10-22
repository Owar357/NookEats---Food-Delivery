<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPedido extends Model
{
    use HasFactory;


    public function  usuario()
    {
       return $this->belongsTo(Usuario::class);
    }


    public function  pedido()
    {
       return $this->belongsTo(Pedido::class);
    }
}
