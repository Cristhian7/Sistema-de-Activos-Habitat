<?php

namespace App\Models\Traslado;

use Illuminate\Database\Eloquent\Model;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;
use App\Models\Estado\Estado;

class Traslado extends Model
{
    use \App\Models\Concerns\ConDocumentoFirmado;

    protected $table = 'traslado';
    protected $primaryKey = 'id_traslado';
    public $timestamps = false;

    // Causas por las que termina un traslado
    public const CAUSA_DEVOLUCION = 'DEVOLUCION';       // se devolvió el equipo (botón "Devolver")
    public const CAUSA_REASIGNACION = 'REASIGNACION';   // el equipo pasó a otro empleado
    public const CAUSA_BAJA_EMPLEADO = 'BAJA_EMPLEADO'; // el empleado fue dado de baja

    protected $fillable = [
        'equipo_id_equipo',
        'empleado_id_empleado',
        'tecnico_id_empleado',
        'motivo_traslado',
        'descripcion',
        'fecha_envio',
        'fecha_devolucion',
        'motivo_devolucion',
        'causa_devolucion',
        'tecnico_devolucion_id_empleado',
        'documento_firmado',
        'documento_nombre',
        'documento_fecha',
        'documento_cargado_por',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id_equipo', 'id_equipo');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id_empleado', 'id_empleado');
    }

    // Técnico que realizó el traslado (usuario que lo registró)
    public function tecnico()
    {
        return $this->belongsTo(Empleado::class, 'tecnico_id_empleado', 'id_empleado');
    }

    // Técnico que registró la devolución del equipo
    public function tecnicoDevolucion()
    {
        return $this->belongsTo(Empleado::class, 'tecnico_devolucion_id_empleado', 'id_empleado');
    }

    // Un traslado está vigente mientras no se haya devuelto el equipo
    public function getVigenteAttribute(): bool
    {
        return $this->fecha_devolucion === null;
    }

    // Texto de la causa por la que terminó el traslado (null si sigue vigente)
    public function getCausaTextoAttribute(): ?string
    {
        if ($this->vigente) {
            return null;
        }

        return match ($this->causa_devolucion) {
            self::CAUSA_BAJA_EMPLEADO => 'Empleado dado de baja',
            self::CAUSA_REASIGNACION  => 'Reasignado a otro empleado',
            default                   => 'Equipo devuelto',
        };
    }

    // Solo los traslados vigentes (equipo todavía sin devolver)
    public function scopeVigentes($query)
    {
        return $query->whereNull('fecha_devolucion');
    }

    // Solo los traslados cuyo empleado NO está dado de baja
    public function scopeDeEmpleadoActivo($query)
    {
        return $query->whereNotIn('empleado_id_empleado', function ($sub) {
            $sub->select('id_empleado')->from('empleado')->where('estado_id_estado', Estado::idBaja());
        });
    }

    /**
     * Traslado vigente de un equipo: a quién está asignado hoy, o null si está disponible.
     * Un traslado cuyo empleado fue dado de baja no cuenta: ese empleado ya no tiene el equipo.
     */
    public static function vigenteDelEquipo(int $idEquipo, ?int $excluirId = null): ?self
    {
        return static::vigentes()
            ->deEmpleadoActivo()
            ->where('equipo_id_equipo', $idEquipo)
            ->when($excluirId, fn ($q) => $q->where('id_traslado', '!=', $excluirId))
            ->orderByDesc('id_traslado')
            ->first();
    }

    /**
     * Traslado vigente de un empleado, o null si no tiene equipo.
     * Un equipo dado de baja ya no cuenta como equipo del empleado.
     */
    public static function vigenteDelEmpleado(int $idEmpleado, ?int $excluirId = null): ?self
    {
        return static::vigentes()
            ->where('empleado_id_empleado', $idEmpleado)
            ->whereHas('equipo', fn ($q) => $q->where('estado_id_estado', '!=', Estado::idBaja()))
            ->when($excluirId, fn ($q) => $q->where('id_traslado', '!=', $excluirId))
            ->orderByDesc('id_traslado')
            ->first();
    }
}
