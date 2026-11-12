<?php

namespace App\Models\Mantenimiento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimiento';
    protected $primaryKey = 'id_mantenimiento';
    
    // La tabla no tiene created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'equipo_id_equipo',
        'empleado_id_empleado',
        'tecnico_id_empleado',
        'descripcion',
        'fecha_mantenimiento',
        'imagen_mantenimiento_1',
        'imagen_mantenimiento_2',
    ];

    // Relación con el equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id_equipo', 'id_equipo');
    }

    // Empleado al que corresponde el mantenimiento
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id_empleado', 'id_empleado');
    }

    // Técnico que realizó el mantenimiento (usuario que lo registró)
    public function tecnico()
    {
        return $this->belongsTo(Empleado::class, 'tecnico_id_empleado', 'id_empleado');
    }
}