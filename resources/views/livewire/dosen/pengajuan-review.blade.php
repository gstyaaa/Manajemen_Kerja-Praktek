<div>
    @section('page_title', 'Konfirmasi Pengajuan KP Mahasiswa')

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Daftar Pengajuan Masuk</h3>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($pengajuans as $pengajuan)
                <div class="p-6 hover:bg-slate-50/50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-3 flex-1">
                        <div>
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 rounded uppercase">
                                Menunggu Konfirmasi Anda
                            </span>
                            <h4 class="text-base font-bold text-slate-800 leading-snug mt-1.5">{{ $pengajuan->judul_kp }}</h4>
                            <p class="text-sm text-indigo-600 font-semibold mt-0.5">di {{ $pengajuan->nama_instansi }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6 text-xs text-slate-500">
                            <div>
                                <span>Mahasiswa: </span>
                                <strong class="text-slate-700">{{ $pengajuan->mahasiswa->nama }}</strong>
                                <span class="font-mono">({{ $pengajuan->mahasiswa->nim }})</span>
                            </div>
                            <div>
                                <span>Periode: </span>
                                <strong class="text-slate-700">
                                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}
                                </strong>
                            </div>
                            <div>
                                <span>Pembimbing Lapangan: </span>
                                <strong class="text-slate-700">{{ $pengajuan->pembimbing_lapangan }}</strong>
                                <span class="font-mono">({{ $pengajuan->kontak_pembimbing_lapangan }})</span>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-100/50 rounded-xl text-xs text-slate-600">
                            <span class="font-bold text-slate-700 block mb-1">Deskripsi Pekerjaan:</span>
                            <p class="whitespace-pre-line">{{ $pengajuan->deskripsi_pekerjaan }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 self-end md:self-center shrink-0">
                        <!-- Reject Trigger -->
                        <button wire:click="openReviewModal({{ $pengajuan->id }}, 'tolak')" class="px-4 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold rounded-xl transition-all border border-rose-100">
                            Tolak
                        </button>
                        <!-- Approve Trigger -->
                        <button wire:click="openReviewModal({{ $pengajuan->id }}, 'setujui')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-900/10">
                            Setujui
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-sm text-slate-400 font-medium">Belum ada pengajuan mahasiswa yang masuk untuk Anda.</div>
            @endforelse
        </div>
    </div>

    <!-- Review Confirmation Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden animate-zoom-in">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                        {{ $actionType === 'tolak' ? 'Tolak Pengajuan KP' : 'Setujui Pengajuan KP' }}
                    </h3>
                    <button wire:click="closeReviewModal" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="process" class="p-6 space-y-4">
                    @if($actionType === 'tolak')
                        <div class="p-3 bg-rose-50 text-rose-800 text-xs rounded-xl border border-rose-100 flex gap-2">
                            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>Anda menolak untuk menjadi pembimbing pengajuan ini. Sisa slot bimbingan Anda tidak akan terpakai oleh mahasiswa ini.</span>
                        </div>
                    @else
                        <div class="p-3 bg-emerald-50 text-emerald-800 text-xs rounded-xl border border-emerald-100 flex gap-2">
                            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Anda menyetujui untuk menjadi pembimbing pengajuan ini. Slot kuota bimbingan Anda akan tetap digunakan oleh mahasiswa ini.</span>
                        </div>
                    @endif

                    <!-- Catatan Dosen -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                            Catatan Dosen {{ $actionType === 'tolak' ? '(Wajib Diisi)' : '(Opsional)' }}
                        </label>
                        <textarea wire:model="catatan_dosen" rows="3" placeholder="Masukkan catatan, arahan, atau alasan penolakan..." class="w-full px-3.5 py-2 border rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all {{ $errors->has('catatan_dosen') ? 'border-rose-300' : 'border-slate-200' }}"></textarea>
                        @error('catatan_dosen') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeReviewModal" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-white text-sm font-semibold rounded-xl transition-all shadow-md
                            {{ $actionType === 'tolak' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-900/10' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-900/10' }}">
                            Kirim Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
