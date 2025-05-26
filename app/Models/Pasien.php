<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasiens'; // Pastikan sesuai dengan nama tabel di database

    protected $fillable = ['kode_pasien', 'nama_pasien', 'jenis_kelamin', 'nomor_hp', 'status', 'alamat', 'keluhan', 'dokter_id'];


    /**
     * Relasi ke tabel administrasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function administrasi(): HasMany
    {
        return $this->hasMany(Administrasi::class, 'pasien_id', 'id');
    }
}
