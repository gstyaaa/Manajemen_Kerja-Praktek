<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['pengajuan_kp_id', 'jenis_dokumen', 'nama_file', 'path_file', 'status', 'catatan'])]
class DokumenKP extends Model
{
    use HasFactory;

    protected $table = 'dokumen_kps';

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKP::class, 'pengajuan_kp_id');
    }
}
