<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'mahasiswa_id',
    'dosen_id',
    'judul_kp',
    'nama_instansi',
    'alamat_instansi',
    'pembimbing_lapangan',
    'kontak_pembimbing_lapangan',
    'tanggal_mulai',
    'tanggal_selesai',
    'deskripsi_pekerjaan',
    'status_pengajuan',
    'status_persetujuan_dosen',
    'catatan_dosen',
    'tanggal_konfirmasi_dosen'
])]
class PengajuanKP extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_kps';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function dokumens()
    {
        return $this->hasMany(DokumenKP::class, 'pengajuan_kp_id');
    }

    public function komentars()
    {
        return $this->hasMany(KomentarBimbingan::class, 'pengajuan_kp_id');
    }

    public function progres()
    {
        return $this->hasMany(ProgresKP::class, 'pengajuan_kp_id');
    }

    /**
     * Record a milestone in the progress tracking table.
     */
    public function addProgress(string $tahap, string $deskripsi): ProgresKP
    {
        return $this->progres()->create([
            'tahap' => $tahap,
            'deskripsi' => $deskripsi,
            'tanggal' => now(),
        ]);
    }
}
