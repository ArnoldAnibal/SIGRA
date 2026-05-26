<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('factura_electronica', function (Blueprint $table) {

            // IMPORTANT:
            // Use signed integer because cliente.id_cliente is signed

            $table->integer('id_cliente_deudor')
                ->nullable()
                ->after('estado_pago');

            $table->foreign('id_cliente_deudor')
                ->references('id_cliente')
                ->on('cliente')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_electronica', function (Blueprint $table) {

            $table->dropForeign(['id_cliente_deudor']);

            $table->dropColumn('id_cliente_deudor');
        });
    }
};