<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StockBathces;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {

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

        $view->with([
            'lowStockAlerts' => $lowStock,
            'lowStockCount'  => $lowStock->count()
        ]);
    });
    }
}
