<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;

class ProductController extends Controller
{
    public function index()
    {
        $produk = Produk::query()
        ->when(request('kategori'), function ($q) {
            $q->where('id_kategori', request('kategori'));
        })
        ->when(request('color'), function ($q) {
            $q->where('color', request('color'));
        })
        ->when(request('size'), function ($q) {
            $q->where('size', request('size'));
        })
        ->orderBy('id_produk')
        ->get();

        return view('modul.master.product.index', [
            'produk'   => $produk,
            'kategori' => Kategori::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'color' => 'required|string',
            'size' => 'required|string',
            'sku' => 'nullable|string'
        ]);

        Produk::create($request->all());

        return back()->with('success', 'Product created successfully!');
    }

    public function update(Request $request, $id_produk)
    {
        $produk = Produk::findOrFail($id_produk);

        $request->validate([
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'color' => 'required|string',
            'size' => 'required|string',
            'sku' => 'nullable|string'
        ]);

        $produk->update($request->all());

        return back()->with('success', 'Product updated successfully!');
    }

    public function destroy($id_produk)
    {
        Produk::findOrFail($id_produk)->delete();

        return back()->with('success', 'Product deleted successfully!');
    }
}
