@extends('layouts.master')

@section('title', 'Tempat Kerja Lulusan - Prodi '.$studyProgram->singkatan)

@section('style')
<link href="{{ asset ('assets/lib') }}/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="{{ asset ('assets/lib') }}/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="br-pageheader">
    <nav class="breadcrumb pd-0 mg-0 tx-12">
        @foreach (Breadcrumbs::generate('alumnus-workplace-show',$studyProgram) as $breadcrumb)
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
        <i class="icon fa fa-building"></i>
        <div>
            <h4>Program Studi: {{$studyProgram->nama}}</h4>
            <p class="mg-b-0">Rincian Tempat Kerja Lulusan</p>
        </div>
    </div>
    @if(!Auth::user()->hasRole('kajur'))
    <div class="ml-auto d-inline-flex">
        <button class="btn btn-teal btn-block mg-y-10 text-white btn-add" data-toggle="modal" data-target="#modal-alumnus-workplace">
            <i class="fa fa-plus mg-r-10"></i> Tambah
        </button>
    </div>
    @endif
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
    <table id="table-alumnusWorkplace" class="table table-colored table-dark" >
        <thead>
            <tr>
                <th class="text-center align-middle" rowspan="2">Tahun Lulus</th>
                <th class="text-center align-middle" rowspan="2">Jumlah Lulusan</th>
                <th class="text-center align-middle" rowspan="2">Jumlah Lulusan<br>Bekerja/Berwirausaha</th>
                <th class="text-center" colspan="3">Jumlah Lulusan yang Bekerja Berdasarkan Tingkat/Tempat Kerja/Berwirausaha</th>
                @if(!Auth::user()->hasRole('kajur'))
                <th class="text-center align-middle" rowspan="2">Aksi</th>
                @endif
            </tr>
            <tr>
                <th class="text-center">Lokal/Wilayah/<br>Berwirausaha tidak Berizin</th>
                <th class="text-center">Nasional/<br>Berwirausaha Berizin</th>
                <th class="text-center">Internasional/<br>Multinasional</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $d)
            <tr>
                <td class="text-center">{{$d->tahun_lulus}}</td>
                <td class="text-center">{{$d->jumlah_lulusan}}</td>
                <td class="text-center">{{$d->lulusan_bekerja}}</td>
                <td class="text-center">{{$d->kerja_lokal}}</td>
                <td class="text-center">{{$d->kerja_nasional}}</td>
                <td class="text-center">{{$d->kerja_internasional}}</td>
                @if(!Auth::user()->hasRole('kajur'))
                <td class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm btn-icon rounded-circle mg-r-5 mg-b-10 btn-edit" data-id="{{encrypt($d->id)}}">
                            <div><i class="fa fa-pencil-alt"></i></div>
                        </button>
                        <form method="POST">
                            <input type="hidden" value="{{encrypt($d->id)}}" name="id">
                            <button type="submit" class="btn btn-danger btn-sm btn-icon rounded-circle mg-r-5 mg-b-10 btn-delete" data-dest="{{ route('alumnus.workplace.delete') }}">
                                <div><i class="fa fa-trash"></i></div>
                            </button>
                        </form>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td class="text-center" colspan="7">BELUM ADA DATA</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if(!Auth::user()->hasRole('kajur'))
@include('alumnus.workplace.form')
@endif
@endsection

@section('js')
<script src="{{asset('assets/lib')}}/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('assets/lib')}}/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('assets/lib')}}/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
@endsection
