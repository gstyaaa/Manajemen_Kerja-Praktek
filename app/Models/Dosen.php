<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'nama', 'nidn', 'email', 'no_hp', 'bidang_keahlian', 'status', 'kuota_bimbingan'])]
class Dosen extends Model
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

    public function komentarBimbingans()
    {
        return $this->hasMany(KomentarBimbingan::class);
    }

    /**
     * Calculate remaining slots.
     * sisa slot = kuota_bimbingan - jumlah pengajuan aktif
     */
    public function getSisaSlotAttribute(): int
    {
        $activeCount = $this->pengajuans()
            ->whereIn('status_pengajuan', [
                'diajukan',
                'menunggu_konfirmasi_dosen',
                'disetujui_dosen',
                'diterima_admin',
                'revisi',
                'kp_berlangsung',
                'laporan_diupload'
            ])
            ->count();

        return max(0, $this->kuota_bimbingan - $activeCount);
    }

    /**
     * Get availability badge state: penuh, hampir_penuh, tersedia
     */
    public function getKuotaStatusAttribute(): string
    {
        $sisa = $this->sisa_slot;

        if ($sisa === 0) {
            return 'penuh';
        }

        if ($sisa <= 1) {
            return 'hampir_penuh';
        }

        return 'tersedia';
    }
}
