<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockBathces;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class MonitoringStock extends Controller
{
    public function index(Request $request)
    {
        $thresholdCritical = 5;
        $thresholdWarning  = 10;

        /* =====================
         * SUMMARY
         * =====================*/
        $totalStock = StockBathces::sum('qty_sisa');
        $totalProduk = Produk::count();

        $tokoBermasalah = StockBathces::select('id_toko')
            ->groupBy('id_toko')
            ->havingRaw('SUM(qty_sisa) <= ?', [$thresholdWarning])
            ->count();

        /* =====================
         * LOW STOCK
         * =====================*/
        $lowStock = StockBathces::select(
                'id_produk',
                'id_toko',
                DB::raw('SUM(qty_sisa) as total_sisa')
            )
            ->groupBy('id_produk', 'id_toko')
            ->having('total_sisa', '<=', $thresholdWarning)
            ->with('produk', 'toko')
            ->orderBy('total_sisa')
            ->get();

        /* =====================
         * FILTER TOKO
         * =====================*/
        $stokPerToko = StockBathces::select(
                'id_produk',
                DB::raw('SUM(qty_sisa) as total_sisa')
            )
            ->when($request->id_toko, fn($q) =>
                $q->where('id_toko', $request->id_toko)
            )
            ->groupBy('id_produk')
            ->with('produk')
            ->get();

        return view('modul.owner.monitoring.index', compact(
            'totalStock',
            'totalProduk',
            'tokoBermasalah',
            'lowStock',
            'stokPerToko'
        ));
    }
}
