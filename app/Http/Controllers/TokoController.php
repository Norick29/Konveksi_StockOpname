<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\toko;

class TokoController extends Controller
{
    public function index()
    {
        $toko = toko::all();
        return view('modul.master.toko.index', compact('toko'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        toko::create([
            'name' => $validated['name'],
        ]);

        return redirect()->back()->with('success', 'Store created successfully!');
    }

    public function update(Request $request, $id_toko)
    {
        $toko = toko::findOrFail($id_toko);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $toko->name = $validated['name'];
        $toko->save();

        return redirect()->back()->with('success', 'Store updated successfully!');
    }

    public function destroy($id_toko)
    {
        $toko = toko::findOrFail($id_toko);
        $toko->delete();

        return redirect()->back()->with('success', 'Store deleted successfully!');
    }
}
