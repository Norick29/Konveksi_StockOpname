<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stok_bulan;
use App\Models\stok_harian;
use App\Models\StockBathces;
use App\Models\produk;
use App\Models\toko;
use Carbon\Carbon;

class MonthlyReport extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end   = Carbon::parse($month . '-01')->endOfMonth();

        $produk = Produk::orderBy('sku')->get();
        $toko   = Toko::orderBy('name')->get();

        $report = [];

        // Jika user pilih toko → filter
        $toko_list = $request->id_toko
            ? Toko::where('id_toko', $request->id_toko)->get()
            : $toko;

        foreach ($toko_list as $tk) {

            foreach ($produk as $p) {

                // 1. OPENING
                $opening = stok_bulan::where('id_produk', $p->id_produk)
                            ->where('id_toko', $tk->id_toko)
                            ->where('month', $month)
                            ->value('quantity') ?? 0;

                // 2. TOTAL IN
                $in = stok_harian::where('type', 'IN')
                        ->where('id_produk', $p->id_produk)
                        ->where('id_toko', $tk->id_toko)
                        ->whereBetween('transaction_date', [$start, $end])
                        ->sum('quantity');

                // 3. ADJUST IN
                $adjust_in = stok_harian::where('type', 'ADJUST')
                                ->where('adjust_type', 'IN')
                                ->where('id_produk', $p->id_produk)
                                ->where('id_toko', $tk->id_toko)
                                ->whereBetween('transaction_date', [$start, $end])
                                ->sum('quantity');

                // 4. ADJUST OUT
                $adjust_out = stok_harian::where('type', 'ADJUST')
                                ->where('adjust_type', 'OUT')
                                ->where('id_produk', $p->id_produk)
                                ->where('id_toko', $tk->id_toko)
                                ->whereBetween('transaction_date', [$start, $end])
                                ->sum('quantity');

                // 5. CLOSING FIFO (real dari batches)
                $closing = StockBathces::where('id_produk', $p->id_produk)
                            ->where('id_toko', $tk->id_toko)
                            ->where('tanggal_masuk', '<=', $end)
                            ->sum('qty_sisa');

                // 6. OUT FIFO (Real, bukan rumus!)
                // rumus: OUT = opening + in + adjust_in - closing - adjust_out
                $out = ($opening + $in + $adjust_in) - ($closing + $adjust_out);
                if ($out < 0) $out = 0;

                // 7. SKIP jika bulan sebelum bisnis dimulai
                // Jika tidak ada opening dan tidak ada batch older → kosongkan
                $has_activity = (
                    $opening != 0 ||
                    $in != 0 ||
                    $out != 0 ||
                    $closing != 0 ||
                    $adjust_in != 0 ||
                    $adjust_out != 0
                );

                if (!$has_activity) {
                    continue; // bulan sebelum bisnis mulai → kosong
                }

                // Masukkan ke report
                $report[] = [
                    'toko'        => $tk->name,
                    'produk'      => $p->sku,
                    'opening'     => $opening,
                    'in'          => $in,
                    'adjust_in'   => $adjust_in,
                    'out'         => $out,
                    'adjust_out'  => $adjust_out,
                    'closing'     => $closing,
                ];
            }
        }

        return view('modul.report.monthly.index', [
            'report' => $report,
            'month'  => $month,
            'toko'   => $toko,
        ]);
    }


}
