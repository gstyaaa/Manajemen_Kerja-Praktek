<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['pengajuan_kp_id', 'tahap', 'deskripsi', 'tanggal'])]
class ProgresKP extends Model
{
    use HasFactory;

    protected $table = 'progres_kps';

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKP::class, 'pengajuan_kp_id');
    }
}
