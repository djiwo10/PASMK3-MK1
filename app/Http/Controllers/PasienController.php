<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrasi;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\User;
use App\Models\Obat;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cari = $request->query('q');

        if ($cari) {
            $pasien = Pasien::where('nama_pasien', 'like', '%' . $cari . '%')
                ->orWhere('kode_pasien', 'like', '%' . $cari . '%')
                ->paginate(10);
        } else {
            $pasien = Pasien::latest()->paginate(10);
        }

        // Jika request dari Postman atau API
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pasien berhasil diambil',
                'data' => $pasien
            ], 200, [], JSON_PRETTY_PRINT);
        }

        return view('pasien_index', compact('pasien'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['judul'] = 'Tambah Data';
        $data['dokters'] = Dokter::all(); // Ambil daftar dokter untuk dropdown
        return view('pasien_create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validasiData = $request->validate([
            'nama_pasien' => 'required',
            'jenis_kelamin' => 'required',
            'status' => 'required',
            'nomor_hp' => 'required',
            'alamat' => 'required',
            'keluhan' => 'nullable', // Bisa dikosongkan, nanti diisi default
            'dokter_id' => 'required|exists:dokters,id', // Harus ada dokter
        ]);

        // Set default untuk keluhan jika kosong
        if (!$request->filled('keluhan')) {
            $validasiData['keluhan'] = 'Tidak ada keluhan';
        }

        // Generate kode pasien otomatis
        $kodeQuery = Pasien::orderBy('id', 'desc')->first();
        $kode = 'P0001';
        if ($kodeQuery) {
            $kode = 'P' . sprintf('%04d', $kodeQuery->id + 1);
        }

        $pasien = new Pasien();
        $pasien->kode_pasien = $kode;
        $pasien->fill($validasiData);
        $pasien->save();

        flash('Data berhasil disimpan');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['pasien'] = Pasien::findOrFail($id);
        $data['judul'] = 'Edit Data';
        $data['dokters'] = Dokter::all(); // Ambil semua dokter untuk dropdown
        return view('pasien_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validasiData = $request->validate([
            'nama_pasien' => 'required',
            'jenis_kelamin' => 'required',
            'status' => 'required',
            'nomor_hp' => 'required',
            'alamat' => 'required',
            'keluhan' => 'nullable',
            'dokter_id' => 'required|exists:dokters,id',
        ]);

        if (!$request->filled('keluhan')) {
            $validasiData['keluhan'] = 'Tidak ada keluhan';
        }

        $pasien = Pasien::findOrFail($id);
        $pasien->fill($validasiData);
        $pasien->save();

        flash('Data berhasil diubah');
        return redirect('pasien');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pasien = Pasien::withCount('administrasi')->findOrFail($id);
        
        if ($pasien->administrasi_count > 0) {
            flash('Data tidak bisa dihapus karena sudah digunakan')->error();
            return back();
        }

        $pasien->delete();
        flash('Data berhasil dihapus');
        return back();
    }

    /**
     * Report generation (optional).
     */
    public function laporan()
    {
        return response()->json([
            'success' => true,
            'message' => 'Laporan fitur belum tersedia'
        ], 200);
    }
}
