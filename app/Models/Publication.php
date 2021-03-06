<?php

namespace App\Models;

use App\Models\BaseModel;

class Publication extends BaseModel
{
    protected $fillable = [
        // 'nidn',
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

    // public function teacher()
    // {
    //     return $this->belongsTo('App\Models\Teacher', 'nidn');
    // }

    public function academicYear()
    {
        return $this->belongsTo('App\Models\AcademicYear', 'id_ta');
    }

    public function publicationCategory()
    {
        return $this->belongsTo('App\Models\PublicationCategory', 'jenis_publikasi');
    }

    public function publicationMembers()
    {
        return $this->hasMany('App\Models\PublicationMember', 'id_publikasi');
    }

    public function penulisUtama()
    {
        return $this->hasOne('App\Models\PublicationMember', 'id_publikasi')->where('penulis_utama', true);
    }

    public function penulisAnggota()
    {
        return $this->hasMany('App\Models\PublicationMember', 'id_publikasi')->where('penulis_utama', false);
    }

    public function publikasiDosen()
    {
        return $this->hasMany('App\Models\PublicationMember', 'id_publikasi')->where('status', 'Dosen');
    }

    public function publikasiMahasiswa()
    {
        return $this->hasMany('App\Models\PublicationMember', 'id_publikasi')->where('status', 'Mahasiswa');
    }

    public function publikasiLainnya()
    {
        return $this->hasMany('App\Models\PublicationMember', 'id_publikasi')->where('status', 'Lainnya');
    }

    public function publikasiNotLainnya()
    {
        return $this->hasMany('App\Models\PublicationMember', 'id_publikasi')->where('status', '!=', 'Lainnya');
    }
}
