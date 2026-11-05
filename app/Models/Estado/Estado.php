<?php

namespace App\Models\Estado;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = ['nombre_estado'];

    // Relación con empleados
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'estado_id_estado', 'id_estado');
    }
}

