<?php

namespace App\Http\Controllers\BK_ROLE;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{

    public function countAlumni(){
        $alumniCount = Alumni::all()->count();
        $alumniCountRPL = Alumni::where('jurusan', 'RPL')->count();
        $alumniCountKKBT = Alumni::where('jurusan', 'KKBT')->count();
        $alumniCountDKV = Alumni::where('jurusan', 'DKV')->count();
        $alumniCountAKL = Alumni::where('jurusan', 'AKL')->count();
        $alumniCountMP = Alumni::where('jurusan', 'MP')->count();
        $alumniCountBD = Alumni::where('jurusan', 'BD')->count();

        return response()->json([
            'ALL' => $alumniCount,
            'RPL' => $alumniCountRPL,
            'KKBT' => $alumniCountKKBT,
            'DKV' => $alumniCountDKV,
            'AKL' => $alumniCountAKL,
            'MP' => $alumniCountMP,
            'BD' => $alumniCountBD
        ], 200);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $csvData = file($file->getRealPath());
        $rows = array_map('str_getcsv', $csvData);
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        unset($rows[0]);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            Alumni::create([
                'nama_lengkap'    => $data['nama_lengkap'] ?? null,
                'nisn'            => $data['nisn'] ?? null,
                'tahun_lulus'     => $data['tahun_lulus'] ?? null,
                'jurusan'         => $data['jurusan'] ?? null,
                'email'           => $data['email'] ?? null,
                'no_hp'           => $data['no_hp'] ?? null,
                'alamat'          => $data['alamat'] ?? null,
                'status_pekerjaan'=> $data['status_pekerjaan'] ?? null,
                'perusahaan_id'   => $data['perusahaan_id'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Data alumni berhasil diimport']);
    }

    public function index(){
        $alumnis = Alumni::with('perusahaan')->get();
        return response()->json($alumnis, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_lengkap'    => 'required|string|max:255',
            'nisn'            => 'required|string|max:50|unique:alumni,nisn',
            'tahun_lulus'     => 'required|integer',
            'jurusan'         => 'required|string|max:100',
            'email'           => 'required|email|unique:alumni,email',
            'no_hp'           => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
            'status_pekerjaan'=> 'nullable|string|max:100',
            'perusahaan_id'   => 'nullable|exists:perusahaan,id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        $alumni = Alumni::create($data);

        return response()->json([
            'message' => 'Berhasil menambah data alumni',
            'data'    => $alumni
        ], 201);
    }

    public function show($id){
        $alumni = Alumni::where('id', $id)->with('perusahaan.user')->first();
        if(!$alumni){
            return response()->json([
                'message' => 'Data alumni tidak ditemukan!'
            ], 401);
        }

        return response()->json($alumni);
    }

    public function unggahProfil(Request $request, $id)
    {
        $alumni = Alumni::findOrFail($id);

        $request->validate([
            'profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('profil')) {
            $fileName = time().'_'.$request->file('profil')->getClientOriginalName();
            $request->file('profil')->move(public_path('uploads/profil'), $fileName);

            $alumni->profil = $fileName;
            $alumni->save();
        }

        return response()->json([
            'message' => 'berhasil unggah foto profil'
        ]);
    }


    public function update(Request $request, $id)
    {
        $alumni = Alumni::find($id);

        if(!$alumni){
            return response()->json([
                'message' => 'Data alumni tidak ditemukan!'
            ], 401);
        }


        $validator = Validator::make($request->all(), [
            'nama_lengkap'    => 'required',
            'nisn'            => 'required',
            'tahun_lulus'     => 'required',
            'jurusan'         => 'required',
            'email'           => 'required',
            'no_hp'           => 'required',
            'alamat'          => 'required',
            'status_pekerjaan'=> 'nullable',
            'perusahaan_id'   => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $alumni->update([
            'nama_lengkap'    => $request->nama_lengkap,
            'nisn'            => $request->nisn,
            'tahun_lulus'     => $request->tahun_lulus,
            'jurusan'         => $request->jurusan,
            'email'           => $request->email,
            'no_hp'           => $request->no_hp,
            'alamat'          => $request->alamat,
            'status_pekerjaan'=> $request->status_pekerjaan,
            'perusahaan_id'   => $request->perusahaan_id,
        ]);

        return response()->json([
            'message' => 'Berhasil mengedit data alumni',
            'data'    => $alumni
        ], 200);
    }

    public function destroy($id){
        $alumni = Alumni::find($id);

        if(!$alumni){
            return response()->json([
                'message' => 'Data alumni tidak ditemukan!'
            ], 401);
        }

        $alumni->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data'
        ]);
    }


}
