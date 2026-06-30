<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('email');
            $table->string('program_studi');
            $table->string('angkatan');
            $table->string('no_hp');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('nidn')->unique();
            $table->string('email');
            $table->string('no_hp');
            $table->string('bidang_keahlian');
            $table->boolean('status')->default(true);
            $table->integer('kuota_bimbingan')->default(5);
            $table->timestamps();
        });

        Schema::create('pengajuan_kps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->string('judul_kp');
            $table->string('nama_instansi');
            $table->text('alamat_instansi');
            $table->string('pembimbing_lapangan');
            $table->string('kontak_pembimbing_lapangan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('deskripsi_pekerjaan');
            $table->enum('status_pengajuan', [
                'draft',
                'diajukan',
                'menunggu_konfirmasi_dosen',
                'disetujui_dosen',
                'ditolak_dosen',
                'diterima_admin',
                'ditolak_admin',
                'revisi',
                'kp_berlangsung',
                'laporan_diupload',
                'dibatalkan',
                'selesai'
            ])->default('draft');
            $table->enum('status_persetujuan_dosen', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_dosen')->nullable();
            $table->timestamp('tanggal_konfirmasi_dosen')->nullable();
            $table->timestamps();
        });

        Schema::create('dokumen_kps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_kp_id')->constrained('pengajuan_kps')->onDelete('cascade');
            $table->enum('jenis_dokumen', ['surat_pengajuan', 'surat_penerimaan', 'laporan_kp', 'lampiran']);
            $table->string('nama_file');
            $table->string('path_file');
            $table->enum('status', ['belum_upload', 'sudah_upload', 'perlu_revisi', 'diterima'])->default('belum_upload');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('komentar_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_kp_id')->constrained('pengajuan_kps')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->text('isi_komentar');
            $table->enum('status_komentar', ['catatan_umum', 'revisi', 'diterima'])->default('catatan_umum');
            $table->timestamps();
        });

        Schema::create('progres_kps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_kp_id')->constrained('pengajuan_kps')->onDelete('cascade');
            $table->string('tahap');
            $table->string('deskripsi');
            $table->timestamp('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres_kps');
        Schema::dropIfExists('komentar_bimbingans');
        Schema::dropIfExists('dokumen_kps');
        Schema::dropIfExists('pengajuan_kps');
        Schema::dropIfExists('dosens');
        Schema::dropIfExists('mahasiswas');
    }
};
