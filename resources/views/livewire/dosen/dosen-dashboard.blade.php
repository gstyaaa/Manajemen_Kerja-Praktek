<div>
    @section('page_title', 'Dashboard Dosen')

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-3">
        <!-- Stat Card: Pending Requests -->
        <div class="glass-card p-6 rounded-xl hover:-translate-y-1 transition-all duration-300 flex items-center">
            <div class="p-3 bg-error/10 text-error rounded-xl mr-4">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wider text-on-surface-variant uppercase">Menunggu Konfirmasi</p>
                <p class="text-2xl font-headline font-bold text-on-surface">{{ $pendingCount }}</p>
            </div>
        </div>

        <!-- Stat Card: Active Guidance -->
        <div class="glass-card p-6 rounded-xl hover:-translate-y-1 transition-all duration-300 flex items-center">
            <div class="p-3 bg-primary/5 text-primary rounded-xl mr-4">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wider text-on-surface-variant uppercase">Bimbingan Aktif</p>
                <p class="text-2xl font-headline font-bold text-on-surface">{{ $activeCount }}</p>
            </div>
        </div>

        <!-- Stat Card: Remaining Slots -->
        <div class="glass-card p-6 rounded-xl hover:-translate-y-1 transition-all duration-300 flex items-center">
            @php
                $isFull = $sisaQuota === 0;
                $isCritical = $sisaQuota === 1;
                $bgColor = $isFull ? 'bg-error/10 text-error' : ($isCritical ? 'bg-amber-100 text-amber-600' : 'bg-tertiary-fixed/30 text-on-tertiary-fixed-variant');
            @endphp
            <div class="p-3 rounded-xl mr-4 {{ $bgColor }}">
                <span class="material-symbols-outlined">assignment_turned_in</span>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wider text-on-surface-variant uppercase">Sisa Kuota Bimbingan</p>
                <p class="text-2xl font-headline font-bold text-on-surface">{{ $sisaQuota }} / {{ $dosen->kuota_bimbingan }}</p>
            </div>
        </div>
    </div>

    <!-- Main Dash Rows -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 mb-8">
        <!-- Pending Confirmations (Left 2 Columns) -->
        <div class="glass-card p-6 rounded-xl hover:-translate-y-1 transition-all duration-300 lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-headline font-bold text-primary">Pengajuan KP Perlu Konfirmasi</h3>
                <a href="{{ route('dosen.pengajuan') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-outline-variant/30 bg-surface-container-low text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">
                            <th class="px-4 py-3">Mahasiswa</th>
                            <th class="px-4 py-3">Instansi / Judul</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        @forelse($pendingPengajuans as $pengajuan)
                            <tr class="hover:bg-surface-container-lowest transition-colors group">
                                <td class="px-4 py-4">
                                    <p class="text-sm font-bold text-on-surface">{{ $pengajuan->mahasiswa->nama }}</p>
                                    <span class="text-xs text-on-surface-variant font-mono">{{ $pengajuan->mahasiswa->nim }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-semibold text-on-surface truncate max-w-[200px]">{{ $pengajuan->judul_kp }}</p>
                                    <span class="text-xs text-secondary font-semibold">{{ $pengajuan->nama_instansi }}</span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('dosen.pengajuan') }}" class="inline-flex items-center px-3 py-1.5 bg-primary text-on-primary hover:bg-primary/95 text-xs font-bold rounded-lg transition-colors">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-xs text-on-surface-variant font-medium">Tidak ada pengajuan baru yang menunggu konfirmasi Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quota Progress Indicator (Right 1 Column) -->
        <div class="glass-card p-6 rounded-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-headline font-bold text-primary mb-6">Status Slot Bimbingan</h3>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs text-on-surface-variant">Total Bimbingan Saat Ini</span>
                    <span class="text-sm font-bold text-on-surface">{{ $activeCount }} / {{ $dosen->kuota_bimbingan }}</span>
                </div>
                
                @php
                    $percent = ($dosen->kuota_bimbingan - $sisaQuota) / $dosen->kuota_bimbingan * 100;
                    $barColor = $isFull ? 'bg-error' : ($isCritical ? 'bg-amber-500' : 'bg-primary');
                @endphp
                <div class="w-full bg-surface-container-high rounded-full h-3 mb-6 overflow-hidden">
                    <div class="h-3 rounded-full {{ $barColor }} transition-all" style="width: {{ $percent }}%"></div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs p-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant/20">
                        <span class="text-on-surface-variant">Kuota Maksimal</span>
                        <span class="font-bold text-on-surface">{{ $dosen->kuota_bimbingan }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs p-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant/20">
                        <span class="text-on-surface-variant">Slot Terisi (Aktif)</span>
                        <span class="font-bold text-on-surface">{{ $activeCount }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs p-2.5 rounded-lg bg-surface-container-lowest border border-outline-variant/20">
                        <span class="text-on-surface-variant">Sisa Slot Kosong</span>
                        <span class="font-bold {{ $isFull ? 'text-error' : 'text-[#27c38a]' }}">{{ $sisaQuota }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Active Guidance (Left) -->
        <div class="glass-card p-6 rounded-xl hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-headline font-bold text-primary">Daftar Mahasiswa Bimbingan</h3>
                <a href="{{ route('dosen.bimbingan') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-4">
                @forelse($bimbingans as $guide)
                    <div class="flex items-center justify-between p-4 bg-surface-container-lowest rounded-xl border border-outline-variant/20">
                        <div>
                            <h4 class="text-sm font-bold text-on-surface">{{ $guide->mahasiswa->nama }}</h4>
                            <p class="text-xs text-on-surface-variant mt-0.5">{{ $guide->mahasiswa->program_studi }} • NIM: {{ $guide->mahasiswa->nim }}</p>
                        </div>
                        @php
                            $badges = [
                                'kp_berlangsung' => 'bg-primary-container/20 text-primary',
                                'laporan_diupload' => 'bg-secondary-container/20 text-[#0060ac]',
                                'selesai' => 'bg-primary text-on-primary',
                            ];
                            $badgeClass = $badges[$guide->status_pengajuan] ?? 'bg-surface-container text-on-surface-variant';
                        @endphp
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded capitalize {{ $badgeClass }}">
                            {{ str_replace('_', ' ', $guide->status_pengajuan) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-on-surface-variant text-center py-8">Belum ada mahasiswa bimbingan aktif.</p>
                @endforelse
            </div>
        </div>

        <!-- Reports Needing Review (Right) -->
        <div class="glass-card p-6 rounded-xl hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-headline font-bold text-primary">Laporan Baru Perlu Review</h3>
                <a href="{{ route('dosen.bimbingan') }}" class="text-xs font-bold text-primary hover:underline">Tinjau Laporan</a>
            </div>

            <div class="space-y-4">
                @forelse($laporans as $laporan)
                    <div class="flex items-center justify-between p-4 bg-error-container/10 border border-error-container/20 rounded-xl">
                        <div>
                            <h4 class="text-sm font-bold text-on-surface">{{ $laporan->pengajuan->mahasiswa->nama }}</h4>
                            <p class="text-xs text-on-surface-variant mt-0.5">{{ $laporan->nama_file }}</p>
                            <span class="text-[9px] text-error font-mono tracking-wider uppercase block mt-1">Status: Perlu Tinjauan</span>
                        </div>
                        <a href="{{ route('dosen.bimbingan') }}" class="inline-flex items-center px-3 py-1.5 bg-primary text-on-primary hover:bg-primary/95 text-xs font-bold rounded-lg transition-colors">
                            Review
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-on-surface-variant text-center py-8">Tidak ada laporan baru yang perlu direview.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
