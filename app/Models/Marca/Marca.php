<?php

namespace App\Models\Marca;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';
    protected $primaryKey = 'id_marca';
    public $timestamps = false;

    protected $fillable = ['nombre_marca'];

    // Modelos de esta marca
    public function modelos()
    {
        return $this->hasMany(\App\Models\Modelo\Modelo::class, 'marca_id_marca', 'id_marca');
    }

    // Equipos de esta marca (a través de sus modelos)
    public function equipos()
    {
        return $this->hasManyThrough(
            \App\Models\Equipo\Equipo::class,
            \App\Models\Modelo\Modelo::class,
            'marca_id_marca',   // llave de modelo hacia marca
            'modelo_id_modelo', // llave de equipo hacia modelo
            'id_marca',
            'id_modelo'
        );
    }
}
