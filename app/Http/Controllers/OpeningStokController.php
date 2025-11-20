<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\stok_bulan;

class OpeningStokController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        $produk = Produk::orderBy('sku')->get();
        $toko   = Toko::all();

        // ambil stock bulanan untuk tabel
        $stok = stok_bulan::with(['produk', 'toko'])
                ->where('month', $month)
                ->when($request->id_toko, function($q) use($request){
                    $q->where('id_toko', $request->id_toko);
                })
                ->get();

        return view('modul.transaction.opening.index', compact('produk', 'toko', 'stok'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_toko'   => 'required|exists:tokos,id_toko',
            'month'     => 'required',
            'quantity'  => 'required|integer|min:0'
        ]);

        stok_bulan::updateOrCreate(
            [
                'id_produk' => $request->id_produk,
                'id_toko'   => $request->id_toko,
                'month'     => $request->month,
            ],
            [
                'quantity' => $request->quantity,
            ]
        );

        return back()->with('success', 'Opening stock saved successfully!');
    }

    public function update(Request $request, $id)
    {
        $stok = stok_bulan::findOrFail($id);

        $request->validate([
            'id_produk' => 'required|exists:produks,id_produk',
            'id_toko'   => 'required|exists:tokos,id_toko',
            'month'     => 'required|date_format:Y-m',
            'quantity'  => 'required|integer|min:0',
        ]);

        // Check duplicate combination except itself
        $exists = stok_bulan::where('id_produk', $request->id_produk)
                    ->where('id_toko', $request->id_toko)
                    ->where('month', $request->month)
                    ->where('id_stok_bulan', '!=', $id)
                    ->first();

        if ($exists) {
            return back()->with('error', 'Opening stock for this month already exists.');
        }

        $stok->update([
            'id_produk' => $request->id_produk,
            'id_toko' => $request->id_toko,
            'month' => $request->month,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Opening stock updated successfully!');
    }

    /**
     * Remove entry.
     */
    public function destroy($id)
    {
        $stok = stok_bulan::findOrFail($id);
        $stok->delete();

        return back()->with('success', 'Opening stock deleted successfully!');
    }
}
