<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::query();

        $from = $request->input('from');
        $to = $request->input('to');

        if ($from) $query->whereDate('tanggal_penjualan', '>=', $from);
        if ($to) $query->whereDate('tanggal_penjualan', '<=', $to);

        $penjualan = $query->orderBy('tanggal_penjualan')->get();

        $total = $penjualan->sum(function($s){ return $s->jumlah * $s->harga; });

        $chartData = $penjualan->groupBy(function($item){
            return Carbon::parse($item->tanggal_penjualan)->format('Y-m-d');
        })->map(function($items){
            return $items->sum(function($s){ return $s->jumlah * $s->harga; });
        });

        return view('penjualan.index', [
            'penjualan' => $penjualan,
            'total' => $total,
            'chartLabels' => $chartData->keys()->toArray(),
            'chartValues' => $chartData->values()->toArray(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('trim', array_shift($rows));

        $count = 0;
        foreach ($rows as $row) {
            if (count($row) < 4) continue;
            $row = array_map('trim', $row);
            $data = array_combine($header, $row);

            if (empty($data['nama_produk']) || empty($data['tanggal_penjualan'])) continue;

            Penjualan::create([
                'nama_produk' => $data['nama_produk'],
                'tanggal_penjualan' => Carbon::parse($data['tanggal_penjualan'])->format('Y-m-d'),
                'jumlah' => (int) $data['jumlah'],
                'harga' => (float) $data['harga'],
            ]);
            $count++;
        }

        return redirect()->route('penjualan.index')->with('success', "$count data berhasil diimport.");
    }
}
