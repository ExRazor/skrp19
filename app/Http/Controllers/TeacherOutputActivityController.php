<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherOutputActivityRequest;
use App\Models\TeacherOutputActivity;
use App\Models\OutputActivityCategory;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TeacherOutputActivityController extends Controller
{
    public function __construct()
    {
        $method = [
            'create',
            'edit',
            'store',
            'update',
            'destroy',
            'delete_file',
            'delete_all_file',
        ];

        $this->middleware('role:admin,kaprodi', ['only' => $method]);
    }

    public function index()
    {
        $studyProgram   = StudyProgram::where('kd_jurusan', setting('app_department_id'))->get();
        $outputActivity = TeacherOutputActivity::all();
        $category       = OutputActivityCategory::all();

        return view('output-activity.teacher.index', compact(['outputActivity', 'category', 'studyProgram']));
    }

    public function show($id)
    {
        $id         = decode_id($id);
        $data       = TeacherOutputActivity::where('id', $id)->first();
        $category   = OutputActivityCategory::all();

        return view('output-activity.teacher.show', compact(['data', 'category']));
    }

    public function create()
    {
        $category   = OutputActivityCategory::all();

        return view('output-activity.teacher.form', compact(['category']));
    }

    public function edit($id)
    {
        $id = decode_id($id);

        $category   = OutputActivityCategory::all();
        $data       = TeacherOutputActivity::find($id);
        return view('output-activity.teacher.form', compact(['category', 'data']));
    }

    public function store(TeacherOutputActivityRequest $request)
    {
        DB::beginTransaction();
        try {
            //Query
            $data                   = new TeacherOutputActivity;
            $data->kegiatan         = $request->kegiatan;
            $data->nm_kegiatan      = $request->nm_kegiatan;
            $data->nidn             = $request->nidn;
            $data->id_kategori      = $request->id_kategori;
            $data->judul_luaran     = $request->judul_luaran;
            $data->id_ta            = $request->id_ta;
            $data->jenis_luaran     = $request->jenis_luaran;
            $data->nama_karya       = $request->nama_karya;
            $data->jenis_karya      = $request->jenis_karya;
            $data->pencipta_karya   = $request->pencipta_karya;
            $data->issn             = $request->issn;
            $data->no_paten         = $request->no_paten;
            $data->tgl_sah          = $request->tgl_sah;
            $data->no_permohonan    = $request->no_permohonan;
            $data->tgl_permohonan   = $request->tgl_permohonan;
            $data->penerbit         = $request->penerbit;
            $data->penyelenggara    = $request->penyelenggara;
            $data->url              = $request->url;
            $data->keterangan       = $request->keterangan;
            $data->save();

            //Upload berkas jika ada
            if ($file = $request->file('file_karya')) {
                $tujuan_upload = storage_path('app/upload/output-activity/teacher');
                $filename = str_replace(' ', '', $request->jenis_luaran) . '_' . $request->nidn . '_' . $request->id_kategori . '_' . str_replace(' ', '', $request->kegiatan) . '_' . $request->thn_luaran . '_' . $data->id . '.' . $file->getClientOriginalExtension();
                $file->move($tujuan_upload, $filename);
                $data->update([
                    'file_karya' => $filename
                ]);
            }

            //Activity Log
            $property = [
                'id'    => $data->id,
                'name'  => $data->judul_luaran . ' (' . $data->teacher->nama . ')',
                'url'   => route('output-activity.teacher.show', encode_id($data->id)),
            ];
            $this->log('created', 'Luaran Dosen', $property);

            DB::commit();
            return redirect()->route('output-activity.teacher.show', encode_id($data->id))->with('flash.message', 'Data berhasil ditambahkan!')->with('flash.class', 'success');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('flash.message', $e->getMessage())->with('flash.class', 'danger')->withInput($request->input());
        }
    }

    public function update(TeacherOutputActivityRequest $request)
    {
        DB::beginTransaction();
        try {
            //Decrypt ID
            $id = decrypt($request->id);

            //Query
            $data                   = TeacherOutputActivity::find($id);
            $data->kegiatan         = $request->kegiatan;
            $data->nm_kegiatan      = $request->nm_kegiatan;
            $data->nidn             = $request->nidn;
            $data->id_kategori      = $request->id_kategori;
            $data->judul_luaran     = $request->judul_luaran;
            $data->id_ta            = $request->id_ta;
            $data->jenis_luaran     = $request->jenis_luaran;
            $data->nama_karya       = $request->nama_karya;
            $data->jenis_karya      = $request->jenis_karya;
            $data->pencipta_karya   = $request->pencipta_karya;
            $data->issn             = $request->issn;
            $data->no_paten         = $request->no_paten;
            $data->tgl_sah          = $request->tgl_sah;
            $data->no_permohonan    = $request->no_permohonan;
            $data->tgl_permohonan   = $request->tgl_permohonan;
            $data->penerbit         = $request->penerbit;
            $data->penyelenggara    = $request->penyelenggara;
            $data->url              = $request->url;
            $data->keterangan       = $request->keterangan;
            $data->save();

            //Berkas
            $storagePath = storage_path('app/upload/output-activity/teacher' . $data->file_karya);
            if ($file = $request->file('file_karya')) {
                if (File::exists($storagePath)) {
                    File::delete($storagePath);
                }

                $tujuan_upload = storage_path('app/upload/output-activity/teacher');
                $filename = str_replace(' ', '', $request->jenis_luaran) . '_' . $request->nidn . '_' . $request->id_kategori . '_' . str_replace(' ', '', $request->kegiatan) . '_' . $request->thn_luaran . '_' . $data->id . '.' . $file->getClientOriginalExtension();
                $file->move($tujuan_upload, $filename);
                $data->update([
                    'file_karya' => $filename
                ]);
            }

            if (isset($data->file_karya) && File::exists($storagePath)) {
                $ekstensi = File::extension($storagePath);
                $filename = str_replace(' ', '', $request->jenis_luaran) . '_' . $request->nidn . '_' . $request->id_kategori . '_' . str_replace(' ', '', $request->kegiatan) . '_' . $request->thn_luaran . '_' . $data->id . '.' . $ekstensi;
                File::move($storagePath, storage_path('app/upload/output-activity/teacher/' . $filename));
                $data->update([
                    'file_karya' => $filename
                ]);
            }

            //Activity Log
            $property = [
                'id'    => $data->id,
                'name'  => $data->judul_luaran . ' (' . $data->teacher->nama . ')',
                'url'   => route('output-activity.teacher.show', encode_id($data->id)),
            ];
            $this->log('updated', 'Luaran Dosen', $property);

            DB::commit();
            return redirect()->route('output-activity.teacher.show', encode_id($data->id))->with('flash.message', 'Data berhasil disunting!')->with('flash.class', 'success');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('flash.message', $e->getMessage())->with('flash.class', 'danger')->withInput($request->input());
        }
    }

    public function destroy(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            //Decrypt ID
            $id     = decode_id($request->id);

            //Query
            $data   = TeacherOutputActivity::find($id);
            $data->delete();

            //Hapus berkas
            $this->delete_all_file($data->file_karya);

            //Activity Log
            $property = [
                'id'    => $data->id,
                'name'  => $data->judul_luaran . ' (' . $data->teacher->nama . ')',
            ];
            $this->log('deleted', 'Luaran Dosen', $property);

            DB::commit();
            return response()->json([
                'title'   => 'Berhasil',
                'message' => 'Data berhasil dihapus',
                'type'    => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function download(Request $request)
    {
        $id   = decrypt($request->id);

        $data = TeacherOutputActivity::find($id);

        $storagePath = storage_path('app/upload/output-activity/teacher/' . $data->file_karya);
        if (!File::exists($storagePath)) {
            abort(404);
        } else {
            $mimeType = File::mimeType($storagePath);
            $headers = array(
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $data->file_karya . '"'
            );

            return response(file_get_contents($storagePath), 200, $headers);
        }
    }

    public function delete_file(Request $request)
    {
        $id   = decrypt($request->id);
        $data = TeacherOutputActivity::find($id);

        if (request()->ajax()) {

            $storagePath = storage_path('app/upload/output-activity/teacher/' . $data->file_karya);
            if (File::exists($storagePath)) {
                $delete = File::delete($storagePath);

                if ($delete) {
                    $data->file_karya = null;
                    $q = $data->save();
                }
            }

            if (!$q) {
                return response()->json([
                    'title'   => 'Gagal',
                    'message' => 'Terjadi kesalahan saat menghapus fail',
                    'type'    => 'error'
                ]);
            } else {
                return response()->json([
                    'title'   => 'Berhasil',
                    'message' => 'Fail berhasil dihapus',
                    'type'    => 'success'
                ]);
            }
        } else {
            return redirect()->route('output-activity.teacher.show', encode_id($data->id));
        }
    }

    public function delete_all_file($file)
    {
        $storage = storage_path('app/upload/output-activity/teacher/' . $file);
        if (File::exists($storage)) {
            File::delete($storage);
        }
    }

    public function datatable(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $data = TeacherOutputActivity::whereHas(
            'teacher.latestStatus.studyProgram',
            function ($query) {
                if (Auth::user()->hasRole('kaprodi')) {
                    $query->where('kd_prodi', Auth::user()->kd_prodi);
                } else {
                    $query->where('kd_jurusan', setting('app_department_id'));
                }
            }
        );

        if ($request->kd_prodi_filter) {
            $data->whereHas(
                'teacher.latestStatus.studyProgram',
                function ($q) use ($request) {
                    $q->where('kd_prodi', $request->kd_prodi_filter);
                }
            );
        }

        return DataTables::of($data->get())
            ->addColumn('judul', function ($d) {
                return  '<a href="' . route('output-activity.student.show', encode_id($d->id)) . '" target="_blank">'
                    . $d->judul_luaran .
                    '</a>';
            })
            ->addColumn('milik', function ($d) {
                return  '<a href="' . route('teacher.list.show', $d->teacher->nidn) . '#publication">'
                    . $d->teacher->nama .
                    '<br><small>NIDN.' . $d->teacher->nidn . ' / ' . $d->teacher->latestStatus->studyProgram->singkatan . '</small>
                                        </a>';
            })
            ->addColumn('kategori', function ($d) {
                return  $d->outputActivityCategory->nama;
            })
            ->addColumn('thn_luaran', function ($d) {
                return  $d->academicYear->tahun_akademik . ' - ' . $d->academicYear->semester;
            })
            ->addColumn('aksi', function ($d) {
                if (!Auth::user()->hasRole('kajur')) {
                    return view('output-activity.teacher.table-button', compact('d'))->render();
                }
            })
            ->rawColumns(['judul', 'milik', 'aksi'])
            ->make();
    }
}
