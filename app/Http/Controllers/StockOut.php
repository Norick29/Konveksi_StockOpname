<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\stok_harian;
use App\Models\StockBathces;
use App\Models\produk;
use App\Models\toko;
use App\Models\ekspedisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockOut extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::orderBy('sku')->get();
        $toko   = Toko::orderBy('name')->get();

        $date = $request->date ?? Carbon::today()->toDateString();

        $stok = stok_harian::where('type', 'OUT')
            ->whereDate('transaction_date', $date)
            ->when($request->id_produk, fn($q) =>
                $q->where('id_produk', $request->id_produk)
            )
            ->when($request->id_toko, fn($q) =>
                $q->where('id_toko', $request->id_toko)
            )
            ->with(['produk', 'toko', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(10); // 🔥 WAJIB

        return view('modul.transaction.outt.index', compact(
            'stok', 'produk', 'toko', 'date'
        ));
    }

    /* ----------------------------------------------------------
     *  STORE (PROCESS FIFO)
     * ----------------------------------------------------------*/
    public function store(Request $request)
{
    $request->validate([
        'id_toko'                   => 'required|exists:tokos,id_toko',
        'transaction_date'          => 'required|date',
        'items'                     => 'required|array|min:1',
        'items.*.id_produk'         => 'required|exists:produks,id_produk',
        'items.*.quantity'          => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {

        foreach ($request->items as $item) {

            $idProduk = $item['id_produk'];
            $qtyOut   = $item['quantity'];

            /* ======================
             * FIFO CHECK PER PRODUK
             * ======================*/
            $batches = StockBathces::where('id_produk', $idProduk)
                ->where('id_toko', $request->id_toko)
                ->where('qty_sisa', '>', 0)
                ->orderBy('tanggal_masuk')
                ->get();

            if ($batches->sum('qty_sisa') < $qtyOut) {
                throw new \Exception('Stok tidak cukup untuk salah satu produk');
            }

            /* ======================
             * SIMPAN STOCK OUT
             * ======================*/
            stok_harian::create([
                'id_produk'        => $idProduk,
                'id_toko'          => $request->id_toko,
                'id_user'          => Auth::id(),
                'type'             => 'OUT',
                'quantity'         => $qtyOut,
                'note'             => $request->note,
                'transaction_date' => $request->transaction_date,
            ]);

            /* ======================
             * FIFO POTONG BATCH
             * ======================*/
            $sisa = $qtyOut;

            foreach ($batches as $batch) {
                if ($sisa <= 0) break;

                $ambil = min($batch->qty_sisa, $sisa);
                $batch->qty_sisa -= $ambil;
                $batch->save();

                $sisa -= $ambil;
            }
        }

        DB::commit();
        return back()->with('success', 'Stock OUT berhasil disimpan');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

}