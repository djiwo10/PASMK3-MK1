<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poli;
use App\Models\Dokter;

class PoliController extends Controller
{
    /**
     * Tampilkan semua data poli
     */
    public function index()
    {
        $poli = Poli::with('dokter')->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data poli berhasil diambil',
            'data' => $poli
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Simpan data poli baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validasiData = $request->validate([
            'nama' => 'required|unique:polis,nama',
            'dokter_id' => 'required|exists:dokters,id',
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'required'
        ]);

        // Simpan data
        $poli = Poli::create($validasiData);

        return response()->json([
            'success' => true,
            'message' => 'Data poli berhasil disimpan',
            'data' => $poli
        ], 201);
    }

    /**
     * Tampilkan detail poli berdasarkan ID
     */
    public function show($id)
    {
        $poli = Poli::with('dokter')->find($id);

        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => "Poli dengan ID $id tidak ditemukan"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail poli ditemukan',
            'data' => $poli
        ], 200);
    }

    /**
     * Update data poli berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        $poli = Poli::find($id);

        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => "Poli dengan ID $id tidak ditemukan"
            ], 404);
        }

        // Validasi input
        $validasiData = $request->validate([
            'nama' => 'required|unique:polis,nama,' . $id,
            'dokter_id' => 'required|exists:dokters,id',
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'required'
        ]);

        // Update data
        $poli->update($validasiData);

        return response()->json([
            'success' => true,
            'message' => 'Data poli berhasil diperbarui',
            'data' => $poli
        ], 200);
    }

    /**
     * Hapus data poli berdasarkan ID
     */
    public function destroy($id)
    {
        $poli = Poli::find($id);

        if (!$poli) {
            return response()->json([
                'success' => false,
                'message' => "Poli dengan ID $id tidak ditemukan"
            ], 404);
        }

        $poli->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data poli berhasil dihapus'
        ], 200);
    }
}
