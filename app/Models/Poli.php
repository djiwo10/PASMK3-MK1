<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    use HasFactory;

    protected $table = 'polis'; // Sesuaikan dengan nama tabel di database
    protected $guarded = [];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }
}
