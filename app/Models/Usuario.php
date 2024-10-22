<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    public function  repartidor()
    {
        return $this -> hasOne(Repartidor::class);
    }

    public function  restaurante()
    {
        return $this -> hasOne(Restaurante::class);
    }

    public function  usuarioPedidos()
    {
       return $this->hasMany(UsuarioPedido::class);
    }

    public function  roles()
    {
       return $this->hasMany(Rol::class);
    }
}
