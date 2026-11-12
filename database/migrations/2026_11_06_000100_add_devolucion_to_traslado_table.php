<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite finalizar un traslado (devolver el equipo):
     *  - fecha_devolucion: NULL = traslado vigente; con fecha = traslado finalizado.
     *  - motivo_devolucion: por qué se quitó el equipo al empleado.
     *  - tecnico_devolucion_id_empleado: técnico (usuario logueado) que registró la devolución.
     */
    public function up(): void
    {
        Schema::table('traslado', function (Blueprint $table) {
            if (!Schema::hasColumn('traslado', 'fecha_devolucion')) {
                $table->date('fecha_devolucion')->nullable()->after('fecha_envio');
            }
            if (!Schema::hasColumn('traslado', 'motivo_devolucion')) {
                $table->string('motivo_devolucion', 255)->nullable()->after('fecha_devolucion');
            }
            if (!Schema::hasColumn('traslado', 'tecnico_devolucion_id_empleado')) {
                $table->integer('tecnico_devolucion_id_empleado')->nullable()->after('motivo_devolucion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('traslado', function (Blueprint $table) {
            $table->dropColumn(['fecha_devolucion', 'motivo_devolucion', 'tecnico_devolucion_id_empleado']);
        });
    }
};
