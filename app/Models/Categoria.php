<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    public function  comidasRestaurante()
    {
        return $this -> hasMany(ComidaRestaurante::class);
    }

    public function  restaurante()
    {
        return $this -> belongsTo(Restaurante::class);
    }
    
}
