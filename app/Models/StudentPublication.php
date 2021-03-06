<?php

namespace App\Models;

use App\Models\BaseModel;

class StudentPublication extends BaseModel
{
    protected $fillable = [
        'nim',
        'jenis_publikasi',
        'judul',
        'penerbit',
        'id_ta',
        // 'tahun',
        'sesuai_prodi',
        'sitasi',
        'jurnal',
        'akreditasi',
        'tautan',
    ];

    public function student()
    {
        return $this->belongsTo('App\Models\Student','nim');
    }

    public function academicYear()
    {
        return $this->belongsTo('App\Models\AcademicYear','id_ta');
    }

    public function publicationCategory()
    {
        return $this->belongsTo('App\Models\PublicationCategory','jenis_publikasi');
    }

    public function publicationMembers()
    {
        return $this->hasMany('App\Models\StudentPublicationMember','id_publikasi');
    }
}
