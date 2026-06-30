<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'nim', 'nama', 'email', 'program_studi', 'angkatan', 'no_hp', 'status_aktif'])]
class Mahasiswa extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuans()
    {
        return $this->hasMany(PengajuanKP::class);
    }

    /**
     * Check if student has an active (non-final, slot-consuming) application.
     */
    public function hasActivePengajuan(): bool
    {
        return $this->pengajuans()
            ->whereIn('status_pengajuan', [
                'diajukan',
                'menunggu_konfirmasi_dosen',
                'disetujui_dosen',
                'diterima_admin',
                'revisi',
                'kp_berlangsung',
                'laporan_diupload'
            ])
            ->exists();
    }

    /**
     * Get the current active application.
     */
    public function activePengajuan()
    {
        return $this->pengajuans()
            ->whereIn('status_pengajuan', [
                'diajukan',
                'menunggu_konfirmasi_dosen',
                'disetujui_dosen',
                'diterima_admin',
                'revisi',
                'kp_berlangsung',
                'laporan_diupload'
            ])
            ->first();
    }
}
