<?php

use Illuminate\Database\Seeder;
use App\Imports\CurriculumImport;
use Maatwebsite\Excel\Facades\Excel;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new CurriculumImport, storage_path('app/upload/curriculum/excel_import/matakuliah.xlsx'));
    }
}
