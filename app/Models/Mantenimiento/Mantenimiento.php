<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimiento';
    protected $primaryKey = 'id_mantenimiento';
    
    // Si tu tabla no usa los timestamps por defecto (created_at / updated_at), descomenta la siguiente línea:
    // public $timestamps = false;

    protected $fillable = [
        'equipo_id_equipo',
        'empleado_id_empleado',
        'descripcion',
        'fecha_mantenimiento',
    ];

    // Relación con el equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id_equipo', 'id_equipo');
    }

    // Relación con el empleado (técnico o responsable)
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id_empleado', 'id_empleado');
    }
}