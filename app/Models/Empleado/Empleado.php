<?php

namespace App\Models\Empleado;

use Illuminate\Database\Eloquent\Model;
use App\Models\Puesto\Puesto;
use App\Models\Afiliado\Afiliado;
use App\Models\Estado\Estado;
use App\Models\Rol\Rol;

class Empleado extends Model
{
    protected $table = 'empleado';
    protected $primaryKey = 'id_empleado';
    public $timestamps = false;

    protected $fillable = [
        'nombre_empleado',
        'apellido_empleado',
        'cod_l4',
        'puesto_id_puesto',
        'afiliado_id_afiliado',
        'estado_id_estado',
        'rol_id_rol',
    ];

    // Relaciones con las llaves foráneas
    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'puesto_id_puesto', 'id_puesto');
    }

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id_afiliado', 'id_afiliado');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id_estado', 'id_estado');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id_rol', 'id_rol');
    }
}