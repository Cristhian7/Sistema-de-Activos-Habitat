<?php

namespace App\Models\Baja;

use Illuminate\Database\Eloquent\Model;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;

class Baja extends Model
{
    use \App\Models\Concerns\ConDocumentoFirmado;

    protected $table = 'baja';
    protected $primaryKey = 'id_baja';

    public $timestamps = false;

    protected $fillable = [
        'equipo_id_equipo',
        'empleado_id_empleado',
        'tecnico_id_empleado',
        'problema',
        'detalle',
        'anos_uso',
        'mes_uso',
        'necesita_reemplazo',
        'fecha_baja',
        'documento_firmado',
        'documento_nombre',
        'documento_fecha',
        'documento_cargado_por',
    ];

    // Tiempo de uso en texto: "5 años y 3 meses" (registros antiguos sin meses: solo años)
    public function getTiempoUsoAttribute(): string
    {
        if ($this->anos_uso === null && $this->mes_uso === null) {
            return 'N/A';
        }

        $anos = (int) $this->anos_uso;
        $partes = [$anos . ($anos === 1 ? ' año' : ' años')];

        if ($this->mes_uso !== null) {
            $meses = (int) $this->mes_uso;
            $partes[] = $meses . ($meses === 1 ? ' mes' : ' meses');
        }

        return implode(' y ', $partes);
    }

    // Equipo dado de baja
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id_equipo', 'id_equipo');
    }

    // Empleado que tenía asignado el equipo
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id_empleado', 'id_empleado');
    }

    // Técnico que realizó la baja (usuario que la registró)
    public function tecnico()
    {
        return $this->belongsTo(Empleado::class, 'tecnico_id_empleado', 'id_empleado');
    }
}
