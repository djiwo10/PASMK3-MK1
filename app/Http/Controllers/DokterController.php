<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dokter;
use App\Models\User;

class DokterController extends Controller
{
    /**
     * Menampilkan daftar dokter.
     */
    public function index()
    {
        $dokter = Dokter::all();
        return response()->json($dokter);
    }

    /**
     * Menampilkan form tambah dokter.
     */
    public function create()
    {
        return view('dokter_create');
    }

    /**
     * Menyimpan data dokter baru.
     */
    public function store(Request $request)
    {
        $validasiData = $request->validate([
            'nama_dokter' => 'required',
            'spesialis' => 'required',
            'nomor_hp' => 'required|numeric|unique:dokters,nomor_hp',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:8048',
            'twitter' => 'required',
            'facebook' => 'required',
            'instagram' => 'required',
            'tiktok' => 'required',
            'jadwal' => 'required|string'
        ]);

        // Generate kode dokter otomatis
        $lastDokter = Dokter::orderBy('id', 'desc')->first();
        $kode = $lastDokter ? 'D' . sprintf('%04d', $lastDokter->id + 1) : 'D0001';

        DB::beginTransaction();
        try {
            // Simpan data dokter sebagai user
            $user = new User();
            $user->name = $validasiData['nama_dokter'];
            $user->email = $validasiData['nomor_hp'] . '@dokter.com';
            $user->password = bcrypt($request->password);
            $user->role = 'dokter';
            $user->save();

            // Simpan data dokter
            $dokter = new Dokter();
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('public/foto_dokter');
                $dokter->foto = $path;
            }

            $dokter->user_id = $user->id;
            $dokter->kode_dokter = $kode;
            $dokter->nama_dokter = $request->nama_dokter;
            $dokter->spesialis = $request->spesialis;
            $dokter->nomor_hp = $request->nomor_hp;
            $dokter->twitter = $request->twitter;
            $dokter->facebook = $request->facebook;
            $dokter->instagram = $request->instagram;
            $dokter->tiktok = $request->tiktok;
            $dokter->jadwal = $request->jadwal;
            $dokter->save();

            DB::commit();

            return response()->json([
                'message' => 'Data dokter berhasil disimpan',
                'dokter' => $dokter
            ], 201);

        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail dokter.
     */
    public function show(string $id)
    {
        $dokter = Dokter::find($id);

        if (!$dokter) {
            return response()->json([
                'message' => 'Dokter tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data dokter berhasil ditemukan',
            'dokter' => $dokter
        ], 200);
    }

    /**
     * Menampilkan form edit dokter.
     */
    public function edit(string $id)
    {
        $data['dokter'] = Dokter::findOrFail($id);
        $data['list_sp'] = [
            'Umum' => 'Umum',
            'Gigi' => 'Gigi',
            'Kandungan' => 'Kandungan',
            'Anak' => 'Anak',
            'Bedah' => 'Bedah',
        ];
        return view('dokter_edit', $data);
    }

    /**
     * Memperbarui data dokter.
     */
    public function update(Request $request, string $id)
    {
        $validasiData = $request->validate([
            'nama_dokter' => 'required',
            'spesialis' => 'required',
            'nomor_hp' => 'required|numeric|unique:dokters,nomor_hp,' . $id,
            'twitter' => 'required',
            'facebook' => 'required',
            'instagram' => 'required',
            'tiktok' => 'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:8048',
            'jadwal' => 'required'
        ]);

        $dokter = Dokter::findOrFail($id);
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/foto_dokter');
            $dokter->foto = $path;
        }
        
        $dokter->update($validasiData);

        return redirect('/dokter')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Menghapus data dokter.
     */
    public function destroy(string $id)
    {
        $dokter = Dokter::findOrFail($id);

        // Cek apakah dokter memiliki data administrasi yang terkait
        if ($dokter->administrasi()->exists()) {
            return back()->with('error', 'Data tidak bisa dihapus karena sudah digunakan');
        }

        $dokter->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }

    /**
     * Menampilkan laporan dokter.
     */
    public function laporan()
    {
        // Belum ada isi, bisa ditambahkan nanti
    }
}
