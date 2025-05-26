<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Administrasi;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Poli;

class RegistrasiPasienController extends Controller
{
    /**
     * Menampilkan halaman registrasi pasien.
     */
    public function create()
    {
        return view('registrasipasien_create', [
            'list_jk' => ['Pria' => 'Pria', 'Wanita' => 'Wanita'],
            'dokter' => Dokter::all(),
            'poli' => Poli::all(),
        ]);
    }

    /**
     * Menyimpan data pasien dan administrasi baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pasien' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Pria,Wanita',
            'status' => 'required|string|max:50',
            'alamat' => 'required|string',
            'nomor_hp' => 'nullable|string|max:15',
            'keluhan' => 'nullable|string',
            'tanggal' => 'required|date',
            'poli_id' => 'required|exists:polis,id' // Pastikan poli_id ada di tabel polis
        ]);

        DB::beginTransaction();
        try {
            // Generate kode pasien baru
            $lastPasien = Pasien::latest('id')->first();
            $kodePasien = 'P' . sprintf('%04d', ($lastPasien->id ?? 0) + 1);

            // Simpan data pasien
            $pasien = Pasien::create([
                'kode_pasien' => $kodePasien,
                'nama_pasien' => $validated['nama_pasien'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'status' => $validated['status'],
                'alamat' => $validated['alamat'],
                'nomor_hp' => $validated['nomor_hp'] ?? null,
            ]);

            // Ambil data poli
            $poli = Poli::find($validated['poli_id']);

            // Cek apakah poli memiliki dokter
            if (!$poli || !$poli->dokter_id) {
                return response()->json([
                    'message' => 'Gagal: Poli ini belum memiliki dokter',
                    'poli_id' => $validated['poli_id']
                ], 400);
            }

            // Generate kode administrasi baru
            $lastAdm = Administrasi::latest('id')->first();
            $kodeAdm = 'ADM' . sprintf('%04d', ($lastAdm->id ?? 0) + 1);

            // Simpan data administrasi
            $adm = Administrasi::create([
                'kode_administrasi' => $kodeAdm,
                'poli' => $poli->nama,
                'pasien_id' => $pasien->id,
                'tanggal' => $validated['tanggal'],
                'keluhan' => $validated['keluhan'] ?? 'Tidak ada keluhan', 
                'dokter_id' => $poli->dokter_id,
                'biaya' => $poli->biaya,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Registrasi berhasil',
                'tanggal_pemeriksaan' => $validated['tanggal'],
                'pasien' => $pasien,
                'administrasi' => $adm,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal registrasi pasien: ' . $e->getMessage());

            return response()->json([
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
