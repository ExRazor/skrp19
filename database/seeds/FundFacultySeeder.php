<?php

use Illuminate\Database\Seeder;
use App\Faculty;
use App\FundingCategory;
use App\AcademicYear;

class FundFacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $academicYear = AcademicYear::where('semester','Ganjil')->where('tahun_akademik','>','2013')->get();
        $category   = FundingCategory::all();

        foreach($academicYear as $ay) {
            foreach($category as $c) {
                DB::table('funding_faculties')->insert([
                    'id_fakultas'   => Faculty::where('id',setting('app_faculty_id'))->first()->id,
                    'id_ta'         => $ay->id,
                    'id_kategori'   => $c->id,
                    'nominal'       => rand(10000000, 50000000),
                    'created_at'    => now()
                ]);
            }
        }
    }
}
