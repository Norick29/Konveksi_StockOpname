<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stok_bulan;
use App\Models\stok_harian;
use App\Models\StockBathces;
use App\Models\produk;
use App\Models\toko;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MonthlyReport extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end   = Carbon::parse($month . '-01')->endOfMonth();

        $sizeOrder = ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4, 'XXL' => 5];

        $produk = Produk::with('kategori')
            ->orderBy('color')         // 2️⃣ warna
            ->orderBy('id_kategori')   // 1️⃣ kategori dulu
            
            ->orderByRaw("
                CASE size
                    WHEN 'S' THEN 1
                    WHEN 'M' THEN 2
                    WHEN 'L' THEN 3
                    WHEN 'XL' THEN 4
                    WHEN 'XXL' THEN 5
                    ELSE 99
                END
            ")
            ->orderBy('sku')            // fallback aman
            ->get();
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
                    'size'        => $p->size,
                    'opening'     => $opening,
                    'in'          => $in,
                    'adjust_in'   => $adjust_in,
                    'out'         => $out,
                    'adjust_out'  => $adjust_out,
                    'closing'     => $closing,
                ];
            }
        }

        $perPage = 15;
        $page = request()->get('page', 1);

        $collection = collect($report);

        $paginatedReport = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(), // 🔥 biar filter tetap kebawa
            ]
        );

        return view('modul.report.monthly.index', [
            'report' => $paginatedReport,
            'month'  => $month,
            'toko'   => $toko,
        ]);
    }

public function generateMonthlyReport(Request $request)
{
    $month = $request->month ?? now()->format('Y-m');
    $start = Carbon::parse($month . '-01')->startOfMonth();
    $end   = Carbon::parse($month . '-01')->endOfMonth();

    $produk = Produk::with('kategori')->orderBy('sku')->get();

    $toko = $request->id_toko
        ? Toko::where('id_toko', $request->id_toko)->get()
        : Toko::orderBy('name')->get();

    $tokoName = $request->id_toko
        ? strtoupper($toko->first()->name)
        : 'ALL STORE';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    /* ================= HEADER ================= */
    $sheet->mergeCells('A1:H1');
    $sheet->setCellValue('A1', 'STOCK');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('A2:H2');
    $sheet->setCellValue('A2', Carbon::parse($month)->format('F Y'));
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('A3:H3');
    $sheet->setCellValue('A3', 'TOKO : ' . $tokoName);
    $sheet->getStyle('A3')->getFont()->setBold(true);
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    /* ================= TABLE HEADER ================= */
    $row = 5;
    $sheet->fromArray([
        'Warna',
        'Size',
        'Stock Awal',
        'In',
        'Adjust In',
        'Out',
        'Adjust Out',
        'Stock Akhir'
    ], null, "A$row");

    $sheet->getStyle("A$row:H$row")->getFont()->setBold(true);
    $sheet->getStyle("A$row:H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // warna header
    $sheet->getStyle("C$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF2CC');
    $sheet->getStyle("D$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
    $sheet->getStyle("E$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
    $sheet->getStyle("F$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FCE4D6');
    $sheet->getStyle("G$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8CBAD');
    $sheet->getStyle("H$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BDD7EE');

    $row++;

    /* ================= SIZE ORDER ================= */
    $sizeOrder = ['S'=>1,'M'=>2,'L'=>3,'XL'=>4,'XXL'=>5];

    /* ================= GROUP DATA ================= */
    $data = [];

    foreach ($toko as $tk) {
        foreach ($produk as $p) {

            $opening = stok_bulan::where('id_produk', $p->id_produk)
                ->where('id_toko', $tk->id_toko)
                ->where('month', $month)
                ->value('quantity') ?? 0;

            $in = stok_harian::where('type', 'IN')
                ->where('id_produk', $p->id_produk)
                ->where('id_toko', $tk->id_toko)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('quantity');

            $adjustIn = stok_harian::where('type', 'ADJUST')
                ->where('adjust_type', 'IN')
                ->where('id_produk', $p->id_produk)
                ->where('id_toko', $tk->id_toko)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('quantity');

            $out = stok_harian::where('type', 'OUT')
                ->where('id_produk', $p->id_produk)
                ->where('id_toko', $tk->id_toko)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('quantity');

            $adjustOut = stok_harian::where('type', 'ADJUST')
                ->where('adjust_type', 'OUT')
                ->where('id_produk', $p->id_produk)
                ->where('id_toko', $tk->id_toko)
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('quantity');

            $closing = StockBathces::where('id_produk', $p->id_produk)
                ->where('id_toko', $tk->id_toko)
                ->where('tanggal_masuk', '<=', $end)
                ->sum('qty_sisa');

            if ($opening == 0 && $in == 0 && $out == 0 && $closing == 0) continue;

            $label = strtoupper($p->color . ' ' . $p->kategori->name);

            $data[$label][] = [
                'size'       => $p->size,
                'awal'       => $opening,
                'in'         => $in,
                'adjust_in'  => $adjustIn,
                'out'        => $out,
                'adjust_out' => $adjustOut,
                'akhir'      => $closing
            ];
        }
    }

    /* ================= PRINT ================= */
    $grandTotal = 0;

    foreach ($data as $label => $items) {

        usort($items, fn($a,$b) =>
            ($sizeOrder[$a['size']] ?? 99) <=> ($sizeOrder[$b['size']] ?? 99)
        );

        $startRow = $row;
        $subtotal = 0;

        foreach ($items as $item) {
            $sheet->fromArray([
                null,
                $item['size'],
                $item['awal'],
                $item['in'],
                $item['adjust_in'],
                $item['out'],
                $item['adjust_out'],
                $item['akhir']
            ], null, "A$row");

            $subtotal += $item['akhir'];
            $row++;
        }

        // merge label warna
        $sheet->mergeCells("A$startRow:A".($row - 1));
        $sheet->setCellValue("A$startRow", $label);
        $sheet->getStyle("A$startRow")->getFont()->setBold(true);
        $sheet->getStyle("A$startRow")->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        // total per warna
        $sheet->fromArray(['TOTAL','','','','','','',$subtotal], null, "A$row");
        $sheet->getStyle("A$row:H$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row:H$row")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFD966');

        $grandTotal += $subtotal;
        $row++;
    }

    // GRAND TOTAL
    $row++;
    $sheet->fromArray(['GRAND TOTAL','','','','','','',$grandTotal], null, "A$row");
    $sheet->getStyle("A$row:H$row")->getFont()->setBold(true);
    $sheet->getStyle("A$row:H$row")->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('FFC000');

    foreach (range('A','H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    return new StreamedResponse(function () use ($spreadsheet) {
        (new Xlsx($spreadsheet))->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="Monthly_Stock_Report.xlsx"',
    ]);
}


}
