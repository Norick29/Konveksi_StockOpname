<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stok_harian;
use App\Models\ekspedisi;
use App\Models\Toko;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DailyReport extends Controller
{
    public function index(Request $request)
    {
        $toko = Toko::all();

        return view('modul.report.daily.index', compact('toko'));
    }

//     public function generate(Request $request)
//     {
//         $request->validate([
//             'date' => 'required|date',
//             'id_toko' => 'required|array|min:1',
//             'id_toko.*' => 'exists:tokos,id_toko',

//             'shipments' => 'nullable|array',
//             'shipments.*.expedition' => 'required_with:shipments.*.qty|string',
//             'shipments.*.qty' => 'required_with:shipments.*.expedition|integer|min:1',

//             'press_admin' => 'nullable|string',
//             'resi_admin' => 'nullable|string',
//             'packing_admin' => 'nullable|string',
//             'buang_admin' => 'nullable|string',
//         ]);

//         $date = $request->date;
//         $storeIds = $request->id_toko;

//         /*
//         |------------------------------------------------------------------
//         | SIMPAN DATA SHIPMENTS
//         |------------------------------------------------------------------
//         */
//         if ($request->filled('shipments')) {
//     foreach ($storeIds as $id_toko) {

//         // 🔥 HAPUS SHIPMENT LAMA DI TANGGAL & TOKO INI
//         ekspedisi::where('id_toko', $id_toko)
//             ->whereDate('date', $date)
//             ->delete();

//         // 🔥 SIMPAN SHIPMENT BARU
//         foreach ($request->shipments as $shipment) {
//             ekspedisi::create([
//                 'id_toko'   => $id_toko,
//                 'courier'   => $shipment['expedition'],
//                 'quantity'  => $shipment['qty'],
//                 'date'      => $date,
//                 'id_user'   => Auth::id(), // WAJIB (biar tidak error lagi)
//             ]);
//         }
//     }
// }

//         /*
//         |------------------------------------------------------------------
//         | GENERATE LAPORAN (IN & OUT)
//         |------------------------------------------------------------------
//         */
//         $reports = [];

//         foreach ($storeIds as $id_toko) {

//             $store = Toko::find($id_toko);
//             if (!$store) continue;

//             // STOCK OUT (E-COMMERCE)
//             $out = stok_harian::with('produk')
//                 ->where('type', 'OUT')
//                 ->where('id_toko', $id_toko)
//                 ->whereDate('transaction_date', $date)
//                 ->get();

//             // STOCK IN (BUANG BENANG)
//             $in = stok_harian::with('produk')
//                 ->where('type', 'IN')
//                 ->where('id_toko', $id_toko)
//                 ->whereDate('transaction_date', $date)
//                 ->get();

//             // SHIPMENTS
//             $shipments = ekspedisi::where('id_toko', $id_toko)
//                 ->whereDate('date', $date)
//                 ->select('courier', DB::raw('SUM(quantity) as total'))
//                 ->groupBy('courier')
//                 ->get();

//             $reports[] = [
//                 'store'     => $store,
//                 'out'       => $out,
//                 'in'        => $in,
//                 'shipments' => $shipments,
//                 'total_out' => $out->sum('quantity'),
//             ];
//         }

//         /*
//         |------------------------------------------------------------------
//         | SIMPAN SESSION UNTUK PDF
//         |------------------------------------------------------------------
//         */
//         session([
//             'daily-report' => [
//                 'date'     => $date,
//                 'reports'  => $reports,
//                 'press'    => $request->press_admin,
//                 'resi'     => $request->resi_admin,
//                 'packing'  => $request->packing_admin,
//                 'buang'    => $request->buang_admin,
//             ]
//         ]);

//         return view('modul.report.daily.result', [
//             'date'     => $date,
//             'reports'  => $reports,
//             'press'    => $request->press_admin,
//             'resi'     => $request->resi_admin,
//             'packing'  => $request->packing_admin,
//             'buang'    => $request->buang_admin,
//         ]);
//     }

public function generate(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'id_toko' => 'required|array|min:1',
        'id_toko.*' => 'exists:tokos,id_toko',

        'shipments' => 'nullable|array',
        'shipments.*.expedition' => 'required_with:shipments.*.qty|string',
        'shipments.*.qty' => 'required_with:shipments.*.expedition|integer|min:1',

        'press_admin' => 'nullable|string|max:50',
        'resi_admin' => 'nullable|string|max:50',
        'packing_admin' => 'nullable|string|max:50',
        'buang_admin' => 'nullable|string|max:50',
    ]);

    $date     = $request->date;
    $storeIds = $request->id_toko;

    /*
    |------------------------------------------------------------------
    | SIMPAN SHIPMENTS + ADMIN (PER TOKO & TANGGAL)
    |------------------------------------------------------------------
    */
    foreach ($storeIds as $id_toko) {

        if ($request->filled('shipments')) {

            ekspedisi::where('id_toko', $id_toko)
                ->whereDate('date', $date)
                ->delete();

            foreach ($request->shipments as $shipment) {
                ekspedisi::create([
                    'id_toko'       => $id_toko,
                    'courier'       => $shipment['expedition'],
                    'quantity'      => $shipment['qty'],
                    'date'          => $date,
                    'id_user'       => Auth::id(),

                    'press_admin'   => $request->press_admin,
                    'resi_admin'    => $request->resi_admin,
                    'packing_admin' => $request->packing_admin,
                    'buang_admin'   => $request->buang_admin,
                ]);
            }
        }
    }

    /*
    |------------------------------------------------------------------
    | GENERATE LAPORAN
    |------------------------------------------------------------------
    */
    $reports = [];

    foreach ($storeIds as $id_toko) {

        $store = Toko::find($id_toko);
        if (!$store) continue;

        // OUT
        $out = stok_harian::with('produk')
            ->where('type', 'OUT')
            ->where('id_toko', $id_toko)
            ->whereDate('transaction_date', $date)
            ->get();

        // IN
        $in = stok_harian::with('produk')
            ->where('type', 'IN')
            ->where('id_toko', $id_toko)
            ->whereDate('transaction_date', $date)
            ->get();

        // SHIPMENTS
        $shipments = ekspedisi::where('id_toko', $id_toko)
            ->whereDate('date', $date)
            ->select(
                'courier',
                DB::raw('SUM(quantity) as total')
            )
            ->groupBy('courier')
            ->get();

        // 🔥 AMBIL ADMIN DARI DB (BUAT OWNER)
        $admin = ekspedisi::where('id_toko', $id_toko)
            ->whereDate('date', $date)
            ->first();

        $reports[] = [
            'store'     => $store,
            'out'       => $out,
            'in'        => $in,
            'shipments' => $shipments,
            'total_out' => $out->sum('quantity'),

            'press'     => $admin->press_admin   ?? '-',
            'resi'      => $admin->resi_admin    ?? '-',
            'packing'   => $admin->packing_admin ?? '-',
            'buang'     => $admin->buang_admin   ?? '-',
        ];
    }

    session([
        'daily-report' => [
            'date'    => $date,
            'reports' => $reports,
        ]
    ]);

    return view('modul.report.daily.result', [
        'date'    => $date,
        'reports' => $reports,
    ]);
}


    public function outSummary(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'id_toko' => 'required|array|min:1',
        'id_toko.*' => 'exists:tokos,id_toko'
    ]);

    $result = [];

    foreach ($request->id_toko as $id_toko) {
        $toko = Toko::find($id_toko);

        $out = stok_harian::with('produk.kategori')
            ->where('type', 'OUT')
            ->where('id_toko', $id_toko)
            ->whereDate('transaction_date', $request->date)
            ->get();

        $result[] = [
            'toko' => $toko->name,
            'total_out' => $out->sum('quantity'),
            'by_category' => $out->groupBy(fn($o) => $o->produk->kategori->name)
                ->map(fn($items) => $items->sum('quantity'))
        ];
    }

    return response()->json($result);
}

    public function exportPdf()
    {
        $data = session('daily-report');

        if (!$data) {
            return redirect()
                ->route('daily-report.index')
                ->with('error', 'Daily report session not found.');
        }

        $pdf = Pdf::loadView('modul.report.daily.pdf', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download(
            'daily-report-' . $data['date'] . '.pdf'
        );
    }

    
}
