<?php

namespace Database\Seeders;

    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    use App\Models\User; // Import model User
    use Illuminate\Support\Facades\Hash; // Untuk hashing password

    class AdminUserSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            User::firstOrCreate(
                ['email' => 'superadmin@example.com'], // Email unik superadmin
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('superadmin1234'), // Password bisa diganti sesuai kebutuhan
                    'role' => 'superadmin',
                    'email_verified_at' => now(),
                ]
            );

            // Buat user admin jika belum ada
            User::firstOrCreate(
                ['email' => 'admin@example.com'], // Cari berdasarkan email
                [
                    'name' => 'Admin BKPSDM',
                    'password' => Hash::make('admin1234'), // Ganti dengan password yang kuat!
                    'role' => 'admin',
                    'email_verified_at' => now(), // Opsional, tandai sudah terverifikasi
                ]
            );

            // Contoh membuat user pegawai
            User::firstOrCreate(
                ['email' => 'pegawai@example.com'],
                [
                    'name' => 'Pegawai Biasa',
                    'password' => Hash::make('user1234'), // Ganti dengan password yang kuat!
                    'role' => 'pegawai',
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info('Superadmin, Admin, and Pegawai users seeded!');
        }
    }
    