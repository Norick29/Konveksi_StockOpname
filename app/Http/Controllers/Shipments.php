<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ekspedisi;
use App\Models\toko;
use Illuminate\Support\Facades\Auth;


class Shipments extends Controller
{
    public function index(Request $request)
    {
        $toko = Toko::orderBy('name')->get();

        $query = ekspedisi::with(['toko', 'user'])->orderBy('date', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('id_toko')) {
            $query->where('id_toko', $request->id_toko);
        }

        $shipments = $query->get();

        return view('modul.note.shipments.index', compact('shipments', 'toko'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_toko' => 'required',
            'date'    => 'required|date',
            'courier'   => 'required|string',
            'quantity'     => 'required|integer|min:1',
        ]);

        ekspedisi::create([
            'id_toko' => $validated['id_toko'],
            'id_user' => Auth::id(),
            'date'    => $validated['date'],
            'courier'   => $validated['courier'],
            'quantity'     => $validated['quantity'],
        ]);

        return back()->with('success', 'Shipment created successfully!');
    }

    public function update(Request $request, $id_shipments)
    {
        $shipment = ekspedisi::findOrFail($id_shipments);

        $validated = $request->validate([
            'id_toko' => 'required',
            'date'    => 'required|date',
            'courier'   => 'required|string',
            'quantity'     => 'required|integer|min:1',
        ]);

        $shipment->update($validated);

        return back()->with('success', 'Shipment updated successfully!');
    }

    public function destroy($id_shipments)
    {
        ekspedisi::findOrFail($id_shipments)->delete();

        return back()->with('success', 'Shipment deleted successfully!');
    }
}
