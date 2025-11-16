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
        Schema::create('stok_harians', function (Blueprint $table) {
            $table->id('id_stok_harian');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_toko');
            $table->unsignedBigInteger('id_user');
            $table->enum('type', ['IN', 'OUT', 'ADJUST']);
            $table->integer('quantity');
            $table->string('note')->nullable();
            $table->date('transaction_date');
            $table->timestamps();

            $table->foreign('id_produk')->references('id_produk')->on('produks')->onDelete('cascade');
            $table->foreign('id_toko')->references('id_toko')->on('tokos')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_harians');
    }
};
