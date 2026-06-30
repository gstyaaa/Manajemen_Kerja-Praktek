<div class="h-[calc(100vh-8rem)] flex flex-col md:flex-row gap-6">
    @section('page_title', 'Bimbingan Kerja Praktik')

    <!-- Left Pane: Guidance List -->
    <div class="w-full md:w-80 bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col shrink-0">
        <div class="p-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Mahasiswa Aktif ({{ $students->count() }})</h3>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-slate-50">
            @forelse($students as $st)
                <button wire:click="selectStudent({{ $st->id }})" class="w-full text-left p-4 hover:bg-slate-50 transition-all focus:outline-none flex flex-col gap-1.5 {{ $selectedPengajuanId == $st->id ? 'bg-indigo-50/50 border-l-4 border-indigo-600 pl-3' : '' }}">
                    <div class="flex justify-between items-start gap-2 w-full">
                        <span class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $st->mahasiswa->nama }}</span>
                        @if($st->status_pengajuan === 'selesai')
                            <span class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-900 text-white rounded shrink-0">SELESAI</span>
                        @elseif($st->status_pengajuan === 'laporan_diupload')
                            <span class="px-1.5 py-0.5 text-[8px] font-bold bg-sky-100 text-sky-800 rounded shrink-0">LAPORAN</span>
                        @elseif($st->status_pengajuan === 'revisi')
                            <span class="px-1.5 py-0.5 text-[8px] font-bold bg-amber-100 text-amber-800 rounded shrink-0">REVISI</span>
                        @else
                            <span class="px-1.5 py-0.5 text-[8px] font-bold bg-indigo-100 text-indigo-800 rounded shrink-0 uppercase">{{ $st->status_pengajuan }}</span>
                        @endif
                    </div>
                    <span class="text-xs text-slate-400 font-mono">NIM: {{ $st->mahasiswa->nim }}</span>
                    <span class="text-xs text-slate-500 font-medium line-clamp-1">KP: {{ $st->judul_kp }}</span>
                </button>
            @empty
                <div class="p-8 text-center text-xs text-slate-400 font-medium">Belum ada mahasiswa bimbingan aktif.</div>
            @endforelse
        </div>
    </div>

    <!-- Right Pane: Details & Comments -->
    <div class="flex-1 bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        @if($selectedPengajuan)
            <!-- Workspace Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">{{ $selectedPengajuan->mahasiswa->nama }}</h3>
                    <p class="text-xs text-slate-500">{{ $selectedPengajuan->mahasiswa->program_studi }} • NIM: {{ $selectedPengajuan->mahasiswa->nim }}</p>
                </div>
                
                <div class="flex items-center gap-3">
                    @if($selectedPengajuan->status_pengajuan !== 'selesai')
                        @if($this->hasUploadedAllRequiredDocs($selectedPengajuan))
                            <button wire:click="completeKP({{ $selectedPengajuan->id }})" wire:confirm="Nyatakan Kerja Praktik mahasiswa ini SELESAI?" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                Selesaikan KP
                            </button>
                        @else
                            <button disabled class="px-4 py-2 bg-slate-200 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed shadow-none" title="Mahasiswa harus mengunggah seluruh dokumen persyaratan terlebih dahulu.">
                                Selesaikan KP
                            </button>
                        @endif
                    @else
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-800 text-xs font-bold rounded-xl border border-emerald-100 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            KP Selesai & Lulus
                        </span>
                    @endif
                </div>
            </div>

            <!-- Workspace Body (Scrollable split: Details & Comments) -->
            <div class="flex-1 overflow-hidden flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-slate-100">
                
                <!-- Left half: Documents & Info -->
                <div class="flex-1 p-6 overflow-y-auto space-y-6">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Topik KP</span>
                        <h4 class="text-sm font-semibold text-slate-800 mt-0.5">{{ $selectedPengajuan->judul_kp }}</h4>
                        <p class="text-xs text-indigo-600 font-medium mt-0.5">di {{ $selectedPengajuan->nama_instansi }}</p>
                    </div>

                    <!-- Documents status list -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Dokumen Bimbingan</span>
                            @if(!$this->hasUploadedAllRequiredDocs($selectedPengajuan) && $selectedPengajuan->status_pengajuan !== 'selesai')
                                <span class="text-[10px] text-amber-600 font-bold flex items-center gap-1">
                                    <span>⚠️ Dokumen belum lengkap</span>
                                </span>
                            @endif
                        </div>
                        
                        @php
                            $types = [
                                'surat_pengajuan' => 'Surat Pengajuan',
                                'surat_penerimaan' => 'Surat Penerimaan',
                                'laporan_kp' => 'Laporan KP',
                                'lampiran' => 'Lampiran',
                            ];
                        @endphp

                        @foreach($types as $typeKey => $typeLabel)
                            @php
                                $d = $selectedPengajuan->dokumens->firstWhere('jenis_dokumen', $typeKey);
                                $statusClass = [
                                    'belum_upload' => 'text-slate-400 bg-slate-50',
                                    'sudah_upload' => 'text-indigo-600 bg-indigo-50 border-indigo-100',
                                    'perlu_revisi' => 'text-amber-600 bg-amber-50 border-amber-100',
                                    'diterima' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                                ][$d ? $d->status : 'belum_upload'] ?? 'bg-slate-100';
                            @endphp
                            
                            <div wire:key="doc-row-{{ $typeKey }}" class="p-3 border border-slate-100 rounded-xl flex items-center justify-between text-xs hover:bg-slate-50/20 transition-colors">
                                <div class="overflow-hidden mr-3">
                                    <span class="font-semibold text-slate-700 block">{{ $typeLabel }}</span>
                                    @if($d)
                                        <span class="text-[10px] text-slate-400 truncate block font-mono">{{ $d->nama_file }}</span>
                                    @else
                                        <span class="text-[10px] text-slate-400 block italic">Belum diunggah</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($d)
                                        <!-- Download -->
                                        <button wire:click="download({{ $d->id }})" class="p-1.5 bg-white text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 rounded-lg transition-colors" title="Download">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0L8 8m4-4v12"></path></svg>
                                        </button>
                                        
                                        @if($d->status !== 'diterima' && $selectedPengajuan->status_pengajuan !== 'selesai')
                                            <!-- Minta Revisi -->
                                            <button wire:click="openDocModal({{ $d->id }}, 'perlu_revisi')" class="p-1.5 text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-100 rounded-lg transition-all" title="Minta Revisi">
                                                Revisi
                                            </button>
                                            <!-- Terima -->
                                            <button wire:click="openDocModal({{ $d->id }}, 'diterima')" class="p-1.5 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 rounded-lg transition-all" title="Setujui">
                                                Setujui
                                            </button>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold border capitalize {{ $statusClass }}">
                                                {{ str_replace('_', ' ', $d->status) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-400 border border-slate-100">
                                            Kosong
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right half: Guidance panel (Island Component) -->
                <div class="flex-1 flex flex-col h-full bg-slate-50/50">
                    <div class="px-4 py-2 border-b border-slate-100 bg-slate-50 flex items-center justify-between shrink-0">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Log Bimbingan (Komentar)</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>

                    <!-- Comments List -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4 max-h-[350px]">
                        @forelse($comments as $comment)
                            <div class="flex flex-col gap-1 max-w-[85%] {{ $comment->dosen_id ? 'ml-auto items-end' : 'mr-auto items-start' }}">
                                <div class="p-3 rounded-2xl text-xs {{ $comment->dosen_id ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white border border-slate-200/60 text-slate-700 rounded-tl-none shadow-sm' }}">
                                    <p class="whitespace-pre-line leading-relaxed">{{ $comment->isi_komentar }}</p>
                                    
                                    @if($comment->status_komentar === 'revisi')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold mt-1.5 uppercase {{ $comment->dosen_id ? 'bg-indigo-800 text-indigo-100' : 'bg-amber-100 text-amber-800' }}">
                                            Perlu Revisi
                                        </span>
                                    @elseif($comment->status_komentar === 'diterima')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold mt-1.5 uppercase {{ $comment->dosen_id ? 'bg-indigo-800 text-indigo-100' : 'bg-emerald-100 text-emerald-800' }}">
                                            Disetujui
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[9px] text-slate-400 font-mono px-1">{{ $comment->created_at->format('d M H:i') }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-12">Belum ada diskusi/bimbingan tertulis.</p>
                        @endforelse
                    </div>

                    <!-- Add Comment input -->
                    @if($selectedPengajuan->status_pengajuan !== 'selesai')
                        <div class="p-4 border-t border-slate-100 bg-white shrink-0 space-y-3">
                            <!-- Comment status type -->
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center text-xs">
                                    <input type="radio" wire:model="status_komentar" value="catatan_umum" class="text-indigo-600 focus:ring-indigo-500" />
                                    <span class="ml-1 text-slate-600 font-medium">Catatan Umum</span>
                                </label>
                                <label class="inline-flex items-center text-xs">
                                    <input type="radio" wire:model="status_komentar" value="revisi" class="text-amber-600 focus:ring-amber-500" />
                                    <span class="ml-1 text-slate-600 font-medium">Revisi Laporan</span>
                                </label>
                                <label class="inline-flex items-center text-xs">
                                    <input type="radio" wire:model="status_komentar" value="diterima" class="text-emerald-600 focus:ring-emerald-500" />
                                    <span class="ml-1 text-slate-600 font-medium">Terima Progress</span>
                                </label>
                            </div>

                            <div class="flex gap-2">
                                <textarea wire:model="isi_komentar" rows="1" placeholder="Tulis komentar bimbingan..." class="flex-1 px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all resize-none"></textarea>
                                <button wire:click="addComment" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shrink-0 self-end">
                                    Kirim
                                </button>
                            </div>
                            @error('isi_komentar') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Placeholder screen -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4">
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center border border-slate-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Pilih Mahasiswa Bimbingan</h3>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">
                        Pilih salah satu mahasiswa dari daftar di sebelah kiri untuk meninjau berkas, memberikan komentar revisi, dan memantau progres KP.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Document Review Modal (Approval/Rejection with notes) -->
    @if($isOpenDocModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden animate-zoom-in">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                        {{ $docStatusAction === 'perlu_revisi' ? 'Minta Revisi Dokumen' : 'Setujui Dokumen' }}
                    </h3>
                    <button wire:click="closeDocModal" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="processDoc" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                            Catatan Dosen {{ $docStatusAction === 'perlu_revisi' ? '(Wajib Diisi)' : '(Opsional)' }}
                        </label>
                        <textarea wire:model="docCatatan" rows="3" placeholder="Tulis catatan persetujuan atau detail perbaikan dokumen..." class="w-full px-3.5 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all {{ $errors->has('docCatatan') ? 'border-rose-300' : 'border-slate-200' }}"></textarea>
                        @error('docCatatan') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeDocModal" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-white text-sm font-semibold rounded-xl transition-all shadow-md
                            {{ $docStatusAction === 'perlu_revisi' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-900/10' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-900/10' }}">
                            Proses Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
