<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use App\Models\Dosen;
use App\Models\PengajuanKP;
use App\Models\KomentarBimbingan;
use App\Models\DokumenKP;

class DosenDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $dosen = $user->dosen;

        // Pending applications targeted to this lecturer
        $pendingCount = PengajuanKP::where('dosen_id', $dosen->id)
            ->where('status_pengajuan', 'menunggu_konfirmasi_dosen')
            ->count();

        // Active guidance students count
        $activeCount = PengajuanKP::where('dosen_id', $dosen->id)
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

        $pendingPengajuans = PengajuanKP::with('mahasiswa')
            ->where('dosen_id', $dosen->id)
            ->where('status_pengajuan', 'menunggu_konfirmasi_dosen')
            ->latest()
            ->limit(5)
            ->get();

        $bimbingans = PengajuanKP::with('mahasiswa')
            ->where('dosen_id', $dosen->id)
            ->whereIn('status_pengajuan', [
                'diterima_admin',
                'revisi',
                'kp_berlangsung',
                'laporan_diupload'
            ])
            ->latest()
            ->limit(5)
            ->get();

        // Documents needing review from active guidance
        $laporans = DokumenKP::whereHas('pengajuan', function ($query) use ($dosen) {
            $query->where('dosen_id', $dosen->id)
                ->whereIn('status_pengajuan', ['kp_berlangsung', 'laporan_diupload', 'revisi']);
        })
        ->where('status', 'sudah_upload')
        ->with('pengajuan.mahasiswa')
        ->latest()
        ->limit(5)
        ->get();

        return view('livewire.dosen.dosen-dashboard', [
            'dosen' => $dosen,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
            'sisaQuota' => $dosen->sisa_slot,
            'pendingPengajuans' => $pendingPengajuans,
            'bimbingans' => $bimbingans,
            'laporans' => $laporans,
        ])->layout('layouts.app');
    }
}
