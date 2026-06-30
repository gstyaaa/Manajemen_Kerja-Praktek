<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        User::create([
            'name' => 'Admin Prodi',
            'email' => 'admin@kp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Seed Dosen
        $dosenData = [
            [
                'name' => 'Dr. Ir. Budi Raharjo',
                'email' => 'budi@kp.com',
                'nidn' => '0401017801',
                'bidang_keahlian' => 'Sistem Informasi',
                'no_hp' => '08111222333',
            ],
            [
                'name' => 'Prof. Sri Lestari',
                'email' => 'sri@kp.com',
                'nidn' => '0402028002',
                'bidang_keahlian' => 'Kecerdasan Buatan',
                'no_hp' => '08222333444',
            ],
            [
                'name' => 'Joko Susilo, M.T.',
                'email' => 'joko@kp.com',
                'nidn' => '0403038203',
                'bidang_keahlian' => 'Rekayasa Perangkat Lunak',
                'no_hp' => '08333444555',
            ]
        ];

        foreach ($dosenData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'dosen',
            ]);

            Dosen::create([
                'user_id' => $user->id,
                'nama' => $data['name'],
                'nidn' => $data['nidn'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'bidang_keahlian' => $data['bidang_keahlian'],
                'status' => true,
                'kuota_bimbingan' => 5,
            ]);
        }

        // 3. Seed Mahasiswa
        $mahasiswaData = [
            [
                'name' => 'Aditya Pratama',
                'email' => 'aditya@kp.com',
                'nim' => 'H1D023001',
                'program_studi' => 'Informatika',
                'angkatan' => '2023',
                'no_hp' => '081234567890',
            ],
            [
                'name' => 'Citra Kirana',
                'email' => 'citra@kp.com',
                'nim' => 'H1D023002',
                'program_studi' => 'Informatika',
                'angkatan' => '2023',
                'no_hp' => '081234567891',
            ],
            [
                'name' => 'Fahri Hamzah',
                'email' => 'fahri@kp.com',
                'nim' => 'H1D023003',
                'program_studi' => 'Sistem Informasi',
                'angkatan' => '2023',
                'no_hp' => '081234567892',
            ]
        ];

        foreach ($mahasiswaData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'nama' => $data['name'],
                'email' => $data['email'],
                'program_studi' => $data['program_studi'],
                'angkatan' => $data['angkatan'],
                'no_hp' => $data['no_hp'],
                'status_aktif' => true,
            ]);
        }
    }
}
