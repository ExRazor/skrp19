<?php

namespace App\Imports;

use App\Student;
use App\StudyProgram;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kd_prodi = StudyProgram::where('nama',$row[0])->first()->kd_prodi;

        // dd($row);
        return new Student([
            'kd_matkul'     => $row[1],
            'kd_prodi'      => $kd_prodi,
            'nama'          => $row[2],
            'versi'         => $row[3],
            'jenis'         => $row[4],
            'semester'      => $row[5],
            'sks_teori'     => $row[6],
            'sks_seminar'   => '0',
            'sks_praktikum' => '0',
            'capaian'       => 'Pengetahuan',
            'dokumen_nama'  => 'RPB - 2017'
        ]);
    }

    public function startRow() : int
    {
        return 2;
    }
}
