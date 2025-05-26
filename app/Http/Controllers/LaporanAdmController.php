<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\LaporanAdmController;
use App\Models\Administrasi;
use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\User;

class LaporanAdmController extends Controller
{
    public function index(Request $request)
{
    // Jika request menuntut JSON (Postman dengan header Accept: application/json)
    if ($request->wantsJson()) {
        if ($request->has('tanggal_awal') && $request->has('tanggal_akhir')) {
            $adm = Administrasi::whereBetween('tanggal', [
                $request->tanggal_awal, 
                $request->tanggal_akhir
            ])->get();

            return response()->json([
                'message' => 'Laporan administrasi berhasil diambil!',
                'data'    => $adm
            ], 200);
        }

        return response()->json([
            'message' => 'Silakan masukkan parameter tanggal_awal dan tanggal_akhir!'
        ], 400);
    }

    // Jika request datang dari browser (HTML)
    if ($request->has('tanggal_awal') && $request->has('tanggal_akhir')) {
        $adm = Administrasi::whereBetween('tanggal', [
            $request->tanggal_awal, 
            $request->tanggal_akhir
        ])->get();
        return view('laporanadm_index', ['adm' => $adm]);
    }

    return view('laporanadm_form');
}

}
