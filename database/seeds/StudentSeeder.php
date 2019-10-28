<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\StudyProgram;
use App\AcademicYear;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $jk = ['Laki-Laki','Perempuan'];
        $agama = ['Islam','Kristen','Katholik','Buddha','Hindu','Kong Hu Cu'];
        $kewarganegaraan = ['WNI','WNA'];
        $kelas = ['Reguler','Non-Reguler'];
        $tipe = ['Reguler','Non-Reguler','Alih Status','Beasiswa'];
        $program = ['Reguler','Asing'];
        $seleksi_jenis = ['Nasional','Lokal'];
        $seleksi_jalur= [
            'Nasional' => array('SBMPTN','SNMPTN'),
            'Lokal'    => array('Mandiri')
        ];
        $masuk_status = ['Baru','Pindahan'];
        $status       = ['Aktif','Nonaktif','Lulus'];

    	for($i = 0; $i < 200; $i++){
                // insert data ke table pegawai menggunakan Faker
            $jenis_seleksi  = $seleksi_jenis[array_rand($seleksi_jenis)];
            $count_jalur    = count($seleksi_jalur[$jenis_seleksi])-1;
            $jalur_seleksi  = $seleksi_jalur[$jenis_seleksi][rand(0,$count_jalur)];
            $ta             = AcademicYear::all()->random();

            DB::table('students')->insert([
                'nim'                   => rand(000000000, 999999999),
                'nama'                  => $faker->name,
                'tpt_lhr'               => $faker->city,
                'tgl_lhr'               => $faker->date($format = 'Y-m-d', $max = '2001-12-31'),
                'jk'                    => $jk[array_rand($jk)],
                'agama'                 => $agama[array_rand($agama)],
                'alamat'                => $faker->address,
                'kewarganegaraan'       => $kewarganegaraan[array_rand($kewarganegaraan)],
                'kd_prodi'              => StudyProgram::all()->random()->kd_prodi,
                'kelas'                 => $kelas[array_rand($kelas)],
                'tipe'                  => $tipe[array_rand($tipe)],
                'program'               => $program[array_rand($program)],
                'seleksi_jenis'         => $jenis_seleksi,
                'seleksi_jalur'         => $jalur_seleksi,
                'masuk_status'          => $masuk_status[array_rand($masuk_status)],
                'masuk_ta'              => $ta->id,
                'status'                => $status[array_rand($status)],
                'angkatan'              => $ta->tahun_akademik,
                'created_at'            => now()
            ]);
        }
    }
}
