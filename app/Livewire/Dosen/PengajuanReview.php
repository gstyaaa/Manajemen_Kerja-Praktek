<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use App\Models\PengajuanKP;

class PengajuanReview extends Component
{
    public $isOpen = false;
    public $selectedPengajuanId;
    public $catatan_dosen;
    public $actionType; // 'setujui' or 'tolak'

    public function openReviewModal($id, $type)
    {
        $dosen = auth()->user()->dosen;
        $pengajuan = PengajuanKP::findOrFail($id);

        if ($pengajuan->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized action.');
        }

        $this->selectedPengajuanId = $id;
        $this->actionType = $type;
        $this->catatan_dosen = '';
        $this->isOpen = true;
    }

    public function closeReviewModal()
    {
        $this->isOpen = false;
        $this->selectedPengajuanId = null;
        $this->catatan_dosen = '';
    }

    public function process()
    {
        $dosen = auth()->user()->dosen;
        $pengajuan = PengajuanKP::findOrFail($this->selectedPengajuanId);

        if ($pengajuan->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($this->actionType === 'tolak') {
            $this->validate([
                'catatan_dosen' => 'required|string|min:5',
            ], [
                'catatan_dosen.required' => 'Catatan wajib diisi jika Anda menolak pengajuan.',
            ]);

            $pengajuan->update([
                'status_pengajuan' => 'ditolak_dosen',
                'status_persetujuan_dosen' => 'ditolak',
                'catatan_dosen' => $this->catatan_dosen,
                'tanggal_konfirmasi_dosen' => now(),
            ]);

            $pengajuan->addProgress('ditolak_dosen', 'Pengajuan ditolak oleh dosen pembimbing dengan catatan: ' . $this->catatan_dosen);
            session()->flash('message', 'Pengajuan berhasil ditolak.');
        } else {
            $pengajuan->update([
                'status_pengajuan' => 'disetujui_dosen',
                'status_persetujuan_dosen' => 'disetujui',
                'catatan_dosen' => $this->catatan_dosen,
                'tanggal_konfirmasi_dosen' => now(),
            ]);

            $pengajuan->addProgress('disetujui_dosen', 'Pengajuan disetujui oleh dosen pembimbing. Menunggu verifikasi admin.');
            session()->flash('message', 'Pengajuan berhasil disetujui.');
        }

        $this->closeReviewModal();
    }

    public function render()
    {
        $dosen = auth()->user()->dosen;

        $pengajuans = PengajuanKP::with('mahasiswa')
            ->where('dosen_id', $dosen->id)
            ->where('status_pengajuan', 'menunggu_konfirmasi_dosen')
            ->latest()
            ->get();

        return view('livewire.dosen.pengajuan-review', [
            'pengajuans' => $pengajuans,
        ])->layout('layouts.app');
    }
}
