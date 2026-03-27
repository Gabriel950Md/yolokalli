<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamos'; 

    protected $fillable = [
        'nombre_libro',
        'a_quien',
        'telefono',
        'id_libro',
        'fecha_prestamo',
        'fecha_devolucion',
        'estatus'
    ];

    protected $attributes = [
        'estatus' => 'prestado'
    ];
}