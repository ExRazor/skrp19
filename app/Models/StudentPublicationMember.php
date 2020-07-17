<?php

namespace App\Models;

use App\Models\BaseModel;

class StudentPublicationMember extends BaseModel
{
    protected $fillable = [
        'id_publikasi',
        'nim',
        'nama',
        'kd_prodi',
    ];

    public function publication()
    {
        return $this->belongsTo('App\Models\StudentPublication','id_publikasi');
    }

    public function studyProgram()
    {
        return $this->belongsTo('App\Models\StudyProgram','kd_prodi');
    }
}