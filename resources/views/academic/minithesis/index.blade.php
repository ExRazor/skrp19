@extends('layouts.master')

@section('title', 'Tugas Akhir')

@section('content')
<div class="br-pageheader">
    <nav class="breadcrumb pd-0 mg-0 tx-12">
        @foreach (Breadcrumbs::generate('academic-minithesis') as $breadcrumb)
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
        <i class="icon fa fa-journal-whills"></i>
        <div>
            <h4>Tugas Akhir</h4>
            <p class="mg-b-0">Olah Data Tugas Akhir Mahasiswa</p>
        </div>
    </div>
    @if (!Auth::user()->hasRole('kajur'))
    <div class="ml-auto">
        <a href="{{ route('academic.minithesis.create') }}" class="btn btn-teal btn-block mg-b-10" style="color:white"><i class="fa fa-plus mg-r-10"></i> Tugas Akhir</a>
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
    <div class="row">
        @if(!Auth::user()->hasRole('kaprodi'))
        <div class="col-sm-3 col-md-5 col-lg-3 mb-2">
            <select id="prodi_mahasiswa_filter" class="form-control filter-box" >
                <option value="">- Prodi Mahasiswa -</option>
                @foreach($studyProgram as $sp)
                <option value="{{$sp->kd_prodi}}">{{$sp->nama}}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-sm-3 col-md-5 col-lg-3 mb-2">
            <select id="prodi_pembimbing_filter" class="form-control filter-box">
                <option value="">- Prodi Pembimbing Utama -</option>
                @foreach($studyProgram as $sp)
                <option value="{{$sp->kd_prodi}}">{{$sp->nama}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="widget-2">
        <div class="card shadow-base mb-3">
            <div class="card-header nm_jurusan">
                <h6 class="card-title">
                    <span class="nm_jurusan">
                    @if(Auth::user()->hasRole('kaprodi'))
                    {{ Auth::user()->studyProgram->nama }}

                    @else
                    {{ setting('app_department_name') }}
                    @endif
                     </span>
                </h6>
            </div>
            <div class="card-body bd-color-gray-lighter">
                <table id="table_minithesis" class="table display responsive" data-order='[[ 2, "desc" ]]' data-page-length="25" url-target="{{route('ajax.minithesis.datatable')}}">
                    <thead>
                        <tr>
                            <th class="text-center min-mobile-p">Judul Tugas Akhir</th>
                            <th class="text-center min-mobile-p">Nama Mahasiswa</th>
                            <th class="text-center desktop">Tahun Diangkat</th>
                            <th class="text-center none">Pembimbing Utama</th>
                            <th class="text-center none">Pembimbing Pendamping</th>
                            <th class="text-center desktop" width="50">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div><!-- card-body -->
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="{{ asset ('assets/lib') }}/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="{{ asset ('assets/lib') }}/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet">
@endsection

@section('js')
<script src="{{asset('assets/lib')}}/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('assets/lib')}}/datatables.net/js/dataTables.hideEmptyColumns.min.js"></script>
<script src="{{asset('assets/lib')}}/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('assets/lib')}}/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
@endsection

@push('custom-js')
<script type="text/javascript">
    var table = $('#table_minithesis');
    datatable(table);

    $('.filter-box').bind("keyup change", function(){
        table.DataTable().clear().destroy();
        datatable(table);
    });

    function datatable(table_ehm)
    {
        table_ehm.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: table_ehm.attr('url-target'),
                type: "post",
                data: function(d){
                    d.prodi_mahasiswa_filter  = $('#prodi_mahasiswa_filter').val();
                    d.prodi_pembimbing_filter = $('#prodi_pembimbing_filter').val();
                    d._token                  = $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'judul',},
                { data: 'mahasiswa',},
                { data: 'tahun', className: 'text-center'},
                { data: 'pembimbing_utama',},
                { data: 'pembimbing_pendamping',},
                { data: 'aksi', className: 'text-center', orderable:false}
            ],
            hideEmptyCols: [ 5 ],
            autoWidth: false,
            language: {
                url: "/assets/lib/datatables.net/indonesian.json",
            }
        })
    }
</script>
@endpush
