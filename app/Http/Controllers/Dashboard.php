<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\stok_harian;
use App\Models\StockBathces;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /* ======================
         * KPI CARDS
         * ======================*/
        $totalProduk = Produk::count();
        $totalToko   = Toko::count();

        $stockInToday = stok_harian::where('type', 'IN')
            ->whereDate('transaction_date', $today)
            ->sum('quantity');

        $stockOutToday = stok_harian::where('type', 'OUT')
            ->whereDate('transaction_date', $today)
            ->sum('quantity');

        $totalStock = StockBathces::sum('qty_sisa');

        /* ======================
         * GRAFIK 30 HARI (IN vs OUT)
         * ======================*/
        $start = Carbon::now()->subDays(29);

        $chartData = stok_harian::select(
                DB::raw('DATE(transaction_date) as date'),
                'type',
                DB::raw('SUM(quantity) as total')
            )
            ->whereBetween('transaction_date', [$start, $today])
            ->whereIn('type', ['IN', 'OUT'])
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        /* ======================
         * TOP 5 PRODUK TERLARIS (OUT)
         * ======================*/
        $topProduk = stok_harian::select(
                'id_produk',
                DB::raw('SUM(quantity) as total_out')
            )
            ->where('type', 'OUT')
            ->whereMonth('transaction_date', now()->month)
            ->groupBy('id_produk')
            ->orderByDesc('total_out')
            ->with('produk')
            ->limit(5)
            ->get();

        /* ======================
         * STOK MENIPIS (≤ 10)
         * ======================*/
        $lowStock = StockBathces::select(
                'id_produk',
                'id_toko',
                DB::raw('SUM(qty_sisa) as total_sisa')
            )
            ->groupBy('id_produk', 'id_toko')
            ->having('total_sisa', '<=', 50)
            ->with('produk', 'toko')
            ->orderBy('total_sisa')
            ->get();

        /* ======================
         * AKTIVITAS TERAKHIR
         * ======================*/
        $lastActivity = stok_harian::with('produk', 'toko', 'user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('home', compact(
            'totalProduk',
            'totalToko',
            'stockInToday',
            'stockOutToday',
            'totalStock',
            'chartData',
            'topProduk',
            'lowStock',
            'lastActivity'
        ));
    }
}
