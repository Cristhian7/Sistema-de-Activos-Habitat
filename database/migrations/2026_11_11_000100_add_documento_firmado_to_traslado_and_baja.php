<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Resguardo del documento firmado (PDF o foto) de cada traslado y de cada baja.
     * El archivo se guarda en storage/app/private (no es público) y aquí queda su ruta,
     * el nombre original, cuándo se subió y quién lo subió.
     */
    private array $tablas = ['traslado', 'baja'];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (!Schema::hasColumn($tabla, 'documento_firmado')) {
                    $table->string('documento_firmado', 255)->nullable();
                    $table->string('documento_nombre', 255)->nullable();
                    $table->dateTime('documento_fecha')->nullable();
                    $table->integer('documento_cargado_por')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn(['documento_firmado', 'documento_nombre', 'documento_fecha', 'documento_cargado_por']);
            });
        }
    }
};
