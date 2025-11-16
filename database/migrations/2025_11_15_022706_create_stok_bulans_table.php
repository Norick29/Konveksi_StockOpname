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
        Schema::create('stok_bulans', function (Blueprint $table) {
            $table->id('id_stok_bulan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_toko');
            $table->string('month'); // 2025-11
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('id_produk')->references('id_produk')->on('produks')->onDelete('cascade');
            $table->foreign('id_toko')->references('id_toko')->on('tokos')->onDelete('cascade');

            $table->unique(['id_produk', 'id_toko', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_bulans');
    }
};
