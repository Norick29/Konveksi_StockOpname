<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stok_harian;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\StockBathces;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockAdjust extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::orderBy('sku')->get();
        $toko = Toko::orderBy('name')->get();

        $stok = stok_harian::where('type', 'ADJUST')
            ->when($request->id_produk, fn($q) => $q->where('id_produk', $request->id_produk))
            ->when($request->id_toko, fn($q) => $q->where('id_toko', $request->id_toko))
            ->when($request->date, fn($q) => $q->whereDate('transaction_date', $request->date))
            ->with(['produk', 'toko'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('modul.transaction.adjust.index', compact('stok', 'produk', 'toko'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_toko' => 'required|exists:tokos,id_toko',
            'adjust_type' => 'required|in:IN,OUT',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        // Simpan log adjust ke stok_harian
        $log = stok_harian::create([
            'id_produk' => $request->id_produk,
            'id_toko'   => $request->id_toko,
            'id_user'   => Auth::id(),
            'type'      => 'ADJUST',
            'adjust_type' => $request->adjust_type, 
            'quantity'  => $request->quantity,
            'note'      => $request->note,
            'transaction_date' => $request->transaction_date
        ]);

        /** --------------------------
         *  FIFO PROCESSING
         * --------------------------*/

        // CASE 1 — ADJUST (+) → MENAMBAH STOK
        if ($request->adjust_type === 'IN') {
            StockBathces::create([
                'id_produk' => $request->id_produk,
                'id_toko' => $request->id_toko,
                'sumber' => 'adjust',
                'id_sumber' => $log->id_stok_harian,
                'qty_awal' => $request->quantity,
                'qty_sisa' => $request->quantity,
                'tanggal_masuk' => $request->transaction_date
            ]);
        }

        // CASE 2 — ADJUST (-) → MENGURANGI STOK
        if ($request->adjust_type === 'OUT') {
            $remaining = $request->quantity;

            // Ambil batch yang masih punya stok (FIFO)
            $batches = StockBathces::where('id_produk', $request->id_produk)
                ->where('id_toko', $request->id_toko)
                ->where('qty_sisa', '>', 0)
                ->orderBy('tanggal_masuk', 'asc')
                ->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) break;

                $ambil = min($batch->qty_sisa, $remaining);

                $batch->qty_sisa -= $ambil;
                $batch->save();

                $remaining -= $ambil;
            }

            if ($remaining > 0) {
                return back()->with('error', 'Stock insufficient for FIFO adjust OUT!');
            }
        }

        return back()->with('success', 'Stock Adjust processed successfully (FIFO Applied)!');
    }

    public function update(Request $request, $id)
    {
        return back()->with('error', 'Adjustment cannot be edited because it affects FIFO history.');
    }

    public function destroy($id)
    {
        return back()->with('error', 'Adjustment cannot be deleted because it affects FIFO history.');
    }
}
