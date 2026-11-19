<?php

namespace App\Console\Commands;

use App\Models\Equipo\Equipo;
use Illuminate\Console\Command;

/**
 * Actualiza en la base el valor en libros guardado de cada equipo según su depreciación de hoy.
 * El sistema ya muestra siempre el valor calculado; esto solo mantiene al día el dato guardado
 * (por ejemplo, para consultas directas a la tabla). Se puede ejecutar cuando se quiera.
 */
class RecalcularValorLibros extends Command
{
    protected $signature = 'equipos:recalcular-valor-libros';

    protected $description = 'Recalcula el valor en libros guardado de todos los equipos (depreciación a 3 años)';

    public function handle(): int
    {
        $cambiados = 0;
        $total = 0;

        // No se cargan las fotos (campos binarios): no hacen falta para el cálculo
        Equipo::select(['id_equipo', 'precio_compra', 'fecha_compra', 'valor_libros'])
            ->orderBy('id_equipo')
            ->each(function (Equipo $equipo) use (&$cambiados, &$total) {
                $total++;
                $equipo->save();

                if ($equipo->wasChanged('valor_libros')) {
                    $cambiados++;
                }
            });

        $this->info("Equipos revisados: {$total}. Valores actualizados: {$cambiados}.");

        return self::SUCCESS;
    }
}
