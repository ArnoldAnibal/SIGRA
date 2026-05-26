<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('factura_electronica', function (Blueprint $table) {

            // New nullable field for transaction references
            $table->string('detalle_pago', 150)
                ->nullable()
                ->after('metodo_pago');
        });

        // Remove QR from ENUM
        DB::statement("
            ALTER TABLE factura_electronica
            MODIFY metodo_pago ENUM(
                'Efectivo',
                'Tarjeta',
                'Transferencia',
                'Credito'
            ) NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore QR option
        DB::statement("
            ALTER TABLE factura_electronica
            MODIFY metodo_pago ENUM(
                'Efectivo',
                'Tarjeta',
                'Transferencia',
                'QR',
                'Credito'
            ) NULL
        ");

        Schema::table('factura_electronica', function (Blueprint $table) {

            $table->dropColumn('detalle_pago');
        });
    }
};