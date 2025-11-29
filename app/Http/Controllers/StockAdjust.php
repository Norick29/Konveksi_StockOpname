<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\stok_harian;
use App\Models\produk;
use App\Models\toko;
use Illuminate\Support\Facades\Auth;

class StockAdjust extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::orderBy('sku')->get();
        $toko = Toko::orderBy('name')->get();

        $stok = stok_harian::where('type', 'ADJUST')
            ->when($request->id_toko, fn($q) => $q->where('id_toko', $request->id_toko))
            ->when($request->id_produk, fn($q) => $q->where('id_produk', $request->id_produk))
            ->when($request->date, fn($q) => $q->where('transaction_date', $request->date))
            ->with(['produk', 'toko'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('modul.transaction.adjust.index', compact('stok', 'produk', 'toko'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required',
            'id_toko' => 'required',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        stok_harian::create([
            'id_produk' => $request->id_produk,
            'id_toko' => $request->id_toko,
            'id_user' => Auth::id(),
            'type' => 'ADJUST',
            'quantity' => $request->quantity,
            'note' => $request->note,
            'transaction_date' => $request->transaction_date
        ]);

        return back()->with('success', 'Stock Adjust created successfully!');
    }

    public function update(Request $request, $id_stok_harian)
    {
        $adjust = stok_harian::findOrFail($id_stok_harian);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string'
        ]);

        $adjust->update([
            'quantity' => $request->quantity,
            'transaction_date' => $request->transaction_date,
            'note' => $request->note
        ]);

        return back()->with('success', 'Stock Adjust updated successfully!');
    }

    public function destroy($id_stok_harian)
    {
        $adjust = stok_harian::findOrFail($id_stok_harian);
        $adjust->delete();

        return back()->with('success', 'Stock Adjust deleted successfully!');
    }
}
