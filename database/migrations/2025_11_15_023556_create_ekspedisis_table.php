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
        Schema::create('ekspedisis', function (Blueprint $table) {
            $table->id('id_ekspedisi');
            $table->unsignedBigInteger('id_toko');
            $table->unsignedBigInteger('id_user');
            $table->date('date');
            $table->string('courier');      // contoh: SPX, JNT, JNE, Shopee Express
            $table->integer('quantity');    // total pcs dikirim via kurir ini
            $table->timestamps();

            $table->foreign('id_toko')->references('id_toko')->on('tokos')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekspedisis');
    }
};
