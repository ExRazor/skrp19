<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherPublicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'jenis_publikasi' => 'required',
            'nidn'            => 'required',
            'judul'           => 'required',
            'penerbit'        => 'required',
            'id_ta'           => 'required',
            'jurnal'          => 'nullable',
            'sesuai_prodi'    => 'nullable',
            'akreditasi'      => 'nullable',
            'sitasi'          => 'nullable|numeric',
            'tautan'          => 'nullable|url',
        ];
    }

    public function attributes()
    {
        return [
            'jenis_publikasi' => 'Jenis Publikasi',
            'nidn'            => 'Dosen',
            'judul'           => 'Judul Publikasi',
            'penerbit'        => 'Penerbit',
            'id_ta'           => 'Tahun Publikasi',
            'jurnal'          => 'Jurnal',
            'sesuai_prodi'    => 'Kesesuaian Prodi',
            'akreditasi'      => 'Akreditasi',
            'sitasi'          => 'Jumlah Sitasi',
            'tautan'          => 'Tautan',
        ];
    }
}
