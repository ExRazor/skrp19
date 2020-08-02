<?php

namespace App\Http\Controllers;

use App\Exports\TeacherExport;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\StudyProgram;
use App\Models\AcademicYear;
use App\Models\Ewmp;
use App\Models\Faculty;
use App\Models\CurriculumSchedule;
use App\Models\TeacherAchievement;
use App\Models\Research;
use App\Models\CommunityService;
use App\Models\Minithesis;
use App\Models\User;
use App\Imports\TeacherImport;
use App\Models\TeacherPublication;
use App\Models\TeacherStatus;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class TeacherController extends Controller
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
            'delete_user',
            'import'
        ];

        $this->middleware('role:admin,kaprodi', ['only' => $method]);
    }

    public function index()
    {
        $studyProgram = StudyProgram::where('kd_jurusan',setting('app_department_id'))->get();
        $faculty      = Faculty::all();

        if(Auth::user()->hasRole('kaprodi')) {
            $data         = Teacher::whereHas(
                                'latestStatus.studyProgram', function($query) {
                                    $query->where('kd_prodi',Auth::user()->kd_prodi);
                                })
                            ->get();
        } else {
            $data         = Teacher::whereHas(
                                'latestStatus.studyProgram', function($query) {
                                    $query->where('kd_jurusan',setting('app_department_id'));
                                })
                            ->get();
        }

        return view('teacher/index',compact(['studyProgram','faculty','data']));
    }

    public function show($nidn)
    {
        // $nidn = decode_id($nidn);
        $data = Teacher::where('nidn',$nidn)->first();

        if(!isset($data) || (Auth::user()->hasRole('kaprodi') && Auth::user()->kd_prodi != $data->latestStatus->studyProgram->kd_prodi)) {
            return redirect(route('teacher.list.index'));
        }

        $data->bidang_ahli = json_decode($data->bidang_ahli);

        $academicYear   = AcademicYear::orderBy('tahun_akademik','desc')->orderBy('semester','desc')->get();
        $tahun          = AcademicYear::where('semester','Ganjil')->orderBy('tahun_akademik','desc')->get();
        $studyProgram   = StudyProgram::where('kd_jurusan',setting('app_department_id'))->get();
        $status         = TeacherStatus::where('nidn',$data->nidn)->get();
        $schedule       = CurriculumSchedule::where('nidn',$data->nidn)->orderBy('kd_matkul','asc')->get();
        $minithesis     = Minithesis::where('pembimbing_utama',$data->nidn)->orWhere('pembimbing_pendamping',$data->nidn)->orderBy('id_ta','desc')->get();
        $ewmp           = Ewmp::where('nidn',$data->nidn)->orderBy('id_ta','desc')->get();
        $achievement    = TeacherAchievement::where('nidn',$data->nidn)->orderBy('id_ta','desc')->get();

        $research       = Research::with([
                                        'researchTeacher' => function($q1) use ($data) {
                                            $q1->where('nidn',$data->nidn);
                                        }
                                    ])
                                    ->whereHas(
                                        'researchTeacher', function($q1) use ($data) {
                                            $q1->where('nidn',$data->nidn);
                                        }
                                    )
                                    ->orderBy('id_ta','desc')
                                    ->get();

        $service        = CommunityService::with([
                                                'serviceTeacher' => function($q1) use ($data) {
                                                    $q1->where('nidn',$data->nidn);
                                                }
                                            ])
                                            ->whereHas(
                                                'serviceTeacher', function($q1) use ($data) {
                                                    $q1->where('nidn',$data->nidn);
                                                }
                                            )
                                            ->orderBy('id_ta','desc')
                                            ->get();

        $publication    = TeacherPublication::whereHas(
                                                'teacher', function($q1) use ($data) {
                                                    $q1->where('nidn',$data->nidn);
                                                }
                                            )
                                            ->orderBy('id_ta','desc')
                                            ->get();

        return view('teacher/profile',compact(['data','academicYear','tahun','studyProgram','status','schedule','ewmp','achievement','minithesis','research','service','publication']));
    }

    public function create()
    {
        $faculty = Faculty::all();
        $studyProgram = StudyProgram::where('kd_jurusan',setting('app_department_id'))->get();

        return view('teacher/form',compact(['faculty','studyProgram']));
    }

    public function edit($nidn)
    {
        // $nidn          = decode_id($nidn);
        $data         = Teacher::where('nidn',$nidn)->first();
        $faculty      = Faculty::all();
        $studyProgram = StudyProgram::where('kd_jurusan',$data->latestStatus->studyProgram->kd_jurusan)->get();

        $bidang = json_decode($data->bidang_ahli);
        $data->bidang_ahli   = implode(', ',$bidang);

        return view('teacher/form',compact(['data','faculty','studyProgram']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nidn'                  => 'required|numeric|min:8',
            // 'kd_prodi'              => 'required',
            'nip'                   => 'nullable|numeric|digits:18',
            'nama'                  => 'required',
            'jk'                    => 'required',
            'agama'                 => 'nullable',
            'tpt_lhr'               => 'nullable',
            'tgl_lhr'               => 'nullable',
            'email'                 => 'email|nullable',
            'pend_terakhir_jenjang' => 'nullable',
            'pend_terakhir_jurusan' => 'nullable',
            'bidang_ahli'           => 'nullable',
            'sesuai_bidang_ps'      => 'nullable',
            'ikatan_kerja'          => 'required',
            'jabatan_akademik'      => 'required',
            'foto'                  => 'mimes:jpeg,jpg,png',
        ]);

        $bidang_ahli = explode(", ",$request->bidang_ahli);

        $Teacher                            = new Teacher;
        $Teacher->nidn                      = $request->nidn;
        // $Teacher->kd_prodi                  = $request->kd_prodi;
        $Teacher->nip                       = $request->nip;
        $Teacher->nama                      = $request->nama;
        $Teacher->jk                        = $request->jk;
        $Teacher->agama                     = $request->agama;
        $Teacher->tpt_lhr                   = $request->tpt_lhr;
        $Teacher->tgl_lhr                   = $request->tgl_lhr;
        $Teacher->alamat                    = $request->alamat;
        $Teacher->no_telp                   = $request->no_telp;
        $Teacher->email                     = $request->email;
        $Teacher->pend_terakhir_jenjang     = $request->pend_terakhir_jenjang;
        $Teacher->pend_terakhir_jurusan     = $request->pend_terakhir_jurusan;
        $Teacher->bidang_ahli               = json_encode($bidang_ahli);
        $Teacher->ikatan_kerja              = $request->ikatan_kerja;
        $Teacher->jabatan_akademik          = $request->jabatan_akademik;
        $Teacher->sertifikat_pendidik       = $request->sertifikat_pendidik;
        $Teacher->sesuai_bidang_ps          = $request->sesuai_bidang_ps;

        if($request->file('foto')) {
            $file = $request->file('foto');
            $tujuan_upload = public_path('upload/teacher');
            $filename = $request->nidn.'_'.str_replace(' ', '', $request->nama).'.'.$file->getClientOriginalExtension();
            $file->move($tujuan_upload,$filename);
            $Teacher->foto = $filename;
        }

        $Teacher->save();

        //Buat User Dosen
        $user               = new User;
        $user->username     = $Teacher->nidn;
        $user->password     = Hash::make($Teacher->nidn);
        $user->role         = 'dosen';
        $user->defaultPass  = 1;
        $user->name         = $Teacher->nama;
        $user->foto         = $Teacher->foto;
        $user->save();

        return redirect()->route('teacher.list.index')->with('flash.message', 'Data berhasil ditambahkan!')->with('flash.class', 'success');
    }

    public function update(Request $request)
    {
        $id  = decrypt($request->_id);

        $request->validate([
            // 'kd_prodi'              => 'required',
            'nip'                   => 'nullable|numeric|digits:18',
            'nama'                  => 'required',
            'jk'                    => 'required',
            'agama'                 => 'nullable',
            'tpt_lhr'               => 'nullable',
            'tgl_lhr'               => 'nullable',
            'email'                 => 'email|nullable',
            'pend_terakhir_jenjang' => 'nullable',
            'pend_terakhir_jurusan' => 'nullable',
            'bidang_ahli'           => 'nullable',
            'sesuai_bidang_ps'      => 'nullable',
            'ikatan_kerja'          => 'required',
            'jabatan_akademik'      => 'required',
            'foto'                  => 'mimes:jpeg,jpg,png',
        ]);

        $bidang_ahli = explode(", ",$request->bidang_ahli);

        $Teacher                            = Teacher::find($id);
        // $Teacher->kd_prodi                  = $request->kd_prodi;
        $Teacher->nip                       = $request->nip;
        $Teacher->nama                      = $request->nama;
        $Teacher->jk                        = $request->jk;
        $Teacher->agama                     = $request->agama;
        $Teacher->tpt_lhr                   = $request->tpt_lhr;
        $Teacher->tgl_lhr                   = $request->tgl_lhr;
        $Teacher->alamat                    = $request->alamat;
        $Teacher->no_telp                   = $request->no_telp;
        $Teacher->email                     = $request->email;
        $Teacher->pend_terakhir_jenjang     = $request->pend_terakhir_jenjang;
        $Teacher->pend_terakhir_jurusan     = $request->pend_terakhir_jurusan;
        $Teacher->bidang_ahli               = json_encode($bidang_ahli);
        $Teacher->ikatan_kerja              = $request->ikatan_kerja;
        $Teacher->jabatan_akademik          = $request->jabatan_akademik;
        $Teacher->sertifikat_pendidik       = $request->sertifikat_pendidik;
        $Teacher->sesuai_bidang_ps          = $request->sesuai_bidang_ps;

        $storagePath = public_path('upload/teacher/'.$Teacher->foto);
        if($request->file('foto')) {
            if(File::exists($storagePath)) {
                File::delete($storagePath);
            }

            $file = $request->file('foto');
            $tujuan_upload = public_path('upload/teacher');
            $filename = $Teacher->nidn.'_'.str_replace(' ', '', $Teacher->nama).'.'.$file->getClientOriginalExtension();
            $file->move($tujuan_upload,$filename);
            $Teacher->foto = $filename;
        }

        if(isset($Teacher->foto) && File::exists($storagePath))
        {
            $ekstensi = File::extension($storagePath);
            $filename = $request->nidn.'_'.str_replace(' ', '', $request->nama).'.'.$ekstensi;
            File::move($storagePath,public_path('upload/teacher/'.$filename));
            $Teacher->foto = $filename;
        }

        $Teacher->save();

        //Update User Dosen
        $user          = User::where('username',$id)->first();
        $user->name    = $Teacher->nama;
        $user->foto    = $Teacher->foto;
        $user->save();

        return redirect()->route('teacher.list.show',$Teacher->nidn)->with('flash.message', 'Data berhasil disunting!')->with('flash.class', 'success');
    }

    public function destroy(Request $request)
    {
        if(!request()->ajax()) {
            return redirect()->route('teacher');
        }

        $id     = decode_id($request->id);
        $data   = Teacher::find($id);
        $q      = $data->delete();
        if(!$q) {
            return response()->json([
                'title'   => 'Gagal',
                'message' => 'Terjadi kesalahan saat menghapus',
                'type'    => 'error'
            ]);
        } else {
            $this->delete_file($data->foto);
            $this->delete_user($id);
            return response()->json([
                'title'   => 'Berhasil',
                'message' => 'Data berhasil dihapus',
                'type'    => 'success'
            ]);
        }
    }

    public function download($filename)
    {
        $file = decrypt($filename);

        $storagePath = public_path('upload/teacher/'.$file);
        if( ! File::exists($storagePath)) {
            abort(404);
        } else {
            $mimeType = File::mimeType($storagePath);
            $headers = array(
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.$file.'"'
            );

            return response(file_get_contents($storagePath), 200, $headers);
        }
    }

    public function delete_file($file)
    {
        $storagePath = public_path('upload/teacher/'.$file);
        if(File::exists($storagePath)) {
            File::delete($storagePath);
        }

    }

    public function delete_user($id)
    {
        $cek = User::where('username',$id)->count();

        if($cek) {
            User::where('username', $id)->delete();
        }
    }

    public function import(Request $request)
	{
		// Memvalidasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);

		// Menangkap file excel
		$file = $request->file('file');

        // Mengambil nama file
        $tgl_upload = date('d-m-Y');
        $nama_file = $file->getClientOriginalName();

        // upload ke folder khusus di dalam folder public
        $path = public_path('upload/teacher/excel_import/',$nama_file);
		$file->move($path);

		// import data
        $q = Excel::import(new TeacherImport, public_path('/upload/teacher/excel_import/'.$nama_file));

        //Validasi jika terjadi error saat mengimpor
        if(!$q) {
            return response()->json([
                'title'   => 'Gagal',
                'message' => 'Terjadi kesalahan saat mengimpor',
                'type'    => 'error'
            ]);
        } else {
            File::delete(public_path('/upload/teacher/excel_import/'.$nama_file));
            return response()->json([
                'title'   => 'Berhasil',
                'message' => 'Data berhasil diimpor',
                'type'    => 'success'
            ]);
        }
    }

    public function export(Request $request)
	{
		// Request
        $tgl    = date('d-m-Y_h_i_s');
        $prodi  = ($request->kd_prodi ? '_'.$request->kd_prodi.'_' : null);
        $nama_file = 'Data Dosen_'.$prodi.$tgl.'.xlsx';

		// Ekspor data
        // return (new TeacherExport($request))->download($nama_file);
        return (new TeacherExport($request))->store($nama_file,'upload');

    }

    public function show_by_prodi(Request $request)
    {
        $data = Teacher::where('kd_prodi',$request->kd_prodi)->get();

        return response()->json($data);
    }

    public function get_by_department(Request $request)
    {
        if($request->ajax()) {

            $kd = $request->input('kd_jurusan');

            if($kd == 0){
                $data = Teacher::with(['latestStatus.studyProgram.department.faculty'])->orderBy('created_at','desc')->get();
            } else {
                $data = Teacher::whereHas(
                            'latestStatus.studyProgram', function($query) use ($kd) {
                                $query->where('kd_jurusan',$kd);
                            })
                        ->with(['latestStatus.studyProgram.department.faculty'])
                        ->get();
            }

            return response()->json($data);
        } else {
            abort(404);
        }
    }

    public function get_by_studyProgram(Request $request)
    {
        if($request->ajax()) {

            $q = Teacher::where('kd_prodi',$request->kd_prodi);

            if($request->prodi) {
                $q->where('kd_prodi',$request->prodi);
            }

            if($request->cari) {
                $q->where(function($query) use ($request) {
                    $query->where('nidn', 'LIKE', '%'.$request->cari.'%')->orWhere('nama', 'LIKE', '%'.$request->cari.'%');
                });
            }

            $data = $q->get();

            $response = array();
            foreach($data as $d){
                $response[] = array(
                    "id"    => $d->nidn,
                    "text"  => $d->nama.' ('.$d->nidn.')'
                );
            }
            return response()->json($response);
        } else {
            abort(404);
        }
    }

    public function datatable(Request $request)
    {
        if(!$request->ajax()) {
            abort(404);
        }

        if(Auth::user()->hasRole('kaprodi')) {
            $data         = Teacher::whereHas(
                                'latestStatus.studyProgram', function($query) {
                                    $query->where('kd_prodi',Auth::user()->kd_prodi);
                            });
        } else {
            $data         = Teacher::whereHas(
                                'latestStatus.studyProgram', function($query) {
                                    $query->where('kd_jurusan',setting('app_department_id'));
                            });
        }

        // dd($data->get());

        if($request->prodi) {
            $data->whereHas('latestStatus.studyProgram',function($q) use($request) {
                $q->where('kd_prodi',$request->prodi);
            });
        }

        return DataTables::of($data->get())
                            ->editColumn('nama', function($d) {
                                return '<a name="'.$d->nama.'" href="'.route("teacher.list.show",$d->nidn).'">'.
                                            $d->nama.
                                        '<br><small>NIDN. '.$d->nidn.'</small></a>';
                            })
                            ->editColumn('study_program', function($d){
                                return  $d->latestStatus->studyProgram->nama.
                                        '<br>
                                        <small>'.$d->latestStatus->studyProgram->department->faculty->singkatan.' - '.$d->latestStatus->studyProgram->department->nama.'</small>';
                            })
                            ->addColumn('aksi', function($d) {
                                if(!Auth::user()->hasRole('kajur')) {
                                    return view('teacher.table-button', compact('d'))->render();
                                }
                            })
                            ->rawColumns(['nama','study_program','aksi'])
                            ->make();
    }

    public function loadData(Request $request)
    {
        if(!$request->ajax()) {
            abort(404);
        }

        $cari  = $request->cari;
        $prodi = $request->prodi;

        $q = Teacher::select('nidn','nama');

        if($prodi) {
            $q->whereHas('latestStatus', function($q) use($prodi) {
                $q->where('kd_prodi',$prodi);
            });
        }

        if($cari) {
            $q->where(function($query) use ($request) {
                $query->where('nidn', 'LIKE', '%'.$request->cari.'%')->orWhere('nama', 'LIKE', '%'.$request->cari.'%');
            });
        }

        $data = $q->orderBy('nama','asc')->get();

        $response = array();
        foreach($data as $d){
            $response[] = array(
                "id"    => $d->nidn,
                "text"  => $d->nama.' ('.$d->nidn.')'
            );
        }
        return response()->json($response);
    }
}
