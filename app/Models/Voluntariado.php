<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voluntariado extends Model
{
    use HasFactory;

    protected $table = 'voluntariado'; 
    protected $fillable = ['nombre', 'gmail', 'telefono', 'tipo_voluntariado', 'mensaje'];
}
