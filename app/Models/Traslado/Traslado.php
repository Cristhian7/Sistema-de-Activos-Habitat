<?php

namespace App\Models\Traslado;

use Illuminate\Database\Eloquent\Model;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;

class Traslado extends Model
{
    protected $table = 'traslado';
    protected $primaryKey = 'id_traslado';
    public $timestamps = false;
    
    protected $fillable = [
        'equipo_id_equipo',
        'empleado_id_empleado',
        'motivo_traslado',
        'descripcion',
        'fecha_envio'
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id_equipo', 'id_equipo');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id_empleado', 'id_empleado');
    }
}