@extends('layouts.master')

@section('title', 'Kepuasan Akademik')

@section('style')
<link href="{{ asset ('assets/lib') }}/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="{{ asset ('assets/lib') }}/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="br-pageheader">
    <nav class="breadcrumb pd-0 mg-0 tx-12">
        @foreach (Breadcrumbs::generate('academic-satisfaction') as $breadcrumb)
            @if($breadcrumb->url && !$loop->last)
                <a class="breadcrumb-item" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
            @else
                <span class="breadcrumb-item">{{ $breadcrumb->title }}</span>
            @endif
        @endforeach
    </nav>
</div>
<div class="br-pagetitle">
    <i class="icon fa fa-percentage"></i>
    <div>
        <h4>Kepuasan Akademik</h4>
        <p class="mg-b-0">Olah Data Kepuasan Akademik</p>
    </div>
    <div class="ml-auto">
        <a href="{{ route('academic.satisfaction.add') }}" class="btn btn-teal btn-block mg-b-10" style="color:white"><i class="fa fa-plus mg-r-10"></i> Tambah Data</a>
    </div>
</div>

<div class="br-pagebody">
    @if (session()->has('flash.message'))
        <div class="alert alert-{{ session('flash.class') }}" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('flash.message') }}
        </div>
    @endif
    <div class="widget-2">
        <div class="card shadow-base mb-3">
            <div class="card-header nm_jurusan">
                <h6 class="card-title">{{ setting('app_department_name') }}</h6>
            </div>
            <div class="card-body bd-color-gray-lighter">
                <table id="table_teacher" class="table display responsive nowrap datatable" data-sort="desc">
                    <thead>
                        <tr>
                            <th class="text-center align-middle">Program Studi</th>
                            <th class="text-center align-middle defaultSort">Tahun</th>
                            <th class="text-center">Sangat Baik</th>
                            <th class="text-center">Baik</th>
                            <th class="text-center">Cukup</th>
                            <th class="text-center">Kurang</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($satisfaction as $s)
                        <tr>
                            <td>{{$s->studyProgram->nama}}</td>
                            <td class="text-center">{{$s->academicYear->tahun_akademik}}</td>
                            <td class="text-center">{{$persen[$s->kd_kepuasan]->sangat_baik}}%</td>
                            <td class="text-center">{{$persen[$s->kd_kepuasan]->baik}}%</td>
                            <td class="text-center">{{$persen[$s->kd_kepuasan]->cukup}}%</td>
                            <td class="text-center">{{$persen[$s->kd_kepuasan]->kurang}}%</td>
                            <td class="text-center">
                                <div class="btn-group hidden-xs-down">
                                    <a class="btn btn-success btn-sm btn-icon rounded-circle mg-r-5 mg-b-10" href="{{route('academic.satisfaction.show',encrypt($s->kd_kepuasan))}}">
                                        <div><i class="fa fa-search-plus"></i></div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- card-body -->
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{asset('assets/lib')}}/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('assets/lib')}}/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('assets/lib')}}/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
@endsection
