<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\stok_harian;
use App\Models\StockBathces;
use App\Models\produk;
use App\Models\toko;
use Illuminate\Support\Facades\Auth;

class StockOut extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::orderBy('sku')->get();
        $toko   = Toko::orderBy('name')->get();

        // Filter OUT
        $stok = stok_harian::where('type', 'OUT')
            ->when($request->id_produk, fn($q) => $q->where('id_produk', $request->id_produk))
            ->when($request->id_toko, fn($q) => $q->where('id_toko', $request->id_toko))
            ->when($request->date, fn($q) => $q->whereDate('transaction_date', $request->date))
            ->with('produk', 'toko', 'user')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('modul.transaction.outt.index', compact('stok', 'produk', 'toko'));
    }

    /* ----------------------------------------------------------
     *  STORE (PROCESS FIFO)
     * ----------------------------------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required',
            'id_toko'   => 'required',
            'quantity'  => 'required|integer|min:1',
            'transaction_date' => 'required|date'
        ]);

        $qty_out = $request->quantity;

        // Ambil semua batch yang masih ada stoknya
        $batches = StockBathces::where('id_produk', $request->id_produk)
            ->where('id_toko', $request->id_toko)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_masuk', 'asc') // FIFO
            ->get();

        if ($batches->sum('qty_sisa') < $qty_out) {
            return back()->with('error', 'Not enough stock for FIFO OUT.');
        }

        // 1. Simpan transaksi OUT ke stok_harian
        $out = stok_harian::create([
            'id_produk' => $request->id_produk,
            'id_toko'   => $request->id_toko,
            'id_user'   => Auth::id(),
            'type'      => 'OUT',
            'quantity'  => $qty_out,
            'note'      => $request->note,
            'transaction_date' => $request->transaction_date,
        ]);

        // 2. Loop FIFO
        $sisa = $qty_out;

        foreach ($batches as $batch) {
            if ($sisa <= 0) break;

            if ($batch->qty_sisa >= $sisa) {
                // Batch cukup → potong lalu selesai
                $batch->qty_sisa -= $sisa;
                $batch->save();
                $sisa = 0;
            } else {
                // Batch tidak cukup → habiskan batch dan lanjut
                $sisa -= $batch->qty_sisa;
                $batch->qty_sisa = 0;
                $batch->save();
            }
        }

        return back()->with('success', 'Stock OUT processed with FIFO!');
    }


    /* ----------------------------------------------------------
     *  UPDATE (OPTIONAL) — but FIFO recompute needed!
     * ----------------------------------------------------------*/
    public function update(Request $request, $id)
    {
        return back()->with('error', 'Editing FIFO OUT is not supported (risk of mismatch).');
    }

    /* ----------------------------------------------------------
     *  DELETE (OPTIONAL) — but requires rollback FIFO
     * ----------------------------------------------------------*/
    public function destroy($id)
    {
        return back()->with('error', 'Deleting OUT is disabled to protect FIFO integrity.');
    }
}
