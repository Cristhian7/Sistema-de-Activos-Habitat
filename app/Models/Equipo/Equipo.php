<?php

namespace App\Models\Equipo;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    // Depreciación en línea recta: el equipo pierde 33.33% de su precio de compra por año
    // durante su vida útil de 3 años, contado desde la fecha de compra (por meses completos).
    public const VIDA_UTIL_ANOS = 3;

    protected $table = 'equipo';
    protected $primaryKey = 'id_equipo';
    
    public $timestamps = false; 

    protected $fillable = [
        'codigo_contable',
        'nombre_equipo',
        'tipo_equipo_id_tipo_equipo',
        'modelo_id_modelo',
        'procesador',
        'ram',
        'tipo_ram',
        'ssd',
        'st',
        'ex',
        'fecha_compra',
        'precio_compra',
        'valor_libros',
        'estado_id_estado',
        'notas_adicionales',
        'imagen_equipo',
        'imagen_codigo',
    ];

    /**
     * El valor en libros se guarda calculado en cada guardado y, además, se recalcula siempre al leerlo,
     * así nunca queda desactualizado aunque pasen los meses. No se captura a mano.
     */
    protected static function booted(): void
    {
        static::saving(function (Equipo $equipo) {
            // Solo si el modelo trae los datos necesarios (por si se cargó con columnas limitadas)
            if (array_key_exists('precio_compra', $equipo->attributes) && array_key_exists('fecha_compra', $equipo->attributes)) {
                $equipo->attributes['valor_libros'] = static::depreciar(
                    $equipo->attributes['precio_compra'],
                    $equipo->attributes['fecha_compra']
                )['valor_libros'];
            }
        });
    }

    /**
     * Depreciación de un equipo a una fecha (hoy si no se indica).
     * Devuelve: meses (completos desde la compra, máx. vida útil), porcentaje depreciado,
     * depreciación acumulada y valor en libros. Valores en null si falta el precio o la fecha.
     */
    public static function depreciar($precioCompra, $fechaCompra, $al = null): array
    {
        if ($precioCompra === null || $precioCompra === '' || empty($fechaCompra)) {
            return ['meses' => null, 'porcentaje' => null, 'acumulada' => null, 'valor_libros' => null];
        }

        $precio = (float) $precioCompra;
        $mesesVida = self::VIDA_UTIL_ANOS * 12;

        $diferencia = Carbon::parse($fechaCompra)->startOfDay()->diff(($al ? Carbon::parse($al) : now())->startOfDay());
        // Una fecha de compra futura todavía no deprecia
        $transcurridos = $diferencia->invert ? 0 : ($diferencia->y * 12 + $diferencia->m);
        $meses = min($transcurridos, $mesesVida);

        $acumulada = round($precio * $meses / $mesesVida, 2);

        return [
            'meses'        => $meses,
            'porcentaje'   => round($meses / $mesesVida * 100, 2),
            'acumulada'    => $acumulada,
            'valor_libros' => round($precio - $acumulada, 2),
        ];
    }

    // Valor en libros al día de hoy (precio de compra menos la depreciación acumulada)
    public function getValorLibrosAttribute($valorGuardado)
    {
        $calculado = static::depreciar($this->attributes['precio_compra'] ?? null, $this->attributes['fecha_compra'] ?? null)['valor_libros'];

        return $calculado ?? $valorGuardado;
    }

    // Depreciación al día de hoy: meses, porcentaje, acumulada y valor en libros
    public function getDepreciacionAttribute(): array
    {
        return static::depreciar($this->attributes['precio_compra'] ?? null, $this->attributes['fecha_compra'] ?? null);
    }

    // Relación con Tipo de Equipo
    public function tipoEquipo()
    {
        return $this->belongsTo(\App\Models\TipoEquipo\TipoEquipo::class, 'tipo_equipo_id_tipo_equipo', 'id_tipo_equipo');
    }

    // Relación con Modelo
    public function modelo()
    {
        return $this->belongsTo(\App\Models\Modelo\Modelo::class, 'modelo_id_modelo', 'id_modelo');
    }

    // Traslado vigente: a quién está asignado el equipo hoy (null = disponible)
    public function asignacionActual()
    {
        return $this->hasOne(\App\Models\Traslado\Traslado::class, 'equipo_id_equipo', 'id_equipo')
            ->ofMany(['id_traslado' => 'max'], function ($query) {
                // Vigente y con un empleado que no esté dado de baja
                $query->whereNull('fecha_devolucion')->deEmpleadoActivo();
            });
    }

    // Relación con Estado
    public function estado()
    {
        return $this->belongsTo(\App\Models\Estado\Estado::class, 'estado_id_estado', 'id_estado');
    }

    // Relación con Marca (A través del Modelo, respetando tu diagrama relacional)
    public function marca()
    {
        return $this->hasOneThrough(
            \App\Models\Marca\Marca::class,         // Modelo final al que queremos llegar
            \App\Models\Modelo\Modelo::class,       // Modelo intermedio
            'id_modelo',                            // Llave primaria en la tabla modelo
            'id_marca',                             // Llave primaria en la tabla marca
            'modelo_id_modelo',                     // Llave foránea en la tabla equipo
            'marca_id_marca'                        // Llave foránea en la tabla modelo
        );
    }
}