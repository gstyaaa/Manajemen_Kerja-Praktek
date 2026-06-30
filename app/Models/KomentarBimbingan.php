<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['pengajuan_kp_id', 'dosen_id', 'isi_komentar', 'status_komentar'])]
class KomentarBimbingan extends Model
{
    use HasFactory;

    protected $table = 'komentar_bimbingans';

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKP::class, 'pengajuan_kp_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}
