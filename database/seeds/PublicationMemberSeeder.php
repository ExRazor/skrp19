<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Publication;
use App\Models\StudyProgram;
use App\Models\Teacher;
use App\Models\Student;

class PublicationMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $data = Publication::all();

        foreach ($data as $d) {
            $anggota = rand(1, 5);
            for ($i = 0; $i < $anggota; $i++) {

                if ($i == 0) {
                    $penulis_utama = true;
                } else {
                    $penulis_utama = false;
                }

                $cek = rand(0, 2); // 0 = dosen || 1 = mahasiswa || 2 = lainnya

                if ($cek == 0) {
                    $nidn    = Teacher::all()->random()->nidn;
                    $nim     = null;
                    $nama    = null;
                    $asal    = null;
                    $status  = 'Dosen';
                } else if ($cek == 1) {
                    $nidn     = null;
                    $nim      = Student::all()->random()->nim;
                    $nama     = null;
                    $asal     = null;
                    $status   = 'Mahasiswa';
                } else if ($cek == 2) {
                    $nidn    = null;
                    $nim     = null;
                    $nama    = $faker->name;
                    $asal    = $faker->address;
                    $status  = 'Lainnya';
                }

                DB::table('publication_members')->insert([
                    'id_publikasi'      => $d->id,
                    'nidn'              => $nidn,
                    'nim'               => $nim,
                    'nama'              => $nama,
                    'asal'              => $asal,
                    'status'            => $status,
                    'penulis_utama'     => $penulis_utama,
                    // 'kd_prodi'          => StudyProgram::all()->random()->kd_prodi,
                    'created_at'        => now()
                ]);
            }
        }
    }
}
