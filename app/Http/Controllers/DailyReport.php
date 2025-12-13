<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stok_harian;
use App\Models\ekspedisi;
use App\Models\Toko;
use App\Models\produk;

class DailyReport extends Controller
{
    public function index(Request $request)
    {
        $toko = Toko::all();

        return view('modul.report.daily.index', compact('toko'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'id_toko' => 'required',
            'press_admin' => 'nullable|string',
            'resi_admin' => 'nullable|string',
            'packing_admin' => 'nullable|string',
            'buang_admin' => 'nullable|string',
        ]);

        $date = $request->date;

        // Fetch data Laporan Buang Benang (Stock-IN)
        $in = stok_harian::with(['produk', 'toko'])
            ->where('type', 'IN')
            ->whereDate('transaction_date', $date)
            ->where('id_toko', $request->id_toko)
            ->get();

        // Fetch data Daily Report E-Commerce (Stock-OUT)
        $out = stok_harian::with(['produk', 'toko'])
            ->where('type', 'OUT')
            ->whereDate('transaction_date', $date)
            ->where('id_toko', $request->id_toko)
            ->get();

        // Shipments
        $shipments = ekspedisi::whereDate('date', $date)
            ->where('id_toko', $request->id_toko)
            ->get();

        return view('modul.report.daily.result', [
            'date' => $date,
            'in' => $in,
            'out' => $out,
            'shipments' => $shipments,
            'press' => $request->press_admin,
            'resi' => $request->resi_admin,
            'packing' => $request->packing_admin,
            'buang' => $request->buang_admin,
        ]);
    }
}
