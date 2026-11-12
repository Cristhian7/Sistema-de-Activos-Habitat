<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Meses de uso del equipo (0 a 11) que acompañan a los años completos de anos_uso.
     */
    public function up(): void
    {
        Schema::table('baja', function (Blueprint $table) {
            if (!Schema::hasColumn('baja', 'mes_uso')) {
                $table->integer('mes_uso')->nullable()->after('anos_uso');
            }
        });
    }

    public function down(): void
    {
        Schema::table('baja', function (Blueprint $table) {
            $table->dropColumn('mes_uso');
        });
    }
};
