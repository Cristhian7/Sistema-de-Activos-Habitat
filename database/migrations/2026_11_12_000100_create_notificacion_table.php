<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notificaciones para quien revisa los documentos firmados de traslados y bajas.
     * Hay una fila por destinatario: cada uno marca la suya como revisada.
     */
    public function up(): void
    {
        if (Schema::hasTable('notificacion')) {
            return;
        }

        Schema::create('notificacion', function (Blueprint $table) {
            $table->increments('id_notificacion');
            $table->integer('empleado_id_empleado');          // destinatario
            $table->string('tipo', 20);                       // traslado | baja
            $table->integer('registro_id');                   // id del traslado o de la baja
            $table->string('mensaje', 255);
            $table->integer('creado_por')->nullable();        // empleado que subió el documento
            $table->dateTime('creado_en');
            $table->dateTime('revisado_en')->nullable();      // null = pendiente de revisar

            $table->index(['empleado_id_empleado', 'revisado_en']);
            $table->index(['tipo', 'registro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};
