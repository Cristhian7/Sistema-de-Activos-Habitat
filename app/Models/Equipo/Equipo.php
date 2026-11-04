<?php

namespace App\Models\Equipo;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
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