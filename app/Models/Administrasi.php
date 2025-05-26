<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrasi extends Model
{
    use HasFactory;

    // Definisikan kolom yang bisa diisi
    protected $fillable = [
        'kode_administrasi',
        'tanggal',
        'pasien_id',
        'dokter_id',
        'poli_id',
        'biaya',
        'keluhan',
        'diagnosis',
        'status'
    ];

    // Nonaktifkan timestamps (karena tidak ada created_at & updated_at)
    public $timestamps = false;

    // Casting format tanggal
    protected $casts = [
        'tanggal' => 'date:d-m-Y'
    ];

    // Relasi ke tabel `pasiens`
    public function pasien()
    {
        return $this->belongsTo(Pasien::class)->withDefault([
            'nama_pasien' => 'Data sudah dihapus',
        ]);
    }

    // Relasi ke tabel `dokters`
    public function dokter()
    {
        return $this->belongsTo(Dokter::class)->withDefault([
            'nama_dokter' => 'Data sudah dihapus',
        ]);
    }

    // Relasi ke tabel `polis`
    public function poli()
    {
        return $this->belongsTo(Poli::class)->withDefault([
            'nama_poli' => 'Data sudah dihapus',
        ]);
    }

    // **Menjaga diagnosis agar tidak NULL**  
    public function setDiagnosisAttribute($value)
    {
        $this->attributes['diagnosis'] = $value ?? 'Belum ada diagnosis';
    }
}
