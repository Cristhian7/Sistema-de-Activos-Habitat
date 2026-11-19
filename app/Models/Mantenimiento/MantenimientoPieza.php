<?php

namespace App\Models\Mantenimiento;

use Illuminate\Database\Eloquent\Model;

/**
 * Pieza cambiada o agregada durante un mantenimiento. La fotografía es obligatoria.
 */
class MantenimientoPieza extends Model
{
    protected $table = 'mantenimiento_pieza';
    protected $primaryKey = 'id_mantenimiento_pieza';
    public $timestamps = false;

    public const ACCION_CAMBIO = 'CAMBIO';
    public const ACCION_AGREGADO = 'AGREGADO';

    public const ACCIONES = [
        self::ACCION_CAMBIO   => 'Cambiada',
        self::ACCION_AGREGADO => 'Agregada',
    ];

    // Tipos de pieza que se pueden registrar ("Otra" exige describirla)
    public const TIPO_OTRA = 'Otra';
    public const TIPOS = [
        'Memoria RAM',
        'SSD / Disco duro',
        'Teclado',
        'Pantalla',
        'Batería',
        'Cargador',
        'Mouse',
        'Tarjeta madre',
        'Ventilador',
        self::TIPO_OTRA,
    ];

    public const MIMES_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];
    public const MAX_KB = 5120;
    public const MAX_POR_MANTENIMIENTO = 10;

    // Columnas sin la fotografía: las listas no cargan los binarios (la foto se pide por su propia ruta)
    public const COLUMNAS_SIN_IMAGEN = [
        'id_mantenimiento_pieza', 'mantenimiento_id_mantenimiento', 'tipo_pieza', 'accion', 'descripcion', 'imagen_mime',
    ];

    protected $fillable = [
        'mantenimiento_id_mantenimiento',
        'tipo_pieza',
        'accion',
        'descripcion',
        'imagen_mime',
        'imagen',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'mantenimiento_id_mantenimiento', 'id_mantenimiento');
    }

    public function getAccionTextoAttribute(): string
    {
        return self::ACCIONES[$this->accion] ?? (string) $this->accion;
    }

    // "Memoria RAM (agregada)"
    public function getResumenAttribute(): string
    {
        return $this->tipo_pieza . ' (' . mb_strtolower($this->accion_texto) . ')';
    }

    /**
     * Si el contenido es una imagen permitida, devuelve su mime; si no, null.
     * Se detecta por el contenido real, no por el nombre ni por lo que declare el navegador.
     */
    public static function mimeDeImagen(?string $binario): ?string
    {
        if ($binario === null || $binario === '') {
            return null;
        }

        $info = @getimagesizefromstring($binario);
        $mime = $info['mime'] ?? null;

        return in_array($mime, self::MIMES_PERMITIDOS, true) ? $mime : null;
    }
}
