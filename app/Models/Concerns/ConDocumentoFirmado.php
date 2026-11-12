<?php

namespace App\Models\Concerns;

use App\Models\Empleado\Empleado;

/**
 * Para los modelos que llevan un documento firmado como resguardo (traslado y baja).
 * Requiere las columnas documento_firmado, documento_nombre, documento_fecha y documento_cargado_por.
 */
trait ConDocumentoFirmado
{
    public function getTieneDocumentoAttribute(): bool
    {
        return !empty($this->documento_firmado);
    }

    // Solo los registros que todavía no tienen el documento firmado
    public function scopeSinDocumento($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('documento_firmado')->orWhere('documento_firmado', '');
        });
    }

    // Empleado que subió el documento
    public function documentoCargadoPor()
    {
        return $this->belongsTo(Empleado::class, 'documento_cargado_por', 'id_empleado');
    }
}
