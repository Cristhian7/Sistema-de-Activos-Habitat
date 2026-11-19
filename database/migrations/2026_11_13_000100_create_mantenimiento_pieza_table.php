<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Piezas que se cambiaron o se agregaron en un mantenimiento (RAM, SSD, teclado, pantalla...).
     * Cada pieza lleva su fotografía obligatoria, guardada en la base como las demás imágenes del sistema.
     */
    public function up(): void
    {
        if (Schema::hasTable('mantenimiento_pieza')) {
            return;
        }

        Schema::create('mantenimiento_pieza', function (Blueprint $table) {
            $table->increments('id_mantenimiento_pieza');
            $table->integer('mantenimiento_id_mantenimiento');
            $table->string('tipo_pieza', 40);
            $table->string('accion', 10);                 // CAMBIO | AGREGADO
            $table->string('descripcion', 255)->nullable();
            $table->string('imagen_mime', 50);

            $table->index('mantenimiento_id_mantenimiento', 'idx_pieza_mantenimiento');
            $table->foreign('mantenimiento_id_mantenimiento', 'fk_pieza_mantenimiento')
                ->references('id_mantenimiento')->on('mantenimiento')
                ->cascadeOnDelete();
        });

        // Laravel crea "blob" (64 KB); las fotos necesitan LONGBLOB, igual que las del mantenimiento
        DB::statement('ALTER TABLE mantenimiento_pieza ADD COLUMN imagen LONGBLOB NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_pieza');
    }
};
