<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guarda por qué terminó un traslado, para distinguir una baja del empleado
     * de una devolución normal o de una reasignación:
     *   DEVOLUCION   : se devolvió el equipo (botón "Devolver")
     *   REASIGNACION : el equipo pasó a otro empleado
     *   BAJA_EMPLEADO: el empleado fue dado de baja
     */
    public function up(): void
    {
        Schema::table('traslado', function (Blueprint $table) {
            if (!Schema::hasColumn('traslado', 'causa_devolucion')) {
                $table->string('causa_devolucion', 20)->nullable()->after('motivo_devolucion');
            }
        });

        // Clasifica las devoluciones ya existentes con los textos que el propio sistema generó
        DB::table('traslado')
            ->whereNotNull('fecha_devolucion')->whereNull('causa_devolucion')
            ->where('motivo_devolucion', 'Empleado dado de baja')
            ->update(['causa_devolucion' => 'BAJA_EMPLEADO']);

        DB::table('traslado')
            ->whereNotNull('fecha_devolucion')->whereNull('causa_devolucion')
            ->where('motivo_devolucion', 'like', 'Reasignado a %')
            ->update(['causa_devolucion' => 'REASIGNACION']);

        DB::table('traslado')
            ->whereNotNull('fecha_devolucion')->whereNull('causa_devolucion')
            ->update(['causa_devolucion' => 'DEVOLUCION']);
    }

    public function down(): void
    {
        Schema::table('traslado', function (Blueprint $table) {
            $table->dropColumn('causa_devolucion');
        });
    }
};
