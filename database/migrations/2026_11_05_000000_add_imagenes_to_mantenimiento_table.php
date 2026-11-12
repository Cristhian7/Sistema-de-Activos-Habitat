<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fotografías propias del mantenimiento (componente cambiado, RAM, disco, etc.).
     * Son independientes de las imágenes con las que se registró el equipo.
     */
    public function up(): void
    {
        Schema::table('mantenimiento', function (Blueprint $table) {
            if (!Schema::hasColumn('mantenimiento', 'imagen_mantenimiento_1')) {
                $table->longText('imagen_mantenimiento_1')->nullable();
            }
            if (!Schema::hasColumn('mantenimiento', 'imagen_mantenimiento_2')) {
                $table->longText('imagen_mantenimiento_2')->nullable();
            }
        });

        // longText -> longblob, igual que las imágenes de la tabla equipo
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE mantenimiento MODIFY imagen_mantenimiento_1 LONGBLOB NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE mantenimiento MODIFY imagen_mantenimiento_2 LONGBLOB NULL');
    }

    public function down(): void
    {
        Schema::table('mantenimiento', function (Blueprint $table) {
            $table->dropColumn(['imagen_mantenimiento_1', 'imagen_mantenimiento_2']);
        });
    }
};
