<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\stok_harian;
use App\Models\produk;
use App\Models\toko;
use Illuminate\Support\Facades\Auth;
use App\Models\StockBathces;
use Carbon\Carbon;

class StockIn extends Controller
{
    public function index(Request $request)
{
    $produk = Produk::orderBy('sku')->get();
    $toko   = Toko::orderBy('name')->get();

    $date = $request->date ?? Carbon::today()->toDateString();

    $stok = stok_harian::where('type', 'IN')
        ->whereDate('transaction_date', $date)
        ->when($request->id_produk, fn($q) =>
            $q->where('id_produk', $request->id_produk)
        )
        ->when($request->id_toko, fn($q) =>
            $q->where('id_toko', $request->id_toko)
        )
        ->with(['produk', 'toko', 'user'])
        ->orderBy('transaction_date', 'desc')
        ->paginate(10);

    return view('modul.transaction.in.index', compact(
        'stok', 'produk', 'toko', 'date'
    ));
}
    

    /* ----------------------------------------------------------
     *  STORE (CREATE IN + CREATE BATCH FIFO)
     * ----------------------------------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'id_toko' => 'required',
            'transaction_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        foreach ($request->items as $item) {

            // 1. Simpan ke stok_harian
            $in = stok_harian::create([
                'id_produk' => $item['id_produk'],
                'id_toko'   => $request->id_toko,
                'id_user'   => Auth::id(),
                'type'      => 'IN',
                'quantity'  => $item['quantity'],
                'note'      => $request->note,
                'transaction_date' => $request->transaction_date,
            ]);

            // 2. Buat batch baru (FIFO)
            StockBathces::create([
                'id_produk'     => $item['id_produk'],
                'id_toko'       => $request->id_toko,
                'sumber'        => 'in',
                'id_sumber'     => $in->id_stok_harian,
                'qty_awal'      => $item['quantity'],
                'qty_sisa'      => $item['quantity'],
                'tanggal_masuk' => $request->transaction_date,
            ]);
        }

        return back()->with('success', 'Stock IN successfully recorded!');
    }


    /* ----------------------------------------------------------
     *  UPDATE (UPDATE HISTORI + UPDATE BATCH)
     * ----------------------------------------------------------*/
    public function update(Request $request, $id)
    {
        $stok = stok_harian::findOrFail($id);

        $request->validate([
            'quantity'          => 'required|integer|min:1',
            'transaction_date'  => 'required|date',
        ]);

        // 1. Update histori IN
        $stok->update([
            'quantity' => $request->quantity,
            'note' => $request->note,
            'transaction_date' => $request->transaction_date,
        ]);

        // 2. Update batch-nya
        $batch = StockBathces::where('sumber', 'in')
                    ->where('id_sumber', $stok->id_stok_harian)
                    ->first();

        if ($batch) {
            $batch->update([
                'qty_awal'      => $request->quantity,
                'qty_sisa'      => $request->quantity,  // reset agar FIFO tetap valid
                'tanggal_masuk' => $request->transaction_date,
            ]);
        }

        return back()->with('success', 'Stock IN updated!');
    }

    /* ----------------------------------------------------------
     *  DELETE (DELETE HISTORI + DELETE BATCH)
     * ----------------------------------------------------------*/
    public function destroy($id)
    {
        $stok = stok_harian::findOrFail($id);

        // 1. Hapus batch FIFO
        StockBathces::where('sumber', 'in')
            ->where('id_sumber', $stok->id_stok_harian)
            ->delete();

        // 2. Hapus histori IN
        $stok->delete();

        return back()->with('success', 'Stock IN deleted!');
    }
}
