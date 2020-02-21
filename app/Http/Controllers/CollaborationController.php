<?php

namespace App\Http\Controllers;

use App\Collaboration;
use App\StudyProgram;
use App\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class CollaborationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data = Collaboration::all();

        $studyProgram = StudyProgram::where('kd_jurusan',setting('app_department_id'))->get();

        foreach($studyProgram as $sp) {
            $collab[$sp->kd_prodi] = Collaboration::where('kd_prodi',$sp->kd_prodi)->get();
        }

        if(Auth::user()->hasRole('kaprodi'))
        {
            $collab = Collaboration::whereHas(
                                        'studyProgram', function($query) {
                                            $query->where('kd_prodi',Auth::user()->kd_prodi);
                                        }
                                    )
                                    ->get();
        }
        else
        {
            $collab = Collaboration::whereHas(
                                        'studyProgram', function($query) {
                                            $query->where('kd_jurusan',setting('app_department_id'));
                                        }
                                    )
                                    ->get();
        }



        return view('collaboration/index',compact(['studyProgram','collab']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->hasRole('admin','kaprodi')) {
            return redirect(route('collaboration'));
        }

        $academicYear = AcademicYear::all();
        $studyProgram = StudyProgram::where('kd_jurusan',setting('app_department_id'))->get();
        return view('collaboration/form',compact(['academicYear','studyProgram']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'kd_prodi'          => 'required',
            'id_ta'             => 'required',
            'jenis'             => 'required',
            'nama_lembaga'      => 'required',
            'tingkat'           => 'required',
            'judul_kegiatan'    => 'required',
            'manfaat_kegiatan'  => 'required',
            'waktu'             => 'required',
            'durasi'            => 'required',
            'bukti_nama'        => 'required',
            'bukti_file'        => 'required|mimes:pdf',
        ]);

        $collaboration = new Collaboration;
        $collaboration->kd_prodi         = $request->kd_prodi;
        $collaboration->id_ta            = $request->id_ta;
        $collaboration->jenis            = $request->jenis;
        $collaboration->nama_lembaga     = $request->nama_lembaga;
        $collaboration->tingkat          = $request->tingkat;
        $collaboration->judul_kegiatan   = $request->judul_kegiatan;
        $collaboration->manfaat_kegiatan = $request->manfaat_kegiatan;
        $collaboration->waktu            = $request->waktu;
        $collaboration->durasi           = $request->durasi;
        $collaboration->bukti_nama       = $request->bukti_nama;

        if($request->file('bukti_file')) {
            $file = $request->file('bukti_file');
            $tgl_skrg = date('Y_m_d_H_i_s');
            $tujuan_upload = 'upload/collaboration';
            $filename = $request->nama_lembaga.'_'.$request->tingkat.'_'.$request->judul_kegiatan.'_'.$tgl_skrg.'.'.$file->getClientOriginalExtension();
            $file->move($tujuan_upload,$filename);
            $collaboration->bukti_file = $filename;
        }

        $collaboration->save();


        return redirect()->route('collaboration')->with('flash.message', 'Data berhasil ditambahkan!')->with('flash.class', 'success');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Collaboration  $collaboration
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decode_id($id);
        $data = Collaboration::find($id);

        $academicYear = AcademicYear::all();
        $studyProgram = StudyProgram::all();
        return view('collaboration/form',compact(['academicYear','studyProgram','data']));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Collaboration  $collaboration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = decrypt($request->id);

        $request->validate([
            'kd_prodi'          => 'required',
            'id_ta'             => 'required',
            'jenis'             => 'required',
            'nama_lembaga'      => 'required',
            'tingkat'           => 'required',
            'judul_kegiatan'    => 'required',
            'manfaat_kegiatan'  => 'required',
            'waktu'             => 'required',
            'durasi'            => 'required',
            'bukti_nama'        => 'required',
            'bukti_file'        => 'mimes:pdf',
        ]);

        $collaboration = Collaboration::find($id);
        $collaboration->kd_prodi         = $request->kd_prodi;
        $collaboration->id_ta            = $request->id_ta;
        $collaboration->jenis            = $request->jenis;
        $collaboration->nama_lembaga     = $request->nama_lembaga;
        $collaboration->tingkat          = $request->tingkat;
        $collaboration->judul_kegiatan   = $request->judul_kegiatan;
        $collaboration->manfaat_kegiatan = $request->manfaat_kegiatan;
        $collaboration->waktu            = $request->waktu;
        $collaboration->durasi           = $request->durasi;
        $collaboration->bukti_nama       = $request->bukti_nama;

        //Upload File
        $storagePath = 'upload/collaboration/'.$collaboration->bukti_file;
        if($request->file('bukti_file')) {
            File::delete($storagePath);

            $file = $request->file('bukti_file');
            $tgl_skrg = date('Y_m_d_H_i_s');
            $tujuan_upload = 'upload/collaboration';
            $filename = $request->nama_lembaga.'_'.$request->tingkat.'_'.$request->judul_kegiatan.'_'.$request->waktu.'_'.$tgl_skrg.'.'.$file->getClientOriginalExtension();
            $file->move($tujuan_upload,$filename);
            $collaboration->bukti_file = $filename;
        }

        $collaboration->save();

        return redirect()->route('collaboration')->with('flash.message', 'Data berhasil disunting!')->with('flash.class', 'success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Collaboration  $collaboration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(request()->ajax()){
            $id = decode_id($request->id);

            $q = Collaboration::destroy($id);
            if(!$q) {
                return response()->json([
                    'title'   => 'Gagal',
                    'message' => 'Terjadi kesalahan saat menghapus',
                    'type'    => 'error'
                ]);
            } else {
                $this->delete_file($id);
                return response()->json([
                    'title'   => 'Berhasil',
                    'message' => 'Data berhasil dihapus',
                    'type'    => 'success'
                ]);
            }
        } else {
            return redirect()->route('collaboration');
        }
    }

    public function download($filename)
    {
        $file = decode_id($filename);
        $storagePath = 'upload/collaboration/'.$file;
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

    public function delete_file($id)
    {
        $data = Collaboration::find($id);

        $storagePath = 'upload/collaboration/'.$data->bukti_file;
        if(File::exists($storagePath)) {
            File::delete($storagePath);
        }
    }

    public function get_by_filter(Request $request)
    {
        if($request->ajax()) {

            $q   = Collaboration::with('studyProgram','academicYear')
                            ->whereHas(
                                'studyProgram', function($query) {
                                    $query->where('kd_jurusan',setting('app_department_id'));
                                }
                            );

            if($request->kd_prodi){
                $q->whereHas(
                    'studyProgram', function($query) use ($request) {
                        $query->where('kd_prodi',$request->kd_prodi);
                });
            }

            $data = $q->orderBy('waktu','desc')->get();

            return response()->json($data);
        } else {
            abort(404);
        }
    }
}
