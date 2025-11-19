<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = kategori::all();
        return view('modul.master.categories.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        kategori::create([
            'name' => $validated['name'],
        ]);

        return redirect()->back()->with('success', 'Store created successfully!');
    }

    public function update(Request $request, $id_kategori)
    {
        $kategori = kategori::findOrFail($id_kategori);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $kategori->name = $validated['name'];
        $kategori->save();

        return redirect()->back()->with('success', 'Store updated successfully!');
    }

    public function destroy($id_kategori)
    {
        $kategori = kategori::findOrFail($id_kategori);
        $kategori->delete();

        return redirect()->back()->with('success', 'Store deleted successfully!');
    }
}
