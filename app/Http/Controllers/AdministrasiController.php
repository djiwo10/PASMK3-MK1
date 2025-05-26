<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrasi;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Poli;

class AdministrasiController extends Controller
{
    /**
     * Menampilkan daftar administrasi
     */
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role == 'dokter') {
            $administrasi = Administrasi::with(['pasien', 'dokter', 'poli'])
                ->where('dokter_id', auth()->user()->dokter->id)
                ->orderBy('tanggal', 'desc')
                ->paginate(50);
        } else {
            $administrasi = Administrasi::with(['pasien', 'dokter', 'poli'])
                ->orderBy('tanggal', 'desc')
                ->paginate(50);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Data administrasi berhasil diambil',
                'data'    => $administrasi
            ], 200);
        }

        return view('administrasi_index', [
            'administrasi' => $administrasi,
            'judul' => 'Data Administrasi'
        ]);
    }

    public function store(Request $request)
    {
        $validasiData = $request->validate([
            'pasien_id' => 'required',
            'poli_id' => 'required',
            'tanggal' => 'required|date',
            'keluhan' => 'required',
            'diagnosis' => 'nullable|string'
        ]);

        $poli = Poli::findOrFail($request->poli_id);
        $kodeAdm = Administrasi::orderBy('id', 'desc')->first();
        $kode = 'ADM0001';
        if ($kodeAdm) {
            $kode = 'ADM' . sprintf('%04d', $kodeAdm->id + 1);
        }

        $adm = new Administrasi();
        $adm->kode_administrasi = $kode;
        $adm->poli = $poli->nama;
        $adm->pasien_id = $request->pasien_id;
        $adm->tanggal = $request->tanggal;
        $adm->keluhan = strip_tags($request->keluhan);
        $adm->dokter_id = $poli->dokter_id;
        $adm->biaya = $poli->biaya;
        $adm->diagnosis = $request->diagnosis ?? "Belum didiagnosis";
        $adm->status = "baru";
        $adm->save();

        // Redirect jika datang dari UI, bukan API
        if (!$request->wantsJson()) {
            return redirect()->route('administrasi.index')->with('success', 'Data administrasi berhasil disimpan.');
        }

        return response()->json([
            'message' => 'Data administrasi berhasil disimpan',
            'data'    => $adm
        ], 201);
    }

    /**
     * Menampilkan detail administrasi berdasarkan ID
     */
    public function show($id)
    {
        $administrasi = Administrasi::with(['pasien', 'dokter', 'poli'])->find($id);

        if (!$administrasi) {
            return response()->json([
                'success' => false,
                'message' => "Administrasi dengan ID $id tidak ditemukan"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail administrasi ditemukan',
            'data' => $administrasi
        ], 200);
    }

    /**
     * Mengupdate diagnosis di administrasi
     */
    public function update(Request $request, $id)
    {
        $validasiData = $request->validate([
            'diagnosis' => 'required|string'
        ]);

        $administrasi = Administrasi::findOrFail($id);
        $administrasi->diagnosis = strip_tags($request->diagnosis);
        $administrasi->status = 'selesai';
        $administrasi->save();

        return response()->json([
            'message' => 'Diagnosis berhasil diperbarui',
            'data'    => $administrasi
        ], 200);
    }
}
