<?php

use Illuminate\Database\Seeder;
use App\Teacher;
use App\AcademicYear;
class EwmpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 50; $i++){
            // insert data ke table pegawai menggunakan Faker
            DB::table('ewmps')->insert([
                'nidn'                  => Teacher::inRandomOrder()->where('ikatan_kerja','Dosen Tetap')->first()->nidn,
                'id_ta'                 => AcademicYear::all()->random()->id,
                'ps_intra'              => rand(0, 40),
                'ps_lain'               => rand(0, 40),
                'ps_luar'               => rand(0, 40),
                'penelitian'            => rand(0, 40),
                'pkm'                   => rand(0, 40),
                'tugas_tambahan'        => rand(0, 40),
                'created_at' => now()
            ]);
        }
    }
}
