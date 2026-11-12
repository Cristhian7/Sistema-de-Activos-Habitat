<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fecha en que el empleado fue dado de baja (para el reporte de bajas de empleados).
     * Las bajas anteriores se reconstruyen con la devolución que el sistema generó al darlo de baja;
     * si no tenía equipo asignado no hay forma de saber la fecha y queda en NULL.
     */
    public function up(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            if (!Schema::hasColumn('empleado', 'fecha_baja')) {
                $table->date('fecha_baja')->nullable();
            }
        });

        $empleados = DB::table('traslado')
            ->where('causa_devolucion', 'BAJA_EMPLEADO')
            ->whereNotNull('fecha_devolucion')
            ->select('empleado_id_empleado', DB::raw('MAX(fecha_devolucion) as fecha'))
            ->groupBy('empleado_id_empleado')
            ->get();

        foreach ($empleados as $fila) {
            DB::table('empleado')
                ->where('id_empleado', $fila->empleado_id_empleado)
                ->whereNull('fecha_baja')
                ->whereIn('estado_id_estado', function ($q) {
                    $q->select('id_estado')->from('estado')->where('nombre_estado', 'Baja');
                })
                ->update(['fecha_baja' => $fila->fecha]);
        }
    }

    public function down(): void
    {
        Schema::table('empleado', function (Blueprint $table) {
            $table->dropColumn('fecha_baja');
        });
    }
};
