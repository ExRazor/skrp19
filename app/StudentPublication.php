<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentPublication extends Model
{
    protected $fillable = [
        'nim',
        'jenis_publikasi',
        'judul',
        'penerbit',
        'tahun',
        'sesuai_prodi',
        'sitasi',
        'jurnal',
        'akreditasi',
        'tautan',
    ];

    public function student()
    {
        return $this->belongsTo('App\Student','nim');
    }

    public function publicationCategory()
    {
        return $this->belongsTo('App\PublicationCategory','jenis_publikasi');
    }

    public function publicationMembers()
    {
        return $this->hasMany('App\StudentPublicationMember','id_publikasi');
    }
}