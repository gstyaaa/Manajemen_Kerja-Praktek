<?php

namespace App\Livewire\Dosen;

use Livewire\Component;
use App\Models\PengajuanKP;
use App\Models\KomentarBimbingan;
use App\Models\DokumenKP;
use Illuminate\Support\Facades\Storage;

class BimbinganManager extends Component
{
    public $selectedPengajuanId;
    public $isi_komentar;
    public $status_komentar = 'catatan_umum';

    // Modal fields for document review
    public $isOpenDocModal = false;
    public $selectedDocId;
    public $docStatusAction; // 'diterima' or 'perlu_revisi'
    public $docCatatan;

    protected $listeners = ['commentAdded' => '$refresh'];

    public function selectStudent($id)
    {
        $dosen = auth()->user()->dosen;
        $pengajuan = PengajuanKP::findOrFail($id);

        if ($pengajuan->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized.');
        }

        $this->selectedPengajuanId = $id;
        $this->resetValidation();
        $this->isi_komentar = '';
        $this->status_komentar = 'catatan_umum';
    }

    public function addComment()
    {
        $this->validate([
            'isi_komentar' => 'required|string|min:3',
            'status_komentar' => 'required|in:catatan_umum,revisi,diterima',
        ]);

        $dosen = auth()->user()->dosen;
        $pengajuan = PengajuanKP::findOrFail($this->selectedPengajuanId);

        KomentarBimbingan::create([
            'pengajuan_kp_id' => $pengajuan->id,
            'dosen_id' => $dosen->id,
            'isi_komentar' => $this->isi_komentar,
            'status_komentar' => $this->status_komentar,
        ]);

        // If comment status is 'revisi', update application status to 'revisi'
        if ($this->status_komentar === 'revisi') {
            $pengajuan->update([
                'status_pengajuan' => 'revisi',
            ]);
            $pengajuan->addProgress('revisi', 'Dosen memberikan catatan revisi bimbingan: ' . $this->isi_komentar);
        } elseif ($this->status_komentar === 'diterima') {
            $pengajuan->addProgress('bimbingan_diterima', 'Dosen menyetujui laporan/progress bimbingan.');
        }

        $this->isi_komentar = '';
        $this->dispatch('commentAdded'); // Livewire event dispatch
        
        session()->flash('message', 'Komentar berhasil ditambahkan.');
    }

    public function openDocModal($docId, $action)
    {
        $this->selectedDocId = $docId;
        $this->docStatusAction = $action;
        $this->docCatatan = '';
        $this->isOpenDocModal = true;
    }

    public function closeDocModal()
    {
        $this->isOpenDocModal = false;
        $this->selectedDocId = null;
        $this->docCatatan = '';
    }

    public function processDoc()
    {
        $doc = DokumenKP::findOrFail($this->selectedDocId);
        $pengajuan = $doc->pengajuan;

        if ($this->docStatusAction === 'perlu_revisi') {
            $this->validate([
                'docCatatan' => 'required|string|min:5',
            ]);

            $doc->update([
                'status' => 'perlu_revisi',
                'catatan' => $this->docCatatan,
            ]);

            $pengajuan->update([
                'status_pengajuan' => 'revisi',
            ]);

            // Auto add to KomentarBimbingan as a revision
            KomentarBimbingan::create([
                'pengajuan_kp_id' => $pengajuan->id,
                'dosen_id' => auth()->user()->dosen->id,
                'isi_komentar' => 'Revisi Dokumen (' . str_replace('_', ' ', $doc->jenis_dokumen) . '): ' . $this->docCatatan,
                'status_komentar' => 'revisi',
            ]);

            $pengajuan->addProgress('revisi', 'Dokumen ' . str_replace('_', ' ', $doc->jenis_dokumen) . ' ditandai perlu revisi: ' . $this->docCatatan);
        } else {
            $doc->update([
                'status' => 'diterima',
                'catatan' => $this->docCatatan ?: 'Disetujui',
            ]);

            $pengajuan->addProgress('dokumen_disetujui', 'Dokumen ' . str_replace('_', ' ', $doc->jenis_dokumen) . ' telah disetujui Dosen Pembimbing.');
        }

        $this->closeDocModal();
        session()->flash('message', 'Status dokumen berhasil diperbarui.');
    }

    public function hasUploadedAllRequiredDocs($pengajuan)
    {
        if (!$pengajuan) {
            return false;
        }

        $requiredTypes = ['surat_pengajuan', 'surat_penerimaan', 'laporan_kp', 'lampiran'];

        // Get the types of documents that have status 'sudah_upload' or 'diterima'
        $uploadedTypes = $pengajuan->dokumens
            ->whereIn('status', ['sudah_upload', 'diterima'])
            ->pluck('jenis_dokumen')
            ->toArray();

        // Check if all required types exist in the uploaded types
        foreach ($requiredTypes as $type) {
            if (!in_array($type, $uploadedTypes)) {
                return false;
            }
        }

        return true;
    }

    public function completeKP($id)
    {
        $pengajuan = PengajuanKP::with('dokumens')->findOrFail($id);
        $dosen = auth()->user()->dosen;

        if ($pengajuan->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized.');
        }

        // Validate kelengkapan dokumen di backend
        if (!$this->hasUploadedAllRequiredDocs($pengajuan)) {
            session()->flash('error', 'Kerja Praktik tidak dapat diselesaikan karena mahasiswa belum mengunggah seluruh dokumen persyaratan.');
            return;
        }

        // Change status to selesai
        $pengajuan->update([
            'status_pengajuan' => 'selesai',
        ]);

        $pengajuan->addProgress('selesai', 'Kerja Praktik (KP) dinyatakan selesai dan lulus oleh Dosen Pembimbing.');

        session()->flash('message', 'Kerja Praktik mahasiswa dinyatakan selesai.');

        if ($this->selectedPengajuanId == $id) {
            $this->selectStudent($id);
        }
    }

    public function download($id)
    {
        $doc = DokumenKP::findOrFail($id);

        // Ensure lecturer is authorized
        if ($doc->pengajuan->dosen_id !== auth()->user()->dosen->id) {
            abort(403, 'Unauthorized.');
        }

        return Storage::disk('public')->download($doc->path_file, $doc->nama_file);
    }

    public function render()
    {
        $dosen = auth()->user()->dosen;

        // Active guidance students list (Eager-load 'dokumens' for local checks)
        $students = PengajuanKP::with(['mahasiswa', 'dokumens'])
            ->where('dosen_id', $dosen->id)
            ->whereIn('status_pengajuan', [
                'diterima_admin',
                'revisi',
                'kp_berlangsung',
                'laporan_diupload',
                'selesai'
            ])
            ->latest()
            ->get();

        $selectedPengajuan = null;
        $comments = [];
        if ($this->selectedPengajuanId) {
            $selectedPengajuan = PengajuanKP::with(['mahasiswa', 'dokumens'])
                ->where('dosen_id', $dosen->id)
                ->findOrFail($this->selectedPengajuanId);

            $comments = KomentarBimbingan::with('dosen')
                ->where('pengajuan_kp_id', $this->selectedPengajuanId)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('livewire.dosen.bimbingan-manager', [
            'students' => $students,
            'selectedPengajuan' => $selectedPengajuan,
            'comments' => $comments,
        ])->layout('layouts.app');
    }
}
