<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    use HasFactory;


    public function  tipoNegocio()
    {
       return $this->belongsTo(TipoNegocio::class);
    }

    public function  usuario()
    {
       return $this->belongsTo(Usuario::class);
    }


    public function  comidasRestaurante()
    {
       return $this->hasMany(ComidaRestaurante::class);
    }

    public function  horarios()
    {
       return $this->hasMany(Horario::class);
    }
}
