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
        Schema::create('stok_bathces', function (Blueprint $table) {
            $table->id('id_batch');

            // Relasi produk & toko
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_toko');

            // Berhubungan dengan proses sumber (opening/in)
            $table->string('sumber'); // 'opening' atau 'in'
            $table->unsignedBigInteger('id_sumber'); // id stok_bulan atau stok_harian

            // Data batch
            $table->integer('qty_awal');  // jumlah awal batch
            $table->integer('qty_sisa');  // sisa batch saat FIFO berjalan
            $table->date('tanggal_masuk'); // tanggal masuk batch

            $table->timestamps();

            // Foreign key (jika tabel ready)
            $table->foreign('id_produk')->references('id_produk')->on('produks')->onDelete('cascade');
            $table->foreign('id_toko')->references('id_toko')->on('tokos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_bathces');
    }
};
