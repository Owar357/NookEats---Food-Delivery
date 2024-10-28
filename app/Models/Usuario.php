<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    
    public function  restaurante()
    {
        return $this -> hasOne(Restaurante::class);
    }

    public function pedidos()
    {
        return $this -> hasMany(Restaurante::class);
    }
 
}
