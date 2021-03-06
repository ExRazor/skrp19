@extends('layouts.master')

@section('title', isset($data) ? 'Sunting Kurikulum' : 'Tambah Kurikulum')

@section('content')
<div class="br-pageheader">
    <nav class="breadcrumb pd-0 mg-0 tx-12">
        @foreach (Breadcrumbs::generate( isset($data) ? 'academic-curriculum-edit' : 'academic-curriculum-create', isset($data) ? $data : '' ) as $breadcrumb)
            @if($breadcrumb->url && !$loop->last)
                <a class="breadcrumb-item" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
            @else
                <span class="breadcrumb-item">{{ $breadcrumb->title }}</span>
            @endif
        @endforeach
    </nav>
</div>

<div class="br-pagetitle">
    <div class="d-flex pl-0 mb-3">
        <i class="icon fa fa-pen-square"></i>
        @if(isset($data))
        <div>
            <h4>Sunting</h4>
            <p class="mg-b-0">Sunting Kurikulum</p>
        </div>
        @else
        <div>
            <h4>Tambah</h4>
            <p class="mg-b-0">Tambah Kurikulum</p>
        </div>
        @endif
    </div>
</div>

<div class="br-pagebody">
    @if($errors->any())
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
        </button>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif
    @if (session()->has('flash.message'))
        <div class="alert alert-{{ session('flash.class') }}" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('flash.message') }}
        </div>
    @endif
    <div class="widget-2">
        <div class="card mb-3">
            <form id="research_form" action="{{isset($data) ? route('academic.curriculum.update',$data->id) : route('academic.curriculum.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
                <div class="card-body bd bd-y-0 bd-color-gray-lighter">
                    <div class="row">
                        <div class="col-md-9 mx-auto">
                            @csrf
                            @if(isset($data))
                                @method('put')
                                <input type="hidden" name="id" value="{{encrypt($data->id)}}">
                            @else
                                @method('post')
                            @endif
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Program Studi: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    @if(Auth::user()->hasRole('kaprodi'))
                                    <input type="hidden" name="kd_prodi" value="{{Auth::user()->kd_prodi}}">
                                    @endif
                                    <select id="prodi_dosen" class="form-control" name="kd_prodi" {{Auth::user()->hasRole('kaprodi') ? 'disabled' : 'required'}}>
                                        <option value="">- Pilih Prodi -</option>
                                        @foreach($studyProgram as $sp)
                                        <option value="{{$sp->kd_prodi}}" {{ (isset($data) && ($sp->kd_prodi==$data->kd_prodi)) || old('kd_prodi')==$sp->kd_prodi || Auth::user()->kd_prodi==$sp->kd_prodi ? 'selected' : ''}}>{{$sp->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Tahun Kurikulum: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control number" type="text" name="versi" value="{{ isset($data) ? $data->versi : old('versi')}}" placeholder="Masukkan edisi kurikulum" maxlength="4" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Kode Mata Kuliah: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="kd_matkul" value="{{ isset($data) ? $data->kd_matkul : old('kd_matkul')}}" placeholder="Masukkan kode unik mata kuliah" maxlength="10" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Nama Mata Kuliah: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="nama" value="{{ isset($data) ? $data->nama : old('nama')}}" placeholder="Masukkan tema penelitian sesuai roadmap" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Semester: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control" name="semester" required>
                                        <option value="">- Pilih Semester -</option>
                                        @for($i=1;$i<=8;$i++)
                                        <option value="{{$i}}" {{ (isset($data) && $data->semester == $i) ||  old('semester')==$i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Jenis Mata Kuliah: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <div id="jenis" class="radio">
                                        <label class="rdiobox rdiobox-inline mb-0">
                                            <input name="jenis" type="radio" value="Wajib" {{ (isset($data) && $data->jenis=='Wajib') || old('jenis')=='Wajib' ? 'checked' : ''}} data-parsley-class-handler="#jenis"
                                            data-parsley-errors-container="#errorsJNS" required>
                                            <span>Wajib</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </label>
                                        <label class="rdiobox rdiobox-inline mb-0">
                                            <input name="jenis" type="radio" value="Pilihan" {{ (isset($data) && $data->jenis=='Pilihan') || old('jenis')=='Pilihan' ? 'checked' : ''}}>
                                            <span>Pilihan</span>
                                        </label>
                                    </div>
                                    <div id="errorsJNS"></div>
                                </div>
                            </div>
                            <div class="form-group row mg-t-20">
                                <label class="col-sm-3 form-control-label">SKS: <span class="tx-danger">*</span> </label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-4 pr-1">
                                            <input type="text" class="form-control number" name="sks_teori" placeholder="SKS Teori" value="{{isset($data) ? $data->sks_teori : old('sks_teori')}}" required>
                                        </div>
                                        <div class="col-4 px-1">
                                            <input type="text" class="form-control number" name="sks_seminar" placeholder="SKS Seminar" value="{{isset($data) ? $data->sks_seminar : old('sks_seminar')}}" required>
                                        </div>
                                        <div class="col-4 pl-1">
                                            <input type="text" class="form-control number" name="sks_praktikum" placeholder="SKS Praktikum" value="{{isset($data) ? $data->sks_praktikum : old('sks_praktikum')}}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Capaian Mata Kuliah: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <div id="capaian" class="checkbox">
                                        <label class="ckbox ckbox-inline mb-0 mr-4">
                                            <input name="capaian[]" type="checkbox" value="Pengetahuan" {{ (isset($data) && (in_array('Pengetahuan',$data->capaian))) || old('capaian') == 'Pengetahuan' ? 'checked' : ''}} data-parsley-class-handler="#capaian"
                                            data-parsley-errors-container="#errorsCapaian" required>
                                            <span class="pl-0">Pengetahuan</span>
                                        </label>
                                        <label class="ckbox ckbox-inline mb-0 mr-4">
                                            <input name="capaian[]" type="checkbox" value="Sikap" {{ (isset($data) && (in_array('Sikap',$data->capaian))) || old('capaian')=='Sikap' ? 'checked' : ''}}>
                                            <span class="pl-0">Sikap</span>
                                        </label>
                                        <label class="ckbox ckbox-inline mb-0 mr-4">
                                            <input name="capaian[]" type="checkbox" value="Keterampilan Umum" {{ (isset($data) && (in_array('Keterampilan Umum',$data->capaian))) || old('capaian')=='Keterampilan Umum' ? 'checked' : ''}}>
                                            <span class="pl-0">Keterampilan Umum</span>
                                        </label>
                                        <label class="ckbox ckbox-inline mb-0 mr-4">
                                            <input name="capaian[]" type="checkbox" value="Keterampilan Khusus" {{ (isset($data) && (in_array('Keterampilan Khusus',$data->capaian))) || old('capaian')=='Keterampilan Khusus' ? 'checked' : ''}}>
                                            <span class="pl-0">Keterampilan Khusus</span>
                                        </label>
                                    </div>
                                    <div id="errorsCapaian"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Kompetensi: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <label class="ckbox ckbox-inline mb-0 mr-4">
                                        <input name="kompetensi_prodi" type="checkbox" value="1" {{ isset($data) && isset($data->kompetensi_prodi) || old('kompetensi_prodi')=='1' ? 'checked' : ''}}>
                                        <span class="pl-0">Sesuai Kompetensi Prodi?</span>
                                    </label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Dokumen Rencana: </label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="dokumen_nama" value="{{ isset($data) ? $data->dokumen_nama : old('dokumen_nama')}}" placeholder="Masukkan nama dokumen rencana pembelajaran">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-md-3 form-control-label">Unit Penyelenggara: <span class="tx-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control" name="unit_penyelenggara" required>
                                        <option value="">- Pilih Unit Penyelenggara -</option>
                                        <option value="Program Studi" {{ (isset($data) && $data->unit_penyelenggara == 'Program Studi') || old('unit_penyelenggara') == 'Program Studi' ? 'selected' : '' }}>Program Studi</option>
                                        <option value="Jurusan" {{ (isset($data) && $data->unit_penyelenggara == 'Jurusan') || old('unit_penyelenggara') == 'Jurusan' ? 'selected' : '' }}>Jurusan</option>
                                        <option value="Fakultas" {{ (isset($data) && $data->unit_penyelenggara == 'Fakultas') || old('unit_penyelenggara') == 'Fakultas' ? 'selected' : '' }}>Fakultas</option>
                                        <option value="Universitas" {{ (isset($data) && $data->unit_penyelenggara == 'Universitas') || old('unit_penyelenggara') == 'Universitas' ? 'selected' : '' }}>Universitas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- card-body -->
                <div class="card-footer bd bd-color-gray-lighter rounded-bottom">
                    <div class="row">
                        <div class="col-6 mx-auto">
                            <div class="text-center">
                                <button class="btn btn-info btn-submit">Simpan</button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </div>
                </div><!-- card-footer -->
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
