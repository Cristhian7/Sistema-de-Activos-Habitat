<?php

namespace App\Models\Notificacion;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';
    protected $primaryKey = 'id_notificacion';
    public $timestamps = false;

    public const TIPO_TRASLADO = 'traslado';
    public const TIPO_BAJA = 'baja';

    protected $fillable = [
        'empleado_id_empleado',
        'tipo',
        'registro_id',
        'mensaje',
        'creado_por',
        'creado_en',
        'revisado_en',
    ];

    protected $casts = [
        'creado_en'   => 'datetime',
        'revisado_en' => 'datetime',
    ];

    public function scopeDe($query, $empleadoId)
    {
        return $query->where('empleado_id_empleado', $empleadoId);
    }

    public function scopePendientes($query)
    {
        return $query->whereNull('revisado_en');
    }

    public function getRevisadaAttribute(): bool
    {
        return $this->revisado_en !== null;
    }

    // Pantalla del traslado o de la baja a la que se refiere
    public function getUrlAttribute(): string
    {
        return $this->tipo === self::TIPO_BAJA
            ? route('bajas.show', $this->registro_id)
            : route('traslados.show', $this->registro_id);
    }

    public function getTipoTextoAttribute(): string
    {
        return $this->tipo === self::TIPO_BAJA ? 'Baja' : 'Traslado';
    }
}
