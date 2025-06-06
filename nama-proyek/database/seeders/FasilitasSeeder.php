<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $fasilitas = [
            'AC',
            'WiFi',
            'TV',
            'Kamar Mandi Dalam',
            'Air Panas',
            'Handuk',
            'Peralatan Mandi',
            'Meja & Kursi',
            'Lemari Pakaian',
            'Kulkas Mini',
            'Dispenser',
            'Cermin',
            'Lampu Tidur',
        ];

        foreach ($fasilitas as $nama) {
            DB::table('fasilitas')->insert([
                'nama' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
