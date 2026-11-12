<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guarda el técnico (empleado del usuario logueado) que registró la baja.
     */
    public function up(): void
    {
        Schema::table('baja', function (Blueprint $table) {
            if (!Schema::hasColumn('baja', 'tecnico_id_empleado')) {
                $table->integer('tecnico_id_empleado')->nullable()->after('empleado_id_empleado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('baja', function (Blueprint $table) {
            $table->dropColumn('tecnico_id_empleado');
        });
    }
};
