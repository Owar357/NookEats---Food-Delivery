<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    public function  horarioDia()
    {
        return $this -> belongsTo(HorarioDia::class);
    }

    public function  restaurante()
    {
        return $this -> belongsTo(Restaurante::class);
    }
}
