<?php

namespace App\Models\Modelo;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $table = 'modelo';
    protected $primaryKey = 'id_modelo';
    public $timestamps = false;

    protected $fillable = ['nombre_modelo', 'marca_id_marca'];

    // Agrega esta relación para que 'modelo.marca' funcione correctamente
    public function marca()
    {
        return $this->belongsTo(\App\Models\Marca\Marca::class, 'marca_id_marca', 'id_marca');
    }
}