<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\stok_bulan;
use App\Models\StockBathces;

class OpeningStokController extends Controller
{
    public function index(Request $request)
{
    $month = $request->month ?? now()->format('Y-m');

    $produk = Produk::orderBy('sku')->get();
    $toko   = Toko::all();

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
        'quantity'  => 'required|integer|min:0',
    ]);

    // 1. Simpan / update stok bulanan
    $stok = stok_bulan::updateOrCreate(
        [
            'id_produk' => $request->id_produk,
            'id_toko'   => $request->id_toko,
            'month'     => $request->month,
        ],
        [
            'quantity' => $request->quantity,
        ]
    );

    // 2. Hapus batch lama untuk opening ini
    StockBathces::where('sumber', 'opening')
        ->where('id_sumber', $stok->id_stok_bulan)
        ->delete();

    // 3. Buat batch baru
    if ($request->quantity > 0) {
        StockBathces::create([
            'id_produk'     => $request->id_produk,
            'id_toko'       => $request->id_toko,
            'sumber'        => 'opening',
            'id_sumber'     => $stok->id_stok_bulan,
            'qty_awal'      => $request->quantity,
            'qty_sisa'      => $request->quantity,
            'tanggal_masuk' => $request->month . '-01',
        ]);
    }

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

    // Cek duplikasi
    $exists = stok_bulan::where('id_produk', $request->id_produk)
                ->where('id_toko', $request->id_toko)
                ->where('month', $request->month)
                ->where('id_stok_bulan', '!=', $id)
                ->first();

    if ($exists) {
        return back()->with('error', 'Opening stock for this month already exists.');
    }

    // Update stok bulan
    $stok->update([
        'id_produk' => $request->id_produk,
        'id_toko'   => $request->id_toko,
        'month'     => $request->month,
        'quantity'  => $request->quantity,
    ]);

    // Reset batch opening lama
    StockBathces::where('sumber', 'opening')
        ->where('id_sumber', $stok->id_stok_bulan)
        ->delete();

    // Buat batch opening baru
    if ($request->quantity > 0) {
        StockBathces::create([
            'id_produk'     => $request->id_produk,
            'id_toko'       => $request->id_toko,
            'sumber'        => 'opening',
            'id_sumber'     => $stok->id_stok_bulan,
            'qty_awal'      => $request->quantity,
            'qty_sisa'      => $request->quantity,
            'tanggal_masuk' => $request->month . '-01',
        ]);
    }

    return back()->with('success', 'Opening stock updated successfully!');
}

public function destroy($id)
{
    $stok = stok_bulan::findOrFail($id);

    // Hapus batch opening terkait
    StockBathces::where('sumber', 'opening')
        ->where('id_sumber', $stok->id_stok_bulan)
        ->delete();

    $stok->delete();

    return back()->with('success', 'Opening stock deleted successfully!');
}
}
