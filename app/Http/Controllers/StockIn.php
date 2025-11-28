<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\stok_harian;
use App\Models\produk;
use App\Models\toko;
use Illuminate\Support\Facades\Auth;

class StockIn extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::orderBy('sku')->get();
        $toko = Toko::orderBy('name')->get();

        // Filter
        $stok = stok_harian::where('type', 'IN')
            ->when($request->id_produk, fn($q) => $q->where('id_produk', $request->id_produk))
            ->when($request->id_toko, fn($q) => $q->where('id_toko', $request->id_toko))
            ->when($request->date, fn($q) => $q->whereDate('transaction_date', $request->date))
            ->with('produk', 'toko', 'user')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('modul.transaction.in.index', compact('stok', 'produk', 'toko'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required',
            'id_toko'   => 'required',
            'quantity'  => 'required|integer|min:1',
            'transaction_date' => 'required|date',
        ]);

        stok_harian::create([
            'id_produk' => $request->id_produk,
            'id_toko'   => $request->id_toko,
            'id_user'   => Auth::id(),
            'type'      => 'IN',
            'quantity'  => $request->quantity,
            'note'      => $request->note,
            'transaction_date' => $request->transaction_date,
        ]);

        return back()->with('success', 'Stock IN successfully recorded!');
    }

    public function update(Request $request, $id)
    {
        $stok = stok_harian::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
        ]);

        $stok->update([
            'quantity' => $request->quantity,
            'note' => $request->note,
            'transaction_date' => $request->transaction_date,
        ]);

        return back()->with('success', 'Stock IN updated!');
    }

    public function destroy($id)
    {
        $stok = stok_harian::findOrFail($id);
        $stok->delete();

        return back()->with('success', 'Stock IN deleted!');
    }
}
